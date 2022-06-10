<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Managers;

use Dracodeum\Kit\Manager;
use Dracodeum\Kit\Managers\PropertiesV2\{
	Meta,
	Property,
	Interfaces,
	Exceptions,
	ClassEntry
};
use Dracodeum\Kit\Utilities\{
	Byte as UByte,
	Call as UCall
};
use ReflectionClass;

/**
 * This manager handles and extends the properties of an object class with the following functionality:
 * - extended coercive and strict typification, using type components and mutators;
 * - modes of operation, such as read-write, read-only, write-only, and others;
 * - lazy type validation and coercion.
 * 
 * Only non-static and non-private properties are supported and affected.
 * 
 * @see \Dracodeum\Kit\Managers\PropertiesV2\Interfaces\Attribute\Class\MetaInitializer
 * @see \Dracodeum\Kit\Managers\PropertiesV2\Interfaces\Attribute\Property\PropertyInitializer
 * @see \Dracodeum\Kit\Managers\PropertiesV2\Interfaces\Attribute\Property\PropertyPostInitializer
 */
final class PropertiesV2 extends Manager
{
	//Private constants
	/** Set (value flag). */
	private const VALUE_FLAG_SET = 0x1;
	
	/** Typed (value flag). */
	private const VALUE_FLAG_TYPED = 0x2;
	
	/** Dirty (value flag). */
	private const VALUE_FLAG_DIRTY = 0x4;
	
	
	
	//Private properties
	private object $owner;
	
	private ?string $owner_base_class = null;
	
	/** @var array<string,\Dracodeum\Kit\Managers\PropertiesV2\Property> */
	private array $properties;
	
	/** @var array<string,int> */
	private array $required_map;
	
	private Meta $meta;
	
	private bool $initialized = false;
	
	/** @var array<string,mixed> */
	private array $values = [];
	
	/** @var array<string,int> */
	private array $values_flags = [];
	
	
	
	//Private static properties
	/** @var array<string,\Dracodeum\Kit\Managers\PropertiesV2\ClassEntry> */
	private static array $classes_entries = [];
	
	
	
	//Final public magic methods
	/**
	 * Instantiate class.
	 * 
	 * @param object $owner
	 * The owner object to instantiate with.
	 * 
	 * @param string|null $owner_base_class
	 * The owner base class to instantiate with.
	 */
	final public function __construct(object $owner, ?string $owner_base_class = null)
	{
		$this->owner = $owner;
		$this->owner_base_class = $owner_base_class;
		$this->initializeProperties();
	}
	
	
	
	//Final public methods
	/**
	 * Get owner object.
	 * 
	 * @return object
	 * The owner object.
	 */
	final public function getOwner(): object
	{
		return $this->owner;
	}
	
	/**
	 * Get owner base class.
	 * 
	 * @return string|null
	 * The owner base class, or `null` if none is set.
	 */
	final public function getOwnerBaseClass(): ?string
	{
		return $this->owner_base_class;
	}
	
	/**
	 * Check if has a property with a given name.
	 * 
	 * @param string $name
	 * The name of the property to check.
	 * 
	 * @return bool
	 * Boolean `true` if has the property with the given name.
	 */
	final public function hasProperty(string $name): bool
	{
		return isset($this->properties[$name]);
	}
	
	/**
	 * Get property instance with a given name.
	 * 
	 * @param string $name
	 * The name of the property to get.
	 * 
	 * @return \Dracodeum\Kit\Managers\PropertiesV2\Property|null
	 * The property instance with the given name, or `null` if none is set.
	 */
	final public function getProperty(string $name): ?Property
	{
		return $this->properties[$name] ?? null;
	}
	
