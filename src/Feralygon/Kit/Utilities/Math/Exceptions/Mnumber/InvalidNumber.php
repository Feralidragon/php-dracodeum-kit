<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Utilities\Math\Exceptions\Mnumber;

use Feralygon\Kit\Utilities\Math\Exceptions\Mnumber as Exception;

/**
 * This exception is thrown from the math utility <code>mnumber</code> method whenever a given number is invalid.
 * 
 * @property-read string $number [coercive]
 * <p>The number.</p>
 */
class InvalidNumber extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		return "Invalid number {{number}}.";
	}
	
	
	
	//Implemented protected methods (Feralygon\Kit\Exception\Traits\PropertiesLoader)
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		$this->addProperty('number')->setAsString();
	}
}
