<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Attributes\Property;

use Dracodeum\Kit\Managers\PropertiesV2\Interfaces\Attribute\Property\PropertyInitializer as IPropertyInitializer;
use Dracodeum\Kit\Managers\PropertiesV2\Property;
use Attribute;

/** This attribute defines the property mode of operation to be only written to (write-only). */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class write implements IPropertyInitializer
{
	//Final public magic methods
	/**
	 * Instantiate class.
	 * 
	 * @param bool $affect_subclasses
	 * Enforce the mode of operation internally for subclasses as well.
	 */
	final public function __construct(private bool $affect_subclasses = false) {}
	
	
	
	//Implemented final public methods (Dracodeum\Kit\Managers\PropertiesV2\Interfaces\Attribute\Property\PropertyInitializer)
	/** {@inheritdoc} */
	final public function initializeProperty(Property $property): void
	{
		$property->setMode('w', $this->affect_subclasses);
	}
}
