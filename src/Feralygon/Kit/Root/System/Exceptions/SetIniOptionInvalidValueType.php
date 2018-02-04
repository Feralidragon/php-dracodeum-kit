<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Root\System\Exceptions;

use Feralygon\Kit\Core\Utilities\Type as UType;

/**
 * Root system <code>setIniOption</code> method invalid value type exception class.
 * 
 * This exception is thrown from the system <code>setIniOption</code> method whenever a given value type is invalid.
 * 
 * @since 1.0.0
 * @property-read string $name <p>The option name.</p>
 * @property-read mixed $value <p>The option value.</p>
 * @property-read string $type <p>The option value type.</p>
 */
class SetIniOptionInvalidValueType extends SetIniOption
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Invalid option value type {{type}} for {{name}}.\n" . 
			"HINT: Only a string, integer, float, boolean or null value is allowed.";
	}
	
	
	
	//Implemented public static methods
	/** {@inheritdoc} */
	public static function getRequiredPropertyNames() : array
	{
		return ['name', 'value', 'type'];
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function evaluateProperty(string $name, &$value) : ?bool
	{
		switch ($name) {
			case 'name':
				return UType::evaluateString($value);
			case 'value':
				return true;
			case 'type':
				return UType::evaluateString($value, true);
		}
		return null;
	}
}
