<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Utilities\Text\Exceptions;

use Feralygon\Kit\Core\Utilities\Type as UType;

/**
 * Core text utility <code>indentate</code> method invalid level exception class.
 * 
 * This exception is thrown from the text utility <code>indentate</code> method whenever a given level is invalid.
 * 
 * @since 1.0.0
 * @property-read int $level <p>The level.</p>
 */
class IndentateInvalidLevel extends Indentate
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Invalid level {{level}}.\n" . 
			"HINT: Only a value greater than or equal to 0 is allowed.";
	}
	
	
	
	//Implemented public static methods
	/** {@inheritdoc} */
	public static function getRequiredPropertyNames() : array
	{
		return ['level'];
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function evaluateProperty(string $name, &$value) : ?bool
	{
		switch ($name) {
			case 'level':
				return UType::evaluateInteger($value);
		}
		return null;
	}
}
