<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Utilities\Text\Exceptions;

use Feralygon\Kit\Core\Utilities\Type as UType;

/**
 * Core text utility <code>truncate</code> method invalid length exception class.
 * 
 * This exception is thrown from the text utility <code>truncate</code> method whenever a given length is invalid.
 * 
 * @since 1.0.0
 * @property-read int $length <p>The length.</p>
 */
class TruncateInvalidLength extends Truncate
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Invalid length {{length}}.\n" . 
			"HINT: Only a value greater than or equal to 0 is allowed.";
	}
	
	
	
	//Implemented public static methods
	/** {@inheritdoc} */
	public static function getRequiredPropertyNames() : array
	{
		return ['length'];
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function evaluateProperty(string $name, &$value) : ?bool
	{
		switch ($name) {
			case 'length':
				return UType::evaluateInteger($value);
		}
		return null;
	}
}
