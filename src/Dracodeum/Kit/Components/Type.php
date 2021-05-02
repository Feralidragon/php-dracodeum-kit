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
use Dracodeum\Kit\Prototypes\Types\Number\Enumerations\Type as ENumberType;
use Dracodeum\Kit\Utilities\Call as UCall;

/**
 * This component represents a type which validates and normalizes values.
 * 
 * If a prototype is given as a name prefixed with a question mark character (`?`), then that character is stripped 
 * from the given name and the type is set as nullable.
 * 
 * @property-read bool $nullable [default = false]  
 * Allow a `null` value.
 * 
 * @property-write coercible:component<\Dracodeum\Kit\Components\Type\Components\Mutator>[] $mutators [writeonce] [transient] [default = []]  
 * The mutators to add, as any combination of the following:
 * - instances, classes or names;
 * - `class => properties` or `name => properties` pairs.
 * 
 * @method \Dracodeum\Kit\Prototypes\Type getPrototype() [protected]
 * 
 * @see \Dracodeum\Kit\Prototypes\Type
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
			'mutators'
				=> $this->createProperty()
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
					})
				,
			default => null
		};
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Component\Traits\PrototypeProducer)
	/** {@inheritdoc} */
	protected function producePrototype(string $name, array $properties)
	{
		return match ($name) {
			'boolean', 'bool' => Prototypes\Boolean::class,
			'number' => Prototypes\Number::class,
			'integer', 'int' => new Prototypes\Number(['type' => ENumberType::INTEGER] + $properties),
			'float' => new Prototypes\Number(['type' => ENumberType::FLOAT] + $properties),
			'string' => Prototypes\TString::class,
			'ustring' => new Prototypes\TString(['unicode' => true] + $properties),
			'enumeration', 'enum' => Prototypes\Enumeration::class,
			'class' => Prototypes\TClass::class,
			'interface' => Prototypes\TInterface::class,
			default => null
		};
	}
	
	
	
	//Final public methods
	/**
	 * Process a given value.
	 * 
	 * @param mixed $value
	 * The value to process.
	 * 
	 * @param coercible:enum<\Dracodeum\Kit\Components\Type\Enumerations\Context> $context
	 * The context to process for.
	 * 
	 * @return \Dracodeum\Kit\Primitives\Error|null
	 * An error instance if the given value failed to be processed, or `null` if otherwise.
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
	 * The value to textify.
	 * 
	 * @param coercible:enum<\Dracodeum\Kit\Components\Type\Enumerations\Context> $context
	 * The context to textify for.
	 * 
	 * @param bool $no_throw
	 * Do not throw an exception.
	 * 
	 * @throws \Dracodeum\Kit\Components\Type\Exceptions\TextificationFailed
	 * 
	 * @return \Dracodeum\Kit\Primitives\Text|null
	 * The given value textified, as a text instance.  
	 * If `$no_throw` is set to boolean `true`, then `null` is returned if the given value failed to be textified.
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
	 * Add mutator.
	 * 
	 * @param coercible:component<\Dracodeum\Kit\Components\Type\Components\Mutator> $mutator
	 * The mutator to add.
	 * 
	 * @param array $properties
	 * The properties to add with.
	 * 
	 * @return $this
	 * This instance, for chaining purposes.
	 */
	final public function addMutator($mutator, array $properties = [])
	{
		//initialize
		$prototype = $this->getPrototype();
		
		//add
		$this->mutators[] = Components\Mutator::produce(
			$mutator, $properties,
			$prototype instanceof PrototypeInterfaces\MutatorProducer ? [$prototype, 'produceMutator'] : null
		);
		
		//return
		return $this;
	}
}
