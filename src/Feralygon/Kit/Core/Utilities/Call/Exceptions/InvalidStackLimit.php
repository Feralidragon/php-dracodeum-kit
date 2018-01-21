<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Utilities\Call\Exceptions;

use Feralygon\Kit\Core\Utilities\Call\Exception;
use Feralygon\Kit\Core\Utilities\Type as UType;

/**
 * Core call utility invalid stack limit exception class.
 * 
 * This exception is thrown from the call utility whenever a given stack limit is invalid.
 * 
 * @since 1.0.0
 * @property-read int $limit <p>The limit.</p>
 */
class InvalidStackLimit extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Invalid stack limit {{limit}}.\n" . 
			"HINT: Only null or a value greater than 0 is allowed.";
	}
	
	
	
	//Implemented public static methods
	/** {@inheritdoc} */
	public static function getRequiredPropertyNames() : array
	{
		return ['limit'];
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function evaluateProperty(string $name, &$value) : ?bool
	{
		switch ($name) {
			case 'limit':
				return UType::evaluateInteger($value);
		}
		return null;
	}
}
