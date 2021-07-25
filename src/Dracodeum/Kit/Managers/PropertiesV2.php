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
		$owner_class = $this->owner::class;
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
	}
}
