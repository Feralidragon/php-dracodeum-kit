<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Attributes\Property;

use Dracodeum\Kit\Managers\PropertiesV2\Interfaces\PropertyPostInitializer as IPropertyPostInitializer;
use Dracodeum\Kit\Managers\PropertiesV2\Property;
use Attribute;

/**
 * This attribute adds a mutator to the property type as a `Dracodeum\Kit\Components\Type\Components\Mutator` component, 
 * using a given prototype, as a class or name, and a set of properties.  
 * If no property type is set, then this attribute has no effect.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class mutator implements IPropertyPostInitializer
{
	//Private properties
	private string $prototype;
	
	private array $properties;
	
	
	
	//Final public magic methods
	/**
	 * Instantiate class.
	 * 
	 * @param string $prototype
	 * The prototype class or name to instantiate with.
	 * 
	 * @param mixed $properties
	 * The properties to instantiate with.
	 */
	final public function __construct(string $prototype, ...$properties)
	{
		$this->prototype = $prototype;
		$this->properties = $properties;
	}
	
	
	
	//Implemented final public methods (Dracodeum\Kit\Managers\PropertiesV2\Interfaces\PropertyPostInitializer)
	/** {@inheritdoc} */
	final public function postInitializeProperty(Property $property): void
	{
		if ($property->hasType()) {
			$property->getType()->addMutator($this->prototype, $this->properties);
		}
	}
}
