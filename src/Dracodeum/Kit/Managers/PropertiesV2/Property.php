<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Managers\PropertiesV2;

use ReflectionProperty as Reflection;
use Dracodeum\Kit\Managers\PropertiesV2\Property\Exceptions;
use Dracodeum\Kit\Components\Type;
use Dracodeum\Kit\Primitives\{
	Error,
	Text
};
use Dracodeum\Kit\Enumerations\InfoLevel as EInfoLevel;
use Dracodeum\Kit\Utilities\{
	Byte as UByte,
	Call as UCall,
	Text as UText
};
use ReflectionNamedType;
use ReflectionUnionType;
use TypeError;

final class Property
{
	//Private constants
	/** Modes of operation. */
	private const MODES = ['r', 'r+', 'rw', 'w', 'w-'];
	
	/** Lazy (flag). */
	private const FLAG_LAZY = 0x1;
	
	/** Required (flag). */
	private const FLAG_REQUIRED = 0x2;
	
	/** Affect subclasses by mode (flag). */
	private const FLAG_MODE_AFFECT_SUBCLASSES = 0x4;
	
	
	
	//Private properties
	private Reflection $reflection;
	
	private Meta $meta;
	
	private string $mode = 'rw';
	
	private ?Type $type = null;
	
	private int $flags = 0x0;
	
	/** @var array<string,mixed> */
	private array $meta_values = [];
	
	
	
	//Final public magic methods
	/**
	 * Instantiate class.
	 * 
	 * @param \ReflectionProperty $reflection
	 * The reflection instance to instantiate with.
	 * 
	 * @param \Dracodeum\Kit\Managers\PropertiesV2\Meta $meta
	 * The meta instance to instantiate with.
	 */
	final public function __construct(Reflection $reflection, Meta $meta)
	{
		$this->reflection = $reflection;
		$this->meta = $meta;
	}
	
	
	
	//Final public methods
	/**
	 * Get reflection instance.
	 * 
	 * @return \ReflectionProperty
	 * The reflection instance.
	 */
	final public function getReflection(): Reflection
	{
		return $this->reflection;
	}
	
	/**
	 * Get meta instance.
	 * 
	 * @return \Dracodeum\Kit\Managers\PropertiesV2\Meta
	 * The meta instance.
	 */
	final public function getMeta(): Meta
	{
		return $this->meta;
	}
	
	/**
	 * Get name.
	 * 
	 * @return string
	 * The name.
	 */
	final public function getName(): string
	{
		return $this->reflection->getName();
	}
	
	/**
	 * Check if is required.
	 * 
	 * @return bool
	 * Boolean `true` if is required.
	 */
	final public function isRequired(): bool
	{
		return UByte::hasFlag($this->flags, self::FLAG_REQUIRED) || (
			$this->reflection->isPublic() && !$this->hasDefaultValue() && $this->mode !== 'r'
		);
	}
	
	/**
	 * Set as required.
	 * 
	 * @return $this
	 * This instance, for chaining purposes.
	 */
	final public function setAsRequired()
	{
		UByte::setFlag($this->flags, self::FLAG_REQUIRED);
		return $this;
	}
	
	/**
	 * Check if is accessible.
	 * 
	 * @param string|null $scope_class
	 * The scope class to check with.
	 * 
	 * @return bool
	 * Boolean `true` if is accessible.
	 */
	final public function isAccessible(?string $scope_class = null): bool
	{
		return $this->reflection->isPublic() || (
			$scope_class !== null && is_a($scope_class, $this->reflection->getDeclaringClass()->getName(), true)
		);
	}
	
	/**
	 * Check if has default value.
	 * 
	 * @return bool
	 * Boolean `true` if has default value.
	 */
	final public function hasDefaultValue(): bool
	{
		return $this->reflection->hasDefaultValue();
	}
	
	/**
	 * Get default value.
	 * 
	 * @return mixed
	 * The default value.
	 */
	final public function getDefaultValue(): mixed
	{
		return $this->reflection->getDefaultValue();
	}
	
	/**
	 * Get mode.
	 * 
	 * @return string
	 * The mode.
	 */
	final public function getMode(): string
	{
		return $this->mode;
	}
	
