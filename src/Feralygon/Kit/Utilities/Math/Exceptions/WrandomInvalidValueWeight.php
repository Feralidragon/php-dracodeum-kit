<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Utilities\Math\Exceptions;

/**
 * This exception is thrown from the math utility <code>wrandom</code> method whenever a given weight is invalid 
 * for a given value.
 * 
 * @since 1.0.0
 * @property-read int|string $value
 * <p>The value.</p>
 * @property-read mixed $weight
 * <p>The weight.</p>
 */
class WrandomInvalidValueWeight extends Wrandom
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Invalid weight {{weight}} for value {{value}}.\n" . 
			"HINT: Only a weight greater than or equal to 0 is allowed.";
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function loadProperties() : void
	{
		$this->addProperty('value')
			->setEvaluator(function (&$value) : bool {
				return is_int($value) || is_string($value);
			})
			->setAsRequired()
		;
		$this->addProperty('weight')->setAsRequired();
	}
}
