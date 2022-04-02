<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
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
 * This component represents a type which validates and coerces values.
 * 
 * If a prototype is given as a name prefixed with a question mark character (`?`), then that character is stripped 
 * from the given name and the type is set as nullable.
 * 
 * @property-read bool $nullable [default = false]  
 * Allow a `null` value.
 * 
 * @property-read bool $strict [default = false]  
 * Perform strict validation without value coercion.
 * 
 * @method \Dracodeum\Kit\Prototypes\Type getPrototype() [protected]
 * 
 * @see \Dracodeum\Kit\Prototypes\Type
 */
class Type extends Component
{
	//Private properties
	private bool $nullable = false;
	
	private bool $strict = false;
	
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
		if (is_string($prototype) && isset($prototype[0])) {
			if ($prototype[0] === '?') {
				$prototype = substr($prototype, 1);
				$properties['nullable'] = true;
			} elseif ($prototype[0] !== '(') {
				$count = 0;
				$prototype = preg_replace('/^\s*null[^\w]+|[^\w]+null(?!\w)/', '', $prototype, count: $count);
				if ($count) {
					$properties['nullable'] = true;
				}
			}
		}
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Component\Traits\PropertyBuilder)
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		return match ($name) {
			'nullable', 'strict' => $this->createProperty()->setMode('r+')->setAsBoolean()->bind(self::class),
			default => null
		};
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Component\Traits\PrototypeProducer)
	/** {@inheritdoc} */
	protected function producePrototype(string $name, array $properties)
	{
		//enclosed array
		if (substr($name, -3) === ')[]' && $name[0] === '(') {
			$type = $this->build(substr($name, 1, -3), ['strict' => $this->strict]);
			return new Prototypes\TArray(['type' => $type, 'non_associative' => true] + $properties);
		}
		
		//union
		if (strpos($name, '|') !== false) {
			$types = [];
			foreach (array_map('trim', explode('|', $name)) as $type_name) {
				$types[] = $this->build($type_name, ['strict' => $this->strict]);
			}
			return new Prototypes\Any(['types' => $types] + $properties);
		}
		
		//array
		if (substr($name, -2) === '[]') {
			$type = $this->build(substr($name, 0, -2), ['strict' => $this->strict]);
			return new Prototypes\TArray(['type' => $type, 'non_associative' => true] + $properties);
		}
		
		//return
		return match ($name) {
			'any', 'mixed' => Prototypes\Any::class,
			'boolean', 'bool' => Prototypes\Boolean::class,
			'number' => Prototypes\Number::class,
			'integer', 'int' => new Prototypes\Number(['type' => ENumberType::INTEGER] + $properties),
			'float', 'double' => new Prototypes\Number(['type' => ENumberType::FLOAT] + $properties),
			'string' => Prototypes\TString::class,
			'ustring' => new Prototypes\TString(['unicode' => true] + $properties),
			'enumeration', 'enum' => Prototypes\Enumeration::class,
			'class' => Prototypes\TClass::class,
			'interface' => Prototypes\TInterface::class,
			'object' => Prototypes\TObject::class,
			'resource' => Prototypes\TResource::class,
			'callable' => Prototypes\TCallable::class,
			'closure' => new Prototypes\TCallable(['closurify' => true] + $properties),
			'array' => Prototypes\TArray::class,
			'list' => new Prototypes\TArray(['non_associative' => true] + $properties),
			'component' => Prototypes\Component::class,
			'structure', 'struct' => Prototypes\Structure::class,
			'text' => Prototypes\Text::class,
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
		$error = $prototype->process($v, $context, $this->strict);
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
	 * Process the cast of a given value.
	 * 
	 * @param mixed $value
	 * The value to process the cast of.
	 * 
	 * @param coercible:enum<\Dracodeum\Kit\Components\Type\Enumerations\Context> $context
	 * The context to process the cast for.
	 * 
	 * @param bool $no_throw
	 * Do not throw an exception.
	 * 
	 * @throws \Dracodeum\Kit\Components\Type\Exceptions\CastFailed
	 * 
	 * @return mixed
	 * The given value cast.  
	 * If `$no_throw` is set to boolean `true`, then `null` is returned if the given value failed to be cast.
	 */
	final public function processCast(mixed $value, $context = EContext::INTERNAL, bool $no_throw = false): mixed
	{
		$error = $this->process($value, $context);
		if ($error !== null) {
			if ($no_throw) {
				return null;
			}
			throw new Exceptions\CastFailed([$this, $value, $context, 'error' => $error]);
		}
		return $value;
	}
	
	/**
	 * Process the coercion of a given value.
	 * 
	 * @param mixed $value
	 * The value to process the coercion of.
	 * 
	 * @param coercible:enum<\Dracodeum\Kit\Components\Type\Enumerations\Context> $context
	 * The context to process the coercion for.
	 * 
	 * @param bool $no_throw
	 * Do not throw an exception.
	 * 
	 * @throws \Dracodeum\Kit\Components\Type\Exceptions\CoercionFailed
	 * 
	 * @return bool
	 * Boolean `true` is always returned if the given value is successfully coerced, otherwise an exception is thrown, 
	 * unless `$no_throw` is set to boolean `true`, in which case boolean `false` is returned instead.
	 */
	final public function processCoercion2(mixed &$value, $context = EContext::INTERNAL, bool $no_throw = false): bool
	{
		$error = $this->process($value, $context);
		if ($error !== null) {
			if ($no_throw) {
				return false;
			}
			throw new Exceptions\CoercionFailed([$this, $value, $context, 'error' => $error]);
		}
		return true;
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
			throw new Exceptions\TextificationFailed([$this, $value, $context, 'error' => $error]);
		}
		
		//textifier
		if ($has_textifier && ($v !== null || !$this->nullable)) {
			//execute
			$text = UCall::guardExecution([$prototype, 'textify'], [$v], function (&$value): bool {
				return $value !== null ? Text::coerce($value) : true;
			});
			
			//return
			if ($text !== null) {
				return $text;
			}
		}
		
		//textify
		if ($v === null) {
			return Text::build("null")->setAsLocalized(self::class);
		} elseif ($v instanceof IStringable) {
			return Text::build($v->toString());
		} elseif (is_scalar($v) || $v instanceof IPhpStringable) {
			return Text::build((string)$v);
		} elseif ($no_throw) {
			return null;
		}
		throw new Exceptions\TextificationFailed([$this, $value, $context]);
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
	
	/**
	 * Check if has mutators.
	 * 
	 * @return bool
	 * Boolean `true` if has mutators.
	 */
	final public function hasMutators(): bool
	{
		return (bool)$this->mutators;
	}
}
