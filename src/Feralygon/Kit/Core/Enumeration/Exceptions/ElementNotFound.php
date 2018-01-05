<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Enumeration\Exceptions;

use Feralygon\Kit\Core\Enumeration;
use Feralygon\Kit\Core\Enumeration\Exception;
use Feralygon\Kit\Core\Utilities\Type as UType;

/**
 * Core enumeration element not found exception class.
 * 
 * This exception is thrown from an enumeration whenever a given element is not found.
 * 
 * @since 1.0.0
 * @property-read string $enumeration <p>The enumeration class.</p>
 * @property-read mixed $element <p>The element.</p>
 */
class ElementNotFound extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Element {{element}} not found in enumeration {{enumeration}}.";
	}
	
	
	
	//Implemented public static methods
	/** {@inheritdoc} */
	public static function getRequiredPropertyNames() : array
	{
		return ['enumeration', 'element'];
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function evaluateProperty(string $name, &$value) : ?bool
	{
		switch ($name) {
			case 'enumeration':
				return UType::evaluateClass($value, Enumeration::class);
			case 'element':
				return true;
		}
		return null;
	}
}
