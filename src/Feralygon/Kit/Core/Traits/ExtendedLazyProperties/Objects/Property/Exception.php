<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Traits\ExtendedLazyProperties\Objects\Property;

use Feralygon\Kit\Core;
use Feralygon\Kit\Core\Traits\ExtendedLazyProperties\Objects\Property;
use Feralygon\Kit\Core\Utilities\Type as UType;

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
	//Implemented public static methods
	/** {@inheritdoc} */
	public static function getRequiredPropertyNames() : array
	{
		return ['property'];
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function evaluateProperty(string $name, &$value) : ?bool
	{
		switch ($name) {
			case 'property':
				return is_object($value) && UType::isA($value, Property::class);
		}
		return null;
	}
}
