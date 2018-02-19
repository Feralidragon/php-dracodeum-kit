<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Managers\Properties\Objects\Property;

use Feralygon\Kit\Exception as KitException;
use Feralygon\Kit\Managers\Properties\Objects\Property;

/**
 * Properties manager property object exception class.
 * 
 * @since 1.0.0
 * @property-read \Feralygon\Kit\Managers\Properties\Objects\Property $property <p>The property instance.</p>
 * @see \Feralygon\Kit\Managers\Properties\Objects\Property
 */
abstract class Exception extends KitException
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function buildProperties() : void
	{
		$this->addProperty('property')->setAsStrictObject(Property::class)->setAsRequired();
	}
}
