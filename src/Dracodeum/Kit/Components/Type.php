<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components;

use Dracodeum\Kit\Component;
use Dracodeum\Kit\Components\Type\{
	Components,
	Exceptions
};
use Dracodeum\Kit\Components\Type\Enumerations\Context as EContext;
use Dracodeum\Kit\Prototypes\{
	Type as Prototype,
	Types as Prototypes
};
use Dracodeum\Kit\Prototypes\Type\Interfaces as PrototypeInterfaces;
use Dracodeum\Kit\Traits\LazyProperties\Property;
use Dracodeum\Kit\Primitives\{
	Text,
	Error
};
use Dracodeum\Kit\Interfaces\Stringable as IStringable;
use Stringable as IPhpStringable;
use Dracodeum\Kit\Utilities\Call as UCall;

/**
 * This component represents a type which validates and normalizes values.
 * 
 * If a prototype is given as a name prefixed with a question mark character (<samp>?</samp>), 
 * then that character is stripped from the given name and the type is set as nullable.
 * 
 * @property-read bool $nullable [default = false]
 * <p>Allow a <code>null</code> value.</p>
 * @property-write coercible:component(Dracodeum\Kit\Components\Type\Components\Mutator)[] $mutators [writeonce] [transient] [default = []]
 * <p>The mutators to add, as any combination of the following:<br>
 * &nbsp; &#8226; &nbsp; instances, classes or names;<br>
 * &nbsp; &#8226; &nbsp; <samp>class => properties</samp> or <samp>name => properties</samp> pairs.</p>
 * 
 * @see \Dracodeum\Kit\Prototypes\Type
 * @see \Dracodeum\Kit\Prototypes\Types\Boolean
 * [prototype, name = 'boolean' or 'bool']
 * @see \Dracodeum\Kit\Prototypes\Types\TString
 * [prototype, name = 'string' or 'ustring']
 */
class Type extends Component
{
	//Private properties
	private bool $nullable = false;
	
	/** @var \Dracodeum\Kit\Components\Type\Components\Mutator[] */
	private array $mutators = [];
	
	
	