	/**
	 * Get property instances.
	 * 
	 * @return array<string,\Dracodeum\Kit\Managers\PropertiesV2\Property>
	 * The property instances, as a set of `name => property` pairs.
	 */
	final public function getProperties(): array
	{
		return $this->properties;
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
	 * Check if is initialized.
	 * 
	 * @return bool
	 * Boolean `true` if is initialized.
	 */
	final public function isInitialized(): bool
	{
		return $this->initialized;
	}
	
	/**
	 * Initialize.
	 * 
	 * @param array $values
	 * The values to initialize with, as a set of `name => value` pairs.  
	 * Values corresponding to required properties may also be given as a non-associative array, with the given values 
	 * following the same order as their corresponding property declarations.
	 * 
	 * @param string|null $scope_class
	 * The scope class to use.
	 * 
	 * @throws \Dracodeum\Kit\Managers\PropertiesV2\Exceptions\Missing
	 * @throws \Dracodeum\Kit\Managers\PropertiesV2\Exceptions\Undefined
	 * @throws \Dracodeum\Kit\Managers\PropertiesV2\Exceptions\Inaccessible
	 * @throws \Dracodeum\Kit\Managers\PropertiesV2\Exceptions\Unwriteable
	 * @throws \Dracodeum\Kit\Managers\PropertiesV2\Exceptions\Invalid
	 * 
	 * @return $this
	 * This instance, for chaining purposes.
	 */
	final public function initialize(array $values = [], ?string $scope_class = null)
	{
		//check
		if ($this->initialized) {
			UCall::halt(['error_message' => "This manager is already initialized."]);
		}
		
		//initialize
		$this->processRequiredValues($values)->setValues($values, $scope_class, true);
		$this->initialized = true;
		
		//return
		return $this;
	}
	
	/**
	 * Check if has a property with a given name.
	 * 
	 * @param string $name
	 * The property name to check.
	 * 
	 * @param string|null $scope_class
	 * The scope class to use.
	 * 
	 * @return bool
	 * Boolean `true` if has the property with the given name.
	 */
	final public function has(string $name, ?string $scope_class = null): bool
	{
		if (isset($this->properties[$name])) {
			$property = $this->properties[$name];
			return $property->isAccessible($scope_class) && $property->isReadable($scope_class);
		}
		return false;
	}
	
	/**
	 * Check if a property with a given name is set (exists, is initialized and is not `null`).
	 * 
	 * @param string $name
	 * The name of the property to check.
	 * 
	 * @param string|null $scope_class
	 * The scope class to use.
	 * 
	 * @return bool
	 * Boolean `true` if the property with the given name is set (exists, is initialized and is not `null`).
	 */
	final public function isset(string $name, ?string $scope_class = null): bool
	{
		return $this->has($name, $scope_class) && $this->isValueInitialized($name) && 
			$this->get($name, $scope_class) !== null;
	}
	
	/**
	 * Get value of a property with a given name.
	 * 
	 * @param string $name
	 * The name of the property to get.
	 * 
	 * @param string|null $scope_class
	 * The scope class to use.
	 * 
	 * @throws \Dracodeum\Kit\Managers\PropertiesV2\Exceptions\Undefined
	 * @throws \Dracodeum\Kit\Managers\PropertiesV2\Exceptions\Inaccessible
	 * @throws \Dracodeum\Kit\Managers\PropertiesV2\Exceptions\Unreadable
	 * @throws \Dracodeum\Kit\Managers\PropertiesV2\Exceptions\Uninitialized
	 * @throws \Dracodeum\Kit\Managers\PropertiesV2\Exceptions\Invalid
	 * 
	 * @return mixed
	 * The value of the property with the given name.
	 */
	final public function get(string $name, ?string $scope_class = null): mixed
	{
		return $this->getValues([$name], $scope_class)[$name];
	}
	
	/**
	 * Get property values.
	 * 
	 * @param string[]|null $names
	 * The names of the properties to get.
	 * 
	 * @param string|null $scope_class
	 * The scope class to use.
	 * 
	 * @throws \Dracodeum\Kit\Managers\PropertiesV2\Exceptions\Undefined
	 * @throws \Dracodeum\Kit\Managers\PropertiesV2\Exceptions\Inaccessible
	 * @throws \Dracodeum\Kit\Managers\PropertiesV2\Exceptions\Unreadable
	 * @throws \Dracodeum\Kit\Managers\PropertiesV2\Exceptions\Uninitialized
	 * @throws \Dracodeum\Kit\Managers\PropertiesV2\Exceptions\Invalid
	 * 
	 * @return array
	 * The property values, as a set of `name => value` pairs.
	 */
	final public function mget(?array $names = null, ?string $scope_class = null): array
	{
		if ($names === null) {
			$names = [];
			foreach ($this->properties as $name => $property) {
				if ($this->has($name, $scope_class) && $this->isValueInitialized($name)) {
					$names[] = $name;
				}
			}
		}
		return $this->getValues($names, $scope_class);
	}
	
	/**
	 * Set value into a property with a given name.
	 * 
	 * @param string $name
	 * The name of the property to set into.
	 * 
	 * @param mixed $value
	 * The value to set.
	 * 
	 * @param string|null $scope_class
	 * The scope class to use.
	 * 
	 * @throws \Dracodeum\Kit\Managers\PropertiesV2\Exceptions\Undefined
	 * @throws \Dracodeum\Kit\Managers\PropertiesV2\Exceptions\Inaccessible
	 * @throws \Dracodeum\Kit\Managers\PropertiesV2\Exceptions\Unwriteable
	 * @throws \Dracodeum\Kit\Managers\PropertiesV2\Exceptions\Invalid
	 * 
	 * @return $this
	 * This instance, for chaining purposes.
	 */
	final public function set(string $name, mixed $value, ?string $scope_class = null)
	{
		$this->setValues([$name => $value], $scope_class);
		return $this;
	}
	
	/**
	 * Set property values.
	 * 
	 * @param array $values
	 * The values to set, as a set of `name => value` pairs.
	 * 
	 * @param string|null $scope_class
	 * The scope class to use.
	 * 
	 * @throws \Dracodeum\Kit\Managers\PropertiesV2\Exceptions\Undefined
	 * @throws \Dracodeum\Kit\Managers\PropertiesV2\Exceptions\Inaccessible
	 * @throws \Dracodeum\Kit\Managers\PropertiesV2\Exceptions\Unwriteable
	 * @throws \Dracodeum\Kit\Managers\PropertiesV2\Exceptions\Invalid
	 * 
	 * @return $this
	 * This instance, for chaining purposes.
	 */
	final public function mset(array $values, ?string $scope_class = null)
	{
		$this->setValues($values, $scope_class);
		return $this;
	}
	
	/**
	 * Unset property value.
	 * 
	 * @param string $name
	 * The name of the property to unset.
	 * 
	 * @param string|null $scope_class
	 * The scope class to use.
	 * 
	 * @throws \Dracodeum\Kit\Managers\PropertiesV2\Exceptions\Undefined
	 * @throws \Dracodeum\Kit\Managers\PropertiesV2\Exceptions\Inaccessible
	 * @throws \Dracodeum\Kit\Managers\PropertiesV2\Exceptions\Ununsettable
	 * 
	 * @return $this
	 * This instance, for chaining purposes.
	 */
	final public function unset(string $name, ?string $scope_class = null)
	{
		$this->unsetValues([$name], $scope_class);
		return $this;
	}
	
	/**
	 * Unset property values.
	 * 
	 * @param string[] $names
	 * The names of the properties to unset.
	 * 
	 * @param string|null $scope_class
	 * The scope class to use.
	 * 
	 * @throws \Dracodeum\Kit\Managers\PropertiesV2\Exceptions\Undefined
	 * @throws \Dracodeum\Kit\Managers\PropertiesV2\Exceptions\Inaccessible
	 * @throws \Dracodeum\Kit\Managers\PropertiesV2\Exceptions\Ununsettable
	 * 
	 * @return $this
	 * This instance, for chaining purposes.
	 */
	final public function munset(array $names, ?string $scope_class = null)
	{
		$this->unsetValues($names, $scope_class);
		return $this;
	}
	
	
	
	//Final public static methods
	/** Clear cache. */
	final public static function clearCache(): void
	{
		self::$classes_entries = [];
	}
	
	
	
	//Private methods
	/** Initialize properties. */
	private function initializeProperties(): void
	{
		//initialize
		$owner_class = $this->owner::class;
		if (!isset(self::$classes_entries[$owner_class])) {
			//classes
			$is_outer = false;
			$classes = $outer_classes = [];
			for ($class = $owner_class; $class !== false; $class = get_parent_class($class)) {
				if ($is_outer) {
					$outer_classes[] = $class;
				} else {
					$classes[] = $class;
					$is_outer = $class === $this->owner_base_class;
				}
			}
			$classes = array_reverse($classes);
			
			//properties
			$parent_class = null;
			foreach ($classes as $class) {
				//class
				if (!isset(self::$classes_entries[$class])) {
					//initialize
					$r_class = new ReflectionClass($class);
					$properties_post_attributes = [];
					
					//properties
					$properties = $parent_class !== null ? self::$classes_entries[$parent_class]->properties : [];
					
					//meta
					$meta = $parent_class !== null
						? self::$classes_entries[$parent_class]->meta->clone($class)
						: new Meta($class);
					
					//class
					foreach ($r_class->getAttributes() as $r_attribute) {
						$attribute = $r_attribute->newInstance();
						if ($attribute instanceof Interfaces\Attribute\Class\MetaInitializer) {
							$attribute->initializeMeta($meta);
						}
					}
					
					//properties
					foreach ($r_class->getProperties() as $r_property) {
						//initialize
						$p_name = $r_property->getName();
						
						//check
						if (
							$r_property->isStatic() || $r_property->isPrivate() || isset($properties[$p_name]) || 
							in_array($r_property->getDeclaringClass()->getName(), $outer_classes, true)
						) {
							continue;
						}
						
						//property
						$property = new Property($r_property, $meta);
						
						//attributes
						foreach ($r_property->getAttributes() as $r_attribute) {
							$attribute = $r_attribute->newInstance();
							if ($attribute instanceof Interfaces\Attribute\Property\PropertyInitializer) {
								$attribute->initializeProperty($property);
							}
						}
						
						//ignore
						if ($property->isIgnored()) {
							continue;
						}
						
						//owner
						if ($this->owner instanceof Interfaces\PropertyInitializer) {
							$this->owner->initializeProperty($property);
						}
						
						//attributes (post)
						foreach ($r_property->getAttributes() as $r_attribute) {
							$attribute = $r_attribute->newInstance();
							if ($attribute instanceof Interfaces\Attribute\Property\PropertyPostInitializer) {
								$properties_post_attributes[$p_name][] = $attribute;
							}
						}
						
						//finalize
						$properties[$p_name] = $property;
					}
					
					//class entry
					$class_entry = self::$classes_entries[$class] = new ClassEntry($properties, $meta);
					
					//sort
					uasort($class_entry->properties, function (Property $property1, Property $property2): int {
						return (int)$property2->isRequired() - (int)$property1->isRequired();
					});
					
					//required mapping
					$i = 0;
					foreach ($class_entry->properties as $p_name => $property) {
						if ($property->isRequired()) {
							$class_entry->required_maps[$p_name] = $i++;
						} else {
							break;
						}
					}
					unset($i);
					
					//post-attributes
					foreach ($properties_post_attributes as $p_name => $attributes) {
						$property = $properties[$p_name];
						foreach ($attributes as $attribute) {
							$attribute->postInitializeProperty($property);
						}
					}
					
					//finalize
					unset($r_class, $properties_post_attributes, $properties, $meta, $class_entry);
				}
				
				//parent class
				$parent_class = $class;
			}
		}
		
		//finalize
		$class_entry = self::$classes_entries[$owner_class];
		$this->properties = $class_entry->properties;
		$this->required_map = $class_entry->required_maps;
		$this->meta = $class_entry->meta;
	}
	
	/**
	 * Process required values.
	 * 
	 * @param array $values
	 * The values to process, as a set of `name => value` pairs.  
	 * Values corresponding to required properties may also be given as a non-associative array, with the given values 
	 * following the same order as their corresponding property declarations.
	 * 
	 * @throws \Dracodeum\Kit\Managers\PropertiesV2\Exceptions\Missing
	 * 
	 * @return $this
	 * This instance, for chaining purposes.
	 */
	private function processRequiredValues(array &$values)
	{
		//map
		$mapped_values = [];
		$names_map = array_flip($this->required_map);
		foreach ($values as $k => $value) {
			$mapped_values[$names_map[$k] ?? $k] = $value;
		}
		
		//missing
		$missing_names = array_keys(array_diff_key($this->required_map, $mapped_values));
		if ($missing_names) {
			throw new Exceptions\Missing([$this, $missing_names]);
		}
		
		//finalize
		$values = $mapped_values;
		
		//return
		return $this;
	}
	
	/**
	 * Validate defined.
	 * 
	 * @param string[] $names
	 * The names to validate.
	 * 
	 * @throws \Dracodeum\Kit\Managers\PropertiesV2\Exceptions\Undefined
	 * 
	 * @return $this
	 * This instance, for chaining purposes.
	 */
	private function validateDefined(array $names)
	{
		//process
		$undefined_names = [];
		foreach ($names as $name) {
			if (!isset($this->properties[$name])) {
				$undefined_names[] = $name;
			}
		}
		
		//check
		if ($undefined_names) {
			throw new Exceptions\Undefined([$this, $undefined_names]);
		}
		
		//return
		return $this;
	}
	
	/**
	 * Validate access.
	 * 
	 * @param string[] $names
	 * The names to validate.
	 * 
	 * @param string|null $scope_class
	 * The scope class to use.
	 * 
	 * @throws \Dracodeum\Kit\Managers\PropertiesV2\Exceptions\Inaccessible
	 * 
	 * @return $this
	 * This instance, for chaining purposes.
	 */
	private function validateAccess(array $names, ?string $scope_class)
	{
		//process
		$inaccessible_names = [];
		foreach ($names as $name) {
			if (!isset($this->properties[$name]) || !$this->properties[$name]->isAccessible($scope_class)) {
				$inaccessible_names[] = $name;
			}
		}
		
		//check
		if ($inaccessible_names) {
			throw new Exceptions\Inaccessible([$this, $inaccessible_names, $scope_class]);
		}
		
		//return
		return $this;
	}
	
	/**
	 * Validate read.
	 * 
	 * @param string[] $names
	 * The names to validate.
	 * 
	 * @param string|null $scope_class
	 * The scope class to use.
	 * 
	 * @throws \Dracodeum\Kit\Managers\PropertiesV2\Exceptions\Unreadable
	 * 
	 * @return $this
	 * This instance, for chaining purposes.
	 */
	private function validateRead(array $names, ?string $scope_class)
	{
		//process
		$unreadable_names = [];
		foreach ($names as $name) {
			if (!isset($this->properties[$name]) || !$this->properties[$name]->isReadable($scope_class)) {
				$unreadable_names[] = $name;
			}
		}
		
		//check
		if ($unreadable_names) {
			throw new Exceptions\Unreadable([$this, $unreadable_names, $scope_class]);
		}
		
		//return
		return $this;
	}
	
	/**
	 * Validate write.
	 * 
	 * @param string[] $names
	 * The names to validate.
	 * 
	 * @param string|null $scope_class
	 * The scope class to use.
	 * 
	 * @param bool $initializing
	 * Whether or not the call is being performed in the context of an initialization.
	 * 
	 * @throws \Dracodeum\Kit\Managers\PropertiesV2\Exceptions\Unwriteable
	 * 
	 * @return $this
	 * This instance, for chaining purposes.
	 */
	private function validateWrite(array $names, ?string $scope_class, bool $initializing = false)
	{
		//process
		$unwriteable_names = [];
		foreach ($names as $name) {
			if (
				!isset($this->properties[$name]) || 
				!$this->properties[$name]->isWriteable($scope_class, $initializing)
			) {
				$unwriteable_names[] = $name;
			}
		}
		
		//check
		if ($unwriteable_names) {
			throw new Exceptions\Unwriteable([$this, $unwriteable_names, $scope_class]);
		}
		
		//return
		return $this;
	}
	
	/**
	 * Validate unset.
	 * 
	 * @param string[] $names
	 * The names to validate.
	 * 
	 * @param string|null $scope_class
	 * The scope class to use.
	 * 
	 * @throws \Dracodeum\Kit\Managers\PropertiesV2\Exceptions\Ununsettable
	 * 
	 * @return $this
	 * This instance, for chaining purposes.
	 */
	private function validateUnset(array $names, ?string $scope_class)
	{
		//process
		$ununsettable_names = [];
		foreach ($names as $name) {
			if (
				!isset($this->properties[$name]) || $this->properties[$name]->isRequired() || 
				!$this->properties[$name]->isWriteable($scope_class)
			) {
				$ununsettable_names[] = $name;
			}
		}
		
		//check
		if ($ununsettable_names) {
			throw new Exceptions\Ununsettable([$this, $ununsettable_names, $scope_class]);
		}
		
		//return
		return $this;
	}
	
	/**
	 * Check if a property with a given name is initialized with a value.
	 * 
	 * @param string $name
	 * The name to check with.
	 * 
	 * @return bool
	 * Boolean `true` if the property with the given name is initialized with a value.
	 */
	private function isValueInitialized(string $name): bool
	{
		return isset($this->properties[$name]) && isset($this->values_flags[$name]) && (
			$this->properties[$name]->hasDefaultValue() ||
			UByte::hasFlag($this->values_flags[$name], self::VALUE_FLAG_SET)
		);
	}
	
	/**
	 * Validate initialized.
	 * 
	 * @param string[] $names
	 * The names to validate.
	 * 
	 * @throws \Dracodeum\Kit\Managers\PropertiesV2\Exceptions\Uninitialized
	 * 
	 * @return $this
	 * This instance, for chaining purposes.
	 */
	private function validateInitialized(array $names)
	{
		//process
		$uninitialized_names = [];
		foreach ($names as $name) {
			if (!$this->isValueInitialized($name)) {
				$uninitialized_names[] = $name;
			}
		}
		
		//check
		if ($uninitialized_names) {
			throw new Exceptions\Uninitialized([$this, $uninitialized_names]);
		}
		
		//return
		return $this;
	}
	
	/**
	 * Get values.
	 * 
	 * @param string[] $names
	 * The names to get values from.
	 * 
	 * @param string|null $scope_class
	 * The scope class to use.
	 * 
	 * @throws \Dracodeum\Kit\Managers\PropertiesV2\Exceptions\Undefined
	 * @throws \Dracodeum\Kit\Managers\PropertiesV2\Exceptions\Inaccessible
	 * @throws \Dracodeum\Kit\Managers\PropertiesV2\Exceptions\Unreadable
	 * @throws \Dracodeum\Kit\Managers\PropertiesV2\Exceptions\Uninitialized
	 * @throws \Dracodeum\Kit\Managers\PropertiesV2\Exceptions\Invalid
	 * 
	 * @return array
	 * The values, as a set of `name => value` pairs.
	 */
	private function getValues(array $names, ?string $scope_class): array
	{
		//check
		if (!$this->initialized) {
			UCall::halt([
				'hint_message' => "This manager must be initialized first in order to be able to call this method.",
				'stack_offset' => 1
			]);
		} elseif (!$names) {
			return [];
		}
		
		//validate
		$this
			->validateDefined($names)
			->validateAccess($names, $scope_class)
			->validateRead($names, $scope_class)
			->validateInitialized($names)
		;
		
		//process
		$values = $errors = $errors_values = [];
		foreach ($names as $name) {
			//initialize
			$value = $this->values[$name];
			$flags = $this->values_flags[$name];
			$property = $this->properties[$name];
			
			//default
			if (!UByte::hasFlag($flags, self::VALUE_FLAG_SET)) {
				$value = $property->getDefaultValue();
				UByte::setFlag($flags, self::VALUE_FLAG_SET | self::VALUE_FLAG_DIRTY);
			}
			
			//dirty
			if (
				UByte::hasFlag($flags, self::VALUE_FLAG_DIRTY) || 
				UByte::hasFlag($flags, self::VALUE_FLAG_TYPED) !== $property->hasType()
			) {
				$error = $property->processValue($value);
				if ($error !== null) {
					$errors[$name] = $error;
					$errors_values[$name] = $value;
				} else {
					UByte::unsetFlag($flags, self::VALUE_FLAG_DIRTY);
					UByte::updateFlag($flags, self::VALUE_FLAG_TYPED, $property->hasType());
					$this->values[$name] = $value;
					$this->values_flags[$name] = $flags;
				}
			}
			
			//finalize
			$values[$name] = $value;
		}
		
		//errors
		if ($errors) {
			throw new Exceptions\Invalid([$this, $errors_values, $errors]);
		}
		
		//return
		return $values;
	}
	
	/**
	 * Set values.
	 * 
	 * @param array $values
	 * The values to set, as a set of `name => value` pairs.
	 * 
	 * @param string|null $scope_class
	 * The scope class to use.
	 * 
	 * @param bool $initializing
	 * Whether or not the call is being performed in the context of an initialization.
	 * 
	 * @throws \Dracodeum\Kit\Managers\PropertiesV2\Exceptions\Undefined
	 * @throws \Dracodeum\Kit\Managers\PropertiesV2\Exceptions\Inaccessible
	 * @throws \Dracodeum\Kit\Managers\PropertiesV2\Exceptions\Unwriteable
	 * @throws \Dracodeum\Kit\Managers\PropertiesV2\Exceptions\Invalid
	 */
	private function setValues(array $values, ?string $scope_class, bool $initializing = false): void
	{
		//initialize
		if ($initializing) {
			//initialize
			$property_names = array_keys($this->properties);
			$this->values = array_fill_keys($property_names, null);
			$this->values_flags = array_fill_keys($property_names, 0x0);
			
			//reset
			foreach ($this->properties as $property) {
				$property->reset($this->owner);
			}
			
		} elseif (!$this->initialized) {
			UCall::halt([
				'hint_message' => "This manager must be initialized first in order to be able to call this method.",
				'stack_offset' => 1
			]);
		}
		
		//check
		if (!$values) {
			return;
		}
		
		//validate
		$names = array_keys($values);
		$this
			->validateDefined($names)
			->validateAccess($names, $scope_class)
			->validateWrite($names, $scope_class, $initializing)
		;
		
		//process
		$errors = [];
		$values_flags = array_intersect_key($this->values_flags, $values);
		foreach ($values as $name => &$value) {
			//initialize
			$property = $this->properties[$name];
			UByte::setFlag($values_flags[$name], self::VALUE_FLAG_SET);
			
			//typed
			UByte::updateFlag($values_flags[$name], self::VALUE_FLAG_TYPED, $property->hasType());
			
			//lazy
			if ($property->isLazy()) {
				UByte::setFlag($values_flags[$name], self::VALUE_FLAG_DIRTY);
				continue;
			}
			
			//process
			$error = $property->processValue($value);
			if ($error !== null) {
				$errors[$name] = $error;
			}
		}
		unset($value);
		
		//errors
		if ($errors) {
			throw new Exceptions\Invalid([$this, array_intersect_key($values, $errors), $errors]);
		}
		
		//finalize
		$this->values = array_replace($this->values, $values);
		$this->values_flags = array_replace($this->values_flags, $values_flags);
	}
	
	/**
	 * Unset values.
	 * 
	 * @param string[] $names
	 * The names to unset values from.
	 * 
	 * @param string|null $scope_class
	 * The scope class to use.
	 * 
	 * @throws \Dracodeum\Kit\Managers\PropertiesV2\Exceptions\Undefined
	 * @throws \Dracodeum\Kit\Managers\PropertiesV2\Exceptions\Inaccessible
	 * @throws \Dracodeum\Kit\Managers\PropertiesV2\Exceptions\Ununsettable
	 */
	private function unsetValues(array $names, ?string $scope_class): void
	{
		//check
		if (!$this->initialized) {
			UCall::halt([
				'hint_message' => "This manager must be initialized first in order to be able to call this method.",
				'stack_offset' => 1
			]);
		} elseif (!$names) {
			return;
		}
		
		//validate
		$this->validateDefined($names)->validateAccess($names, $scope_class)->validateUnset($names, $scope_class);
		
		//process
		foreach ($names as $name) {
			$this->values[$name] = null;
			$this->values_flags[$name] = 0x0;
		}
	}
}
