<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Enumeration\Exceptions;

use Dracodeum\Kit\Enumeration\Exception;

/**
 * @property-read int|float|string $value
 * <p>The value.</p>
 */
class ValueNotFound extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		return "Value {{value}} not found in enumeration {{enumeration}}.";
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		//parent
		parent::loadProperties();
		
		//properties
		$this->addProperty('value')
			->addEvaluator(function (&$value): bool {
				return is_int($value) || is_float($value) || is_string($value);
			})
		;
	}
}
