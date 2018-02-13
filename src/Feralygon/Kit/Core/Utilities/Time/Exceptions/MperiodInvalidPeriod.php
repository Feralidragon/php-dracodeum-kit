<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Utilities\Time\Exceptions;

/**
 * Core time utility <code>mperiod</code> method invalid period exception class.
 * 
 * This exception is thrown from the time utility <code>mperiod</code> method whenever a given period is invalid.
 * 
 * @since 1.0.0
 * @property-read string $period <p>The period.</p>
 */
class MperiodInvalidPeriod extends Mperiod
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Invalid period {{period}}.";
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function buildProperties() : void
	{
		$this->addProperty('period')->setAsString()->setAsRequired();
	}
}
