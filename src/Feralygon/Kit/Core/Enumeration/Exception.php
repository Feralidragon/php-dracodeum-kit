<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Enumeration;

use Feralygon\Kit\Core;
use Feralygon\Kit\Core\Enumeration;
use Feralygon\Kit\Core\Utilities\Type as UType;

/**
 * Core enumeration exception class.
 * 
 * @since 1.0.0
 * @property-read string $enumeration <p>The enumeration class.</p>
 * @see \Feralygon\Kit\Core\Enumeration
 */
abstract class Exception extends Core\Exception
{
	//Implemented public static methods
	/** {@inheritdoc} */
	public static function getRequiredPropertyNames() : array
	{
		return ['enumeration'];
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function evaluateProperty(string $name, &$value) : ?bool
	{
		switch ($name) {
			case 'enumeration':
				return UType::evaluateClass($value, Enumeration::class);
		}
		return null;
	}
}
