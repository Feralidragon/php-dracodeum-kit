<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Traits\ExtendedLazyProperties\Objects\Property;

use Feralygon\Kit\Core;
use Feralygon\Kit\Core\Traits\ExtendedLazyProperties\Objects\Property;

/**
 * Core extended lazy properties trait property object exception class.
 * 
 * @since 1.0.0
 * @property-read \Feralygon\Kit\Core\Traits\ExtendedLazyProperties\Objects\Property $property 
 * <p>The property instance.</p>
 * @see \Feralygon\Kit\Core\Traits\ExtendedLazyProperties\Objects\Property
 */
abstract class Exception extends Core\Exception
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function loadProperties() : void
	{
		$this->addStrictObjectProperty('property', true, Property::class);
	}
}