	/**
	 * Check if subclasses are affected by mode.
	 * 
	 * @return bool
	 * Boolean `true` if subclasses are affected by mode.
	 */
	final public function areSubclassesAffectedByMode(): bool
	{
		return UByte::hasFlag($this->flags, self::FLAG_MODE_AFFECT_SUBCLASSES);
	}
	
	/**
	 * Set mode.
	 * 
	 * @param string $mode
	 * The mode to set, as one of the following:
	 * - `r` : allow this property to be only strictly read from (exclusive read-only), not allowing to be given during 
	 * initialization;
	 * - `r+` : allow this property to be only read from (read-only), but allowing to be given during initialization;
	 * - `rw` : allow this property to be both read from and written to (read-write);
	 * - `w` : allow this property to be only written to (write-only);
	 * - `w-` : allow this property to be only written to, but only once during initialization (write-once).
	 * 
	 * @param bool $affect_subclasses
	 * Enforce the mode of operation internally for subclasses as well.
	 * 
	 * @return $this
	 * This instance, for chaining purposes.
	 */
	final public function setMode(string $mode, bool $affect_subclasses = false)
	{
		//check
		if (!in_array($mode, self::MODES, true)) {
			UCall::haltParameter('mode', $mode, [
				'hint_message' => "Only one of the following is allowed: {{modes}}.",
				'parameters' => ['modes' => self::MODES],
				'string_options' => ['non_assoc_mode' => UText::STRING_NONASSOC_MODE_COMMA_LIST_OR]
			]);
		}
		
		//set
		$this->mode = $mode;
		UByte::updateFlag($this->flags, self::FLAG_MODE_AFFECT_SUBCLASSES, $affect_subclasses);
		
		//return
		return $this;
	}
	
	/**
	 * Check if is readable.
	 * 
	 * @param string|null $scope_class
	 * The scope class to check with.
	 * 
	 * @return bool
	 * Boolean `true` if is readable.
	 */
	final public function isReadable(?string $scope_class = null): bool
	{
		//check
		if ($this->mode[0] === 'r') {
			return true;
		} elseif ($scope_class === null) {
			return false;
		}
		
		//scope
		$declaring_class = $this->reflection->getDeclaringClass()->getName();
		return $scope_class === $declaring_class || (
			is_a($scope_class, $declaring_class, true) && !$this->areSubclassesAffectedByMode()
		);
	}
	
	/**
	 * Check if is writeable.
	 * 
	 * @param string|null $scope_class
	 * The scope class to check with.
	 * 
	 * @param bool $initializing
	 * Whether or not the call is being performed in the context of an initialization.
	 * 
	 * @return bool
	 * Boolean `true` if is writeable.
	 */
	final public function isWriteable(?string $scope_class = null, bool $initializing = false): bool
	{
		//check
		if ($this->mode !== 'r' && ($initializing || in_array($this->mode, ['rw', 'w'], true))) {
			return true;
		} elseif ($scope_class === null) {
			return false;
		}
		
		//scope
		$declaring_class = $this->reflection->getDeclaringClass()->getName();
		return $scope_class === $declaring_class || (
			is_a($scope_class, $declaring_class, true) && !$this->areSubclassesAffectedByMode()
		);
	}
	
	/**
	 * Check if has type.
	 * 
	 * @return bool
	 * Boolean `true` if has type.
	 */
	final public function hasType(): bool
	{
		return $this->type !== null;
	}
	
	/**
	 * Get type instance.
	 * 
	 * @return \Dracodeum\Kit\Components\Type|null
	 * The type instance, or `null` if none is set.
	 */
	final public function getType(): ?Type
	{
		return $this->type;
	}
	
	/**
	 * Set type instance.
	 * 
	 * @param \Dracodeum\Kit\Components\Type $type
	 * The type instance to set.
	 * 
	 * @return $this
	 * This instance, for chaining purposes.
	 */
	final public function setType(Type $type)
	{
		$this->type = $type;
		return $this;
	}
	
