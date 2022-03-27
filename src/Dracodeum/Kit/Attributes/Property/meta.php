<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Attributes\Property;

use Dracodeum\Kit\Managers\PropertiesV2\Interfaces\Attribute\Property\PropertyInitializer as IPropertyInitializer;
use Dracodeum\Kit\Managers\PropertiesV2\Property;
use Attribute;

/** This attribute defines a meta value with a given name for the property. */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class meta implements IPropertyInitializer
{
	//Final public magic methods
	/**
	 * Instantiate class.
	 * 
	 * @param string $name
	 * The name to instantiate with.
	 * 
	 * @param mixed $value
	 * The value to instantiate with.
	 */
	final public function __construct(private string $name, private mixed $value) {}
	
	
	
	//Implemented final public methods (Dracodeum\Kit\Managers\PropertiesV2\Interfaces\Attribute\Property\PropertyInitializer)
	/** {@inheritdoc} */
	final public function initializeProperty(Property $property): void
	{
		$property->setMetaValue($this->name, $this->value);
	}
}