	//Implemented public static methods
	/** {@inheritdoc} */
	public static function getPrototypeBaseClass(): string
	{
		return Prototype::class;
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Component\Traits\PreInitializer)
	/** {@inheritdoc} */
	protected function preInitialize(&$prototype, array &$properties): void
	{
		if (is_string($prototype) && isset($prototype[0]) && $prototype[0] === '?') {
			$prototype = substr($prototype, 1);
			$properties['nullable'] = true;
		}
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Component\Traits\PropertyBuilder)
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		return match ($name) {
			'nullable' => $this->createProperty()->setMode('r+')->setAsBoolean()->bind(self::class),
			'mutators' => $this->createProperty()
				->setMode('w--')
				->setAsArray(function (&$key, &$value): bool {
					if (is_string($key) && is_array($value)) {
						$this->addMutator($key, $value);
						return true;
					} elseif (is_int($key) && (is_string($value) || is_object($value))) {
						$this->addMutator($value);
						return true;
					}
					return false;
				}),
			default => null
		};
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Component\Traits\PrototypeProducer)
	/** {@inheritdoc} */
	protected function producePrototype(string $name, array $properties)
	{
		return match ($name) {
			'boolean', 'bool' => Prototypes\Boolean::class,
			'string' => Prototypes\TString::class,
			'ustring' => new Prototypes\TString(['unicode' => true] + $properties),
			default => null
		};
	}
	
	
	
	//Final public methods
	/**
	 * Process a given value.
	 * 
	 * @param mixed $value [reference]
	 * <p>The value to process.</p>
	 * @param coercible:enum:value(Dracodeum\Kit\Components\Type\Enumerations\Context) $context [default = INTERNAL]
	 * <p>The context to process for.</p>
	 * @return \Dracodeum\Kit\Primitives\Error|null
	 * <p>An error instance if the given value failed to be processed or <code>null</code> if otherwise.</p>
	 */
	final public function process(mixed &$value, $context = EContext::INTERNAL): ?Error
	{
		//initialize
		$v = $value;
		$context = EContext::coerceValue($context);
		$prototype = $this->getPrototype();
		
		//nullable
		if ($v === null && $this->nullable) {
			return null;
		}
		
		//process
		$error = $prototype->process($v, $context);
		if ($error !== null) {
			$error_text = $error->getText() ?? Text::build();
			if (!$error_text->hasString()) {
				$error->setText($error_text->setString("The given value is invalid.")->setAsLocalized(self::class));
			}
			return $error;
		}
		
		//mutators
		foreach ($this->mutators as $mutator) {
			$error = $mutator->process($v);
			if ($error !== null) {
				return $error;
			}
		}
		
		//finalize
		$value = $v;
		
		//return
		return null;
	}
	
	/**
	 * Textify a given value.
	 * 
	 * @param mixed $value
	 * <p>The value to textify.</p>
	 * @param coercible:enum:value(Dracodeum\Kit\Components\Type\Enumerations\Context) $context [default = INTERNAL]
	 * <p>The context to textify for.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Components\Type\Exceptions\TextificationFailed
	 * @return \Dracodeum\Kit\Primitives\Text|null
	 * <p>The given value textified, as a text instance.<br>
	 * If <var>$no_throw</var> is set to boolean <code>true</code>, 
	 * then <code>null</code> is returned if the given value failed to be textified.</p>
	 */
	final public function textify(mixed $value, $context = EContext::INTERNAL, bool $no_throw = false): ?Text
	{
		//initialize
		$v = $value;
		$prototype = $this->getPrototype();
		$has_textifier = $prototype instanceof PrototypeInterfaces\Textifier;
		
		//process
		$error = $this->process($v, $context);
		if ($error !== null) {
			if ($no_throw) {
				return null;
			}
			throw new Exceptions\TextificationFailed([$this, $prototype, $value, $context, 'error' => $error]);
		}
		
		//null
		if ($v === null && ($this->nullable || !$has_textifier)) {
			return Text::build("null")->setAsLocalized(self::class);
		}
		
		//textify
		if ($has_textifier) {
			return UCall::guardExecution([$prototype, 'textify'], [$v], [Text::class, 'coerce']);
		} elseif ($v instanceof IStringable) {
			return Text::build($v->toString());
		} elseif (is_scalar($v) || $v instanceof IPhpStringable) {
			return Text::build((string)$v);
		} elseif ($no_throw) {
			return null;
		}
		throw new Exceptions\TextificationFailed([$this, $prototype, $value, $context]);
	}
	
	/**
	 * Get label.
	 * 
	 * @param coercible:enum:value(Dracodeum\Kit\Components\Type\Enumerations\Context) $context [default = INTERNAL]
	 * <p>The context to get for.</p>
	 * @return \Dracodeum\Kit\Primitives\Text|null
	 * <p>The label, as a text instance, or <code>null</code> if none is set.</p>
	 */
	final public function getLabel($context = EContext::INTERNAL): ?Text
	{
		//initialize
		$context = EContext::coerceValue($context);
		$prototype = $this->getPrototype();
		
		//return
		return $prototype instanceof PrototypeInterfaces\InformationProducer
			? UCall::guardExecution([$prototype, 'produceLabel'], [$context], [Text::class, 'coerce'])
			: null;
	}
	
	/**
	 * Get description.
	 * 
	 * @param coercible:enum:value(Dracodeum\Kit\Components\Type\Enumerations\Context) $context [default = INTERNAL]
	 * <p>The context to get for.</p>
	 * @return \Dracodeum\Kit\Primitives\Text|null
	 * <p>The description, as a text instance, or <code>null</code> if none is set.</p>
	 */
	final public function getDescription($context = EContext::INTERNAL): ?Text
	{
		//initialize
		$context = EContext::coerceValue($context);
		$prototype = $this->getPrototype();
		
		//return
		return $prototype instanceof PrototypeInterfaces\InformationProducer
			? UCall::guardExecution([$prototype, 'produceDescription'], [$context], [Text::class, 'coerce'])
			: null;
	}
	
	/**
	 * Add mutator.
	 * 
	 * @param coercible:component(Dracodeum\Kit\Components\Type\Components\Mutator) $mutator
	 * <p>The mutator to add.</p>
	 * @param array $properties [default = []]
	 * <p>The properties to add with, as a set of <samp>name => value</samp> pairs.<br>
	 * Required properties may also be given as an array of values (<samp>[value1, value2, ...]</samp>), 
	 * in the same order as how these properties were first declared.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function addMutator($mutator, array $properties = [])
	{
		$this->mutators[] = Components\Mutator::coerce($mutator, $properties);
		return $this;
	}
}
