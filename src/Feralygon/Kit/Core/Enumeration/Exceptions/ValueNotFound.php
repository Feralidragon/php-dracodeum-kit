<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Enumeration\Exceptions;

use Feralygon\Kit\Core\Enumeration\Exception;

/**
 * Core enumeration value not found exception class.
 * 
 * This exception is thrown from an enumeration whenever a given value is not found.
 * 
 * @since 1.0.0
 * @property-read int|float|string $value <p>The value.</p>
 */
class ValueNotFound extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Value {{value}} not found in enumeration {{enumeration}}.";
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function buildProperties() : void
	{
		//parent
		parent::buildProperties();
		
		//properties
		$this->addProperty('value')
			->setEvaluator(function (&$value) : bool {
				return is_int($value) || is_float($value) || is_string($value);
			})
			->setAsRequired()
		;
	}
}
