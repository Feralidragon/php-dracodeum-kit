<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Utilities\Time\Exceptions\Mperiod;

use Dracodeum\Kit\Utilities\Time\Exceptions\Mperiod as Exception;

/**
 * @property-read string $period
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
