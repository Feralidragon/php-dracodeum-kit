<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Attributes\Property;

use Dracodeum\Kit\Managers\PropertiesV2\Interfaces\Attribute\Property\Initializer as IPropertyInitializer;
use Dracodeum\Kit\Managers\PropertiesV2\Property;
use Attribute;

/** This attribute defines the property mode of operation. */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class mode implements IPropertyInitializer
{
	//Final public magic methods
	/**
	 * Instantiate class.
	 * 
	 * @param string $mode
	 * The mode to instantiate with, as one of the following:
	 * - `r` : allow the property to be only strictly read from (exclusive read-only), not allowing to be given during 
	 * initialization;
	 * - `r+` : allow the property to be only read from (read-only), but allowing to be given during initialization;
	 * - `rw` : allow the property to be both read from and written to (read-write);
	 * - `w` : allow the property to be only written to (write-only);
	 * - `w-` : allow the property to be only written to, but only once during initialization (write-once).
	 * 
	 * @param bool $affect_subclasses
	 * Enforce the mode of operation internally for subclasses as well.
	 */
	final public function __construct(private string $mode, private bool $affect_subclasses = false) {}
	
	
	
	//Implemented final public methods (Dracodeum\Kit\Managers\PropertiesV2\Interfaces\PropertyInitializer)
	/** {@inheritdoc} */
	final public function initializeProperty(Property $property): void
	{
		$property->setMode($this->mode, $this->affect_subclasses);
	}
}