	/**
	 * Set type by reflection.
	 * 
	 * @param array $properties
	 * The properties to set with.
	 * 
	 * @return $this
	 * This instance, for chaining purposes.
	 */
	final public function setTypeByReflection(array $properties = [])
	{
		//initialize
		$r_inner_types = [];
		$r_type = $this->reflection->getType();
		if ($r_type === null) {
			$this->type = null;
			return $this;
		} elseif ($r_type instanceof ReflectionNamedType) {
			$r_inner_types[] = $r_type;
		} elseif ($r_type instanceof ReflectionUnionType) {
			$r_inner_types = $r_type->getTypes();
		} else {
			UCall::haltInternal([
				'error_message' => "Unknown reflection type class {{class}}.",
				'parameters' => ['class' => $r_type::class]
			]);
		}
		
		//properties
		$properties['nullable'] = $r_type->allowsNull();
		
		//inner
		$inner_types = [];
		$is_single_type = count($r_inner_types) === 1;
		foreach ($r_inner_types as $r_inner_type) {
			//initialize
			$inner_properties = [];
			$inner_prototype = $r_inner_type->getName();
			
			//prototype
			if ($inner_prototype === 'null') {
				continue;
			} elseif (class_exists($inner_prototype)) {
				$inner_properties['class'] = $inner_prototype;
				$inner_prototype = 'object';
			}
			
			//single
			if ($is_single_type) {
				$this->type = Type::build($inner_prototype, $inner_properties + $properties);
				return $this;
			}
			
			//append
			$inner_types[] = Type::build($inner_prototype, $inner_properties);
		}
		
		//finalize
		$this->type = Type::build('any', ['types' => $inner_types] + $properties);
		
		//return
		return $this;
	}
	
	/**
	 * Check if is lazy.
	 * 
	 * @return bool
	 * Boolean `true` if is lazy.
	 */
	final public function isLazy(): bool
	{
		return UByte::hasFlag($this->flags, self::FLAG_LAZY);
	}
	
	/**
	 * Set as lazy, so that a value is only validated and coerced later on read, instead of immediately on write.
	 * 
	 * @return $this
	 * This instance, for chaining purposes.
	 */
	final public function setAsLazy()
	{
		UByte::setFlag($this->flags, self::FLAG_LAZY);
		return $this;
	}
	
	/**
	 * Reset for a given object.
	 * 
	 * @param object $object
	 * The object to reset for.
	 * 
	 * @return $this
	 * This instance, for chaining purposes.
	 */
	final public function reset(object $object)
	{
		$function = function (string $name): void {
			unset($this->$name);
		};
		$function->bindTo($object, $object)($this->getName());
		return $this;
	}
	
	/**
	 * Process value for a given object.
	 * 
	 * @param object $object
	 * The object to process for.
	 * 
	 * @param mixed $value
	 * The value to process.
	 * 
	 * @return \Dracodeum\Kit\Primitives\Error|null
	 * An error instance if the given value failed to be processed, or `null` if otherwise.
	 */
	final public function processValue(object $object, mixed &$value): ?Error
	{
		//type
		if ($this->type !== null) {
			return $this->type->process($value);
		}
		
		//self
		try {
			//initialize
			$name = $this->getName();
			$set = function (string $name, mixed $value): void {
				$this->$name = $value;
			};
			$get = fn (string $name): mixed => $this->$name;
			
			//process
			$set->bindTo($object, $object)($name, $value);
			$value = $get->bindTo($object, $object)($name);
			
		} catch (TypeError $type_error) {
			$text = Text::build()
				->setString("Invalid value type.")
				->setString("{$type_error->getMessage()}.", EInfoLevel::INTERNAL)
			;
			return Error::build(text: $text);
		} finally {
			$this->reset($object);
		}
		
		//return
		return null;
	}
	
	/**
	 * Get meta value.
	 * 
	 * @param string $name
	 * The name of the meta value to get.
	 * 
	 * @return mixed
	 * The meta value.
	 */
	final public function getMetaValue(string $name): mixed
	{
		return array_key_exists($name, $this->meta_values)
			? $this->meta_values[$name]
			: $this->meta->get($name)->default;
	}
	
	/**
	 * Set meta value.
	 * 
	 * @param string $name
	 * The name of the meta value to set.
	 * 
	 * @param mixed $value
	 * The value to set.
	 * 
	 * @throws \Dracodeum\Kit\Managers\PropertiesV2\Property\Exceptions\InvalidMetaValue
	 * 
	 * @return $this
	 * This instance, for chaining purposes.
	 */
	final public function setMetaValue(string $name, mixed $value)
	{
		//process
		$error = $this->meta->process($name, $value);
		if ($error !== null) {
			throw new Exceptions\InvalidMetaValue([$this, $name, $value, $error]);
		}
		
		//set
		$this->meta_values[$name] = $value;
		
		//return
		return $this;
	}
}
