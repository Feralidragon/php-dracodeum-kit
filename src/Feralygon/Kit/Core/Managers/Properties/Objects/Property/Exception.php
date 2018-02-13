<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Managers\Properties\Objects\Property;

use Feralygon\Kit\Core;
use Feralygon\Kit\Core\Managers\Properties\Objects\Property;

/**
 * Core properties manager property object exception class.
 * 
 * @since 1.0.0
 * @property-read \Feralygon\Kit\Core\Managers\Properties\Objects\Property $property <p>The property instance.</p>
 * @see \Feralygon\Kit\Core\Managers\Properties\Objects\Property
 */
abstract class Exception extends Core\Exception
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function buildProperties() : void
	{
		$this->addProperty('property')->setAsStrictObject(Property::class)->setAsRequired();
	}
}
