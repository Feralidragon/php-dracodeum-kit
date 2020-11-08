<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Enumeration\Exceptions;

use Dracodeum\Kit\Enumeration\Exception;

/**
 * @property-read int|float|string $element
 * <p>The element.</p>
 */
class ElementNotFound extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		return "Element {{element}} not found in enumeration {{enumeration}}.";
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		//parent
		parent::loadProperties();
		
		//properties
		$this->addProperty('element')
			->addEvaluator(function (&$value): bool {
				return is_int($value) || is_float($value) || is_string($value);
			})
		;
	}
}
