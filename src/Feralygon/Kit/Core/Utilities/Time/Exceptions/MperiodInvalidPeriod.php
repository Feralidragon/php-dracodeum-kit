<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Utilities\Time\Exceptions;

use Feralygon\Kit\Core\Utilities\Type as UType;

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
	
	
	
	//Implemented public static methods
	/** {@inheritdoc} */
	public static function getRequiredPropertyNames() : array
	{
		return ['period'];
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function evaluateProperty(string $name, &$value) : ?bool
	{
		switch ($name) {
			case 'period':
				return UType::evaluateString($value);
		}
		return null;
	}
}
