<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Managers;

use Dracodeum\Kit\Manager;
use Dracodeum\Kit\Managers\PropertiesV2\Property;
use Dracodeum\Kit\Managers\PropertiesV2\Interfaces\{
	PropertyInitializer as IPropertyInitializer,
	PropertyPostInitializer as IPropertyPostInitializer
};
use Dracodeum\Kit\Managers\PropertiesV2\Exceptions;
use Dracodeum\Kit\Utilities\Byte as UByte;
use ReflectionClass;

/**
 * This manager handles and extends the properties of an object class with the following functionality:
 * - extended coercive and strict typification, using type components and mutators;
 * - modes of operation, such as read-write, read-only, write-only, and others;
 * - name aliasing;
 * - custom getters and setters;
 * - lazy type validation and coercion;
 * - value persistence.
 * 
 * Only non-static and non-private properties are supported and affected.
 * 
 * @see \Dracodeum\Kit\Managers\PropertiesV2\Interfaces\PropertyInitializer
 * @see \Dracodeum\Kit\Managers\PropertiesV2\Interfaces\PropertyPostInitializer
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
	
	/** @var \Dracodeum\Kit\Managers\PropertiesV2\Property[] */
	private array $properties;
	
	private array $required_map;
	
	private array $values = [];
	
	/** @var int[] */
	private array $values_flags = [];
	
	
	
	//Final public magic methods
	/**
	 * Instantiate class.
	 * 
	 * @param object $owner
	 * The owner object to instantiate with.
	 */
	final public function __construct(object $owner)
	{
		$this->owner = $owner;
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
	 * @return void
	 */
	final public function initialize(array $values, ?string $scope_class = null): void
	{
		$this->processRequiredValues($values)->setValues($values, $scope_class, true);
	}
	
	
	
	//Private methods
	/**
	 * Initialize properties.
	 * 
	 * @return void
	 */
	private function initializeProperties(): void
	{
		//initialize
		$owner_class = $this->owner::class;
		static $classes_properties = [], $classes_required_maps = [];
		if (!isset($classes_properties[$owner_class])) {
			//classes
			$classes = [];
			for ($class = $owner_class; $class !== false; $class = get_parent_class($class)) {
				$classes[] = $class;
			}
			$classes = array_reverse($classes);
			
			//properties
			$parent_class = null;
			foreach ($classes as $class) {
				//class
				if (!isset($classes_properties[$class])) {
					//initialize
					$properties = $properties_post_attributes = [];
					$parent_properties = $parent_class !== null ? $classes_properties[$parent_class] : [];
					
					//properties
					foreach ((new ReflectionClass($class))->getProperties() as $r_property) {
						//initialize
						$p_name = $r_property->getName();
						
						//check
						if ($r_property->isStatic() || $r_property->isPrivate() || isset($parent_properties[$p_name])) {
							continue;
						}
						
						//property
						$property = $properties[$p_name] = new Property($r_property);
						
						//attributes
						foreach ($r_property->getAttributes() as $r_attribute) {
							$attribute = $r_attribute->newInstance();
							if ($attribute instanceof IPropertyInitializer) {
								$attribute->initializeProperty($property);
							}
							if ($attribute instanceof IPropertyPostInitializer) {
								$properties_post_attributes[$p_name][] = $attribute;
							}
						}
					}
					
					//class properties
					$classes_properties[$class] = $parent_properties + $properties;
					
					//sort
					uasort($classes_properties[$class], function (Property $property1, Property $property2): int {
						return (int)$property2->isRequired() - (int)$property1->isRequired();
					});
					
					//required mapping
					$i = 0;
					$classes_required_maps[$class] = [];
					foreach ($classes_properties[$class] as $p_name => $property) {
						if ($property->isRequired()) {
							$classes_required_maps[$class][$p_name] = $i++;
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
					unset($properties, $properties_post_attributes, $parent_properties);
				}
				
				//parent class
				$parent_class = $class;
			}
		}
		
		//finalize
		$this->properties = $classes_properties[$owner_class];
		$this->required_map = $classes_required_maps[$owner_class];
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
		$names_map = array_flip($this->required_map);
		foreach ($values as $i => $value) {
			if (is_int($i) && isset($names_map[$i])) {
				$values[$names_map[$i]] = $value;
				unset($values[$i]);
			}
		}
		
		//missing
		$missing_names = array_keys(array_diff_key($this->required_map, $values));
		if ($missing_names) {
			throw new Exceptions\Missing([$this, $missing_names]);
		}
		
		//return
		return $this;
	}
	
	/**
	 * Validate undefined.
	 * 
	 * @param string[] $names
	 * The names to validate.
	 * 
	 * @throws \Dracodeum\Kit\Managers\PropertiesV2\Exceptions\Undefined
	 * 
	 * @return $this
	 * This instance, for chaining purposes.
	 */
	private function validateUndefined(array $names)
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
	 * 
	 * @return void
	 */
	private function setValues(array $values, ?string $scope_class, bool $initializing = false): void
	{
		//initialize
		if (!$values && !$initializing) {
			return;
		}
		$names = array_keys($values);
		
		//validate
		$this
			->validateUndefined($names)
			->validateAccess($names, $scope_class)
			->validateWrite($names, $scope_class, $initializing)
		;
		
		//initializing
		if ($initializing) {
			//initialize
			$property_names = array_keys($this->properties);
			$this->values = array_fill_keys($property_names, null);
			$this->values_flags = array_fill_keys($property_names, 0x0);
			
			//reset
			foreach ($this->properties as $property) {
				$property->reset($this->owner);
			}
		}
		
		//check
		if (!$values) {
			return;
		}
		
		//process
		$errors = [];
		$values_flags = array_fill_keys($names, self::VALUE_FLAG_SET);
		foreach ($values as $name => &$value) {
			//initialize
			$property = $this->properties[$name];
			
			//typed
			UByte::updateFlag($values_flags[$name], self::VALUE_FLAG_TYPED, $property->hasType());
			
			//lazy
			if ($property->isLazy()) {
				UByte::setFlag($values_flags[$name], self::VALUE_FLAG_DIRTY);
				continue;
			}
			
			//process
			$error = $property->processValue($this->owner, $value);
			if ($error !== null) {
				$errors[$name] = $error;
			}
		}
		unset($value);
		
		//errors
		if ($errors) {
			throw new Exceptions\Invalid([$this, array_intersect_key($values, $errors), $errors]);
		}
		
		//values
		$this->values = array_replace($this->values, $values);
		foreach ($values_flags as $name => $flag) {
			UByte::setFlag($this->values_flags[$name], $flag);
		}
	}
}
