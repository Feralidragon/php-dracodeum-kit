<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Utilities\Time\Exceptions\Mperiod;

use Dracodeum\Kit\Utilities\Time\Exceptions\Mperiod as Exception;

/**
 * This exception is thrown from the time utility <code>mperiod</code> method whenever a given period is invalid.
 * 
 * @property-read string $period [coercive]
 * <p>The period.</p>
 */
class InvalidPeriod extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		return "Invalid period {{period}}.";
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Exception\Traits\PropertiesLoader)
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		$this->addProperty('period')->setAsString();
	}
}
