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
use ReflectionClass;

/**
 * This manager handles and extends the properties of an object class with the following functionality:
 * - extended coercive and strict typification, using type components and mutators;
 * - modes of operation, such as read-write, read-only, write-only, and others;
 * - name aliasing;
 * - custom getters and setters;
 * - lazy type coercion and validation;
 * - value persistence.
 * 
 * Only non-static and non-private properties are supported and affected.
 * 
 * @see \Dracodeum\Kit\Managers\PropertiesV2\Interfaces\PropertyInitializer
 * @see \Dracodeum\Kit\Managers\PropertiesV2\Interfaces\PropertyPostInitializer
 */
final class PropertiesV2 extends Manager
{
	//Private properties
	private object $owner;
	
	/** @var \Dracodeum\Kit\Managers\PropertiesV2\Property[] */
	private array $properties;
	
	private array $required_map;
	
	private array $values = [];
	
	
	
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
	 * 
	 * @return void
	 */
	private function setValues(array $values, ?string $scope_class, bool $initializing = false): void
	{
		//initialize
		$names = array_keys($values);
		$this->validateUndefined($names)->validateAccess($names, $scope_class);
		
		
		//TODO
	}
}
