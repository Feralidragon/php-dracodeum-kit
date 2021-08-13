<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Managers\PropertiesV2;

use ReflectionProperty as Reflection;
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
	
	/** Affect subclasses by mode (flag). */
	private const FLAG_MODE_AFFECT_SUBCLASSES = 0x1;
	
	/** Lazy (flag). */
	private const FLAG_LAZY = 0x2;
	
	
	
	//Private properties
	private Reflection $reflection;
	
	private string $mode = 'rw';
	
	private ?Type $type = null;
	
	private int $flags = 0x0;
	
	
	
	//Final public magic methods
	/**
	 * Instantiate class.
	 * 
	 * @param \ReflectionProperty $reflection
	 * The reflection instance to instantiate with.
	 */
	final public function __construct(Reflection $reflection)
	{
		$this->reflection = $reflection;
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
	 * Check if is required.
	 * 
	 * @return bool
	 * Boolean `true` if is required.
	 */
	final public function isRequired(): bool
	{
		return $this->reflection->isPublic() && !$this->hasDefaultValue() && $this->mode !== 'r';
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
	final public function isAccessible(?string $scope_class): bool
	{
		if ($this->reflection->isPublic()) {
			return true;
		} elseif ($scope_class === null || !$this->reflection->isProtected()) {
			return false;
		} elseif (is_a($scope_class, $this->reflection->getDeclaringClass()->getName(), true)) {
			return true;
		}
		return false;
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
	 * instantiation;
	 * - `r+` : allow this property to be only read from (read-only), but allowing to be given during instantiation;
	 * - `rw` : allow this property to be both read from and written to (read-write);
	 * - `w` : allow this property to be only written to (write-only);
	 * - `w-` : allow this property to be only written to, but only once during instantiation (write-once).
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
	final public function isReadable(?string $scope_class): bool
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
	final public function isWriteable(?string $scope_class, bool $initializing = false): bool
	{
		//check
		if (($initializing && $this->mode !== 'r') || in_array($this->mode, ['rw', 'w'], true)) {
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
	final public function setTypeByReflection(array $properties)
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
		$function->bindTo($object, $object);
		$function($this->reflection->getName());
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
		$this->reflection->setAccessible(true); //TODO: to be removed in PHP 8.1: https://wiki.php.net/rfc/make-reflection-setaccessible-no-op
		try {
			$this->reflection->setValue($object, $value);
			$value = $this->reflection->getValue($object);
		} catch (TypeError $type_error) {
			$text = Text::build()
				->setString("Invalid value type.")
				->setString($type_error->getMessage(), EInfoLevel::INTERNAL)
			;
			return Error::build(text: $text);
		} finally {
			$this->reset($object);
		}
		
		//return
		return null;
	}
}
