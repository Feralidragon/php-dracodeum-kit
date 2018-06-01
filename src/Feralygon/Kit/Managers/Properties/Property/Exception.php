<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Managers\Properties\Property;

use Feralygon\Kit\Exception as KitException;
use Feralygon\Kit\Managers\Properties\Property;

/**
 * @since 1.0.0
 * @property-read \Feralygon\Kit\Managers\Properties\Property $property
 * <p>The property instance.</p>
 * @see \Feralygon\Kit\Managers\Properties\Property
 */
abstract class Exception extends KitException
{
	//Implemented protected methods (Feralygon\Kit\Exception\Traits\Properties)
	/** {@inheritdoc} */
	protected function loadProperties() : void
	{
		$this->addProperty('property')->setAsStrictObject(Property::class)->setAsRequired();
	}
}
