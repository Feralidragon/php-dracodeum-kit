<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components;

use Dracodeum\Kit\Component;
use Dracodeum\Kit\Components\Type\{
	Components,
	Exceptions,
	Protoname
};
use Dracodeum\Kit\Components\Type\Enumerations\Context as EContext;
use Dracodeum\Kit\Components\Type\Protoname\Enumerations\Type as EProtonameType;
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
		//protoname
		$protoname = $this->protonamify($name, true);
		if ($protoname === null) {
			return null;
		}
		
		//process
		switch ($protoname->type) {
			//simple
			case EProtonameType::SIMPLE:
				$name = $protoname->names[0];
				break;
				
			//generic
			case EProtonameType::GENERIC:
				switch ($protoname->names[0]) {
					case 'class':
						return !isset($protoname->names[2])
							? new Prototypes\TClass(['class' => $protoname->names[1]] + $properties)
							: null;
					case 'object':
						return !isset($protoname->names[2])
							? new Prototypes\TObject(['class' => $protoname->names[1]] + $properties)
							: null;
					case 'resource':
						return !isset($protoname->names[2])
							? new Prototypes\TResource(['type' => $protoname->names[1]] + $properties)
							: null;
					case 'array':
						if (isset($protoname->names[3])) {
							return null;
						} elseif (isset($protoname->names[2])) {
							return new Prototypes\TArray([
								'type' => $this->build($protoname->names[2], ['strict' => $this->strict]),
								'key_type' => $this->build($protoname->names[1], ['strict' => $this->strict])
							] + $properties);
						}
						return new Prototypes\TArray([
							'type' => $this->build($protoname->names[1], ['strict' => $this->strict])
						] + $properties);
				}
				return null;
				
			//array
			case EProtonameType::ARRAY:
				return new Prototypes\TArray([
					'type' => $this->build($protoname->names[0], ['strict' => $this->strict]),
					'non_associative' => true
				] + $properties);
				
			//union
			case EProtonameType::UNION:
				$types = [];
				foreach ($protoname->names as $type_name) {
					$types[] = $this->build($type_name, ['strict' => $this->strict]);
				}
				return new Prototypes\Any(['types' => $types] + $properties);
		}
		
		//prototype
		$prototype = match ($name) {
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
		if ($prototype !== null) {
			return $prototype;
		}
		
		//instance
		if (class_exists($name)) {
			return new Prototypes\TObject(['class' => $name] + $properties);
		}
		
		//return
		return null;
	}
	
	
	
	//Final public methods
	/**
	 * Process a given value.
	 * 
	 * @param mixed $value
	 * The value to process.
	 * 
	 * @param coercible<enum<\Dracodeum\Kit\Components\Type\Enumerations\Context>> $context
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
	 * @param coercible<enum<\Dracodeum\Kit\Components\Type\Enumerations\Context>> $context
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
			throw new Exceptions\CastFailed($this, $value, $context, error: $error);
		}
		return $value;
	}
	
	/**
	 * Process the coercion of a given value.
	 * 
	 * @param mixed $value
	 * The value to process the coercion of.
	 * 
	 * @param coercible<enum<\Dracodeum\Kit\Components\Type\Enumerations\Context>> $context
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
			throw new Exceptions\CoercionFailed($this, $value, $context, error: $error);
		}
		return true;
	}
	
	/**
	 * Textify a given value.
	 * 
	 * @param mixed $value
	 * The value to textify.
	 * 
	 * @param coercible<enum<\Dracodeum\Kit\Components\Type\Enumerations\Context>> $context
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
			throw new Exceptions\TextificationFailed($this, $value, $context, error: $error);
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
		throw new Exceptions\TextificationFailed($this, $value, $context);
	}
	
	/**
	 * Add mutator.
	 * 
	 * @param coercible<\Dracodeum\Kit\Components\Type\Components\Mutator> $mutator
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
	
	
	
	//Private methods
	/**
	 * Protonamify a given name.
	 * 
	 * @param string $name
	 * The name to protonamify.
	 * 
	 * @param bool $degroup
	 * Return a degrouped protoname instance.
	 * 
	 * @return \Dracodeum\Kit\Components\Type\Protoname|null
	 * A protoname instance from the given name, or `null` if none is instantiated.
	 */
	private function protonamify(string $name, bool $degroup = false): ?Protoname
	{
		//empty
		$name = trim($name);
		if ($name === '') {
			return null;
		}
		
		//tokenize
		$depth = 0;
		$names = [$name];
		$type = EProtonameType::SIMPLE;
		$buffer = $inner_buffer = '';
		foreach (preg_split('/([<>()|,]|\[\])/', $name, flags: PREG_SPLIT_DELIM_CAPTURE) as $token) {
			//initialize
			$token = trim($token);
			if ($token === '') {
				continue;
			}
			
			//token
			if ($depth === 0) {
				switch ($token) {
					case '|':
						if ($buffer === '') {
							return null;
						} elseif ($type !== EProtonameType::UNION) {
							$type = EProtonameType::UNION;
							$names = [];
						}
						$names[] = $buffer;
						$buffer = $inner_buffer = '';
						continue 2;
					case '<':
						if ($type === EProtonameType::SIMPLE) {
							if ($buffer === '') {
								return null;
							}
							$type = EProtonameType::GENERIC;
							$names = [$buffer];
						}
						break;
					case '(':
						if ($type === EProtonameType::SIMPLE) {
							if ($buffer !== '') {
								return null;
							}
							$type = EProtonameType::GROUP;
						}
						break;
					case '>':
					case ')':
					case ',':
						return null;
					case '[]':
						if ($type !== EProtonameType::UNION) {
							if ($buffer === '') {
								return null;
							}
							$type = EProtonameType::ARRAY;
							$names = [$buffer];
						}
						break;
				}
			} elseif ($depth === 1 && $type !== EProtonameType::UNION) {
				switch ($token) {
					case '>':
						if ($type !== EProtonameType::GENERIC || $inner_buffer === '') {
							return null;
						}
						$names[] = $inner_buffer;
						break;
					case ')':
						if ($type !== EProtonameType::GROUP) {
							return null;
						}
						$names = [$inner_buffer];
						$inner_buffer = '';
						break;
					case ',':
						if ($type !== EProtonameType::GENERIC) {
							return null;
						}
						$names[] = $inner_buffer;
						$inner_buffer = '';
						continue 2;
				}
			}
			
			//buffer
			$buffer .= $token;
			if ($depth > 0) {
				$inner_buffer .= $token;
			}
			
			//depth
			if ($token === '<' || $token === '(') {
				$depth++;
			} elseif ($token === '>' || $token === ')') {
				$depth--;
				if ($depth < 0) {
					return null;
				}
			}
		}
		
		//group
		if ($degroup && $type === EProtonameType::GROUP) {
			return $this->protonamify($names[0]);
		}
		
		//union
		if ($type === EProtonameType::UNION) {
			if ($buffer === '') {
				return null;
			}
			$names[] = $buffer;
		}
		
		//return
		return new Protoname($type, $names);
	}
}
