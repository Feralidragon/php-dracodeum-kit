<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Utilities\Byte\Exceptions\Mvalue;

use Feralygon\Kit\Utilities\Byte\Exceptions\Mvalue as Exception;

/**
 * This exception is thrown from the byte utility <code>mvalue</code> method whenever a given value is invalid.
 * 
 * @since 1.0.0
 * @property-read string $value
 * <p>The value.</p>
 */
class InvalidValue extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Invalid value {{value}}.";
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function loadProperties() : void
	{
		$this->addProperty('value')->setAsString()->setAsRequired();
	}
}
