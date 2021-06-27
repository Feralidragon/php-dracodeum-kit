<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Managers;

use Dracodeum\Kit\Manager;
use Dracodeum\Kit\Managers\PropertiesV2\Property;
use Dracodeum\Kit\Managers\PropertiesV2\Interfaces\PropertyInitializer as IPropertyInitializer;
use ReflectionClass;

/**
 * This manager handles and extends the properties of an object class with the following functionality:
 * - extended coercive and strict typification, using type components and mutators;
 * - modes of operation, such as read-write, read-only, write-only, and others;
 * - aliasing;
 * - custom getters and setters;
 * - lazy type coercion and validation;
 * - value persistence.
 * 
 * Only non-static and non-private properties are supported and affected.
 */
final class PropertiesV2 extends Manager
{
	//Private properties
	private object $owner;
	
	/** @var \Dracodeum\Kit\Managers\PropertiesV2\Property[] */
	private array $properties;
	
	
	
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
	
	
	
	//Private methods
	/**
	 * Initialize properties.
	 * 
	 * @return void
	 */
	private function initializeProperties(): void
	{
		//initialize
		static $classes_properties = [];
		$owner_class = get_class($this->owner);
		
		//check
		if (isset($classes_properties[$owner_class])) {
			$this->properties = $classes_properties[$owner_class];
			return;
		}
		
		//classes
		$classes = [];
		for ($class = $owner_class; $class !== false; $class = get_parent_class($class)) {
			$classes[] = $class;
		}
		$classes = array_reverse($classes);
		
		//properties
		$parent_class = null;
		$new_properties = [];
		foreach ($classes as $class) {
			if (!isset($classes_properties[$class])) {
				$properties = $parent_properties_map = [];
				foreach ((new ReflectionClass($class))->getProperties() as $r_property) {
					//check
					if ($r_property->isStatic() || $r_property->isPrivate()) {
						continue;
					}
					
					//properties
					$p_name = $r_property->getName();
					if ($parent_class !== null && isset($classes_properties[$parent_class][$p_name])) {
						$parent_properties_map[$p_name] = true;
					} else {
						$properties[$p_name] = $new_properties[$class][$p_name] = new Property($r_property);
					}
					
					//parent properties
					$parent_properties = $parent_properties_map
						? array_intersect_key($classes_properties[$parent_class], $parent_properties_map)
						: [];
					
					//finalize
					$classes_properties[$class] = $parent_properties + $properties;
					unset($parent_properties);
				}
				unset($properties, $parent_properties_map);
			}
			$parent_class = $class;
		}
		
		//new properties
		foreach ($new_properties as $properties) {
			foreach ($properties as $p_name => $property) {
				//reflection
				$reflection = $property->getReflection();
				
				//attributes
				$has_attributes = false;
				foreach ($reflection->getAttributes() as $r_attribute) {
					$attribute = $r_attribute->newInstance();
					if ($attribute instanceof IPropertyInitializer) {
						$has_attributes = true;
						$attribute->initializeProperty($property);
					}
				}
				
				//cleanup
				if (!$has_attributes) {
					foreach ($classes as $class) {
						unset($classes_properties[$class][$p_name]);
					}
				}
			}
		}
		
		//finalize
		$this->properties = $classes_properties[$owner_class];
	}
}
