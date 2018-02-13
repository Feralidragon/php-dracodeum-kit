<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Enumeration\Exceptions;

use Feralygon\Kit\Core\Enumeration\Exception;

/**
 * Core enumeration element not found exception class.
 * 
 * This exception is thrown from an enumeration whenever a given element is not found.
 * 
 * @since 1.0.0
 * @property-read int|float|string $element <p>The element.</p>
 */
class ElementNotFound extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Element {{element}} not found in enumeration {{enumeration}}.";
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function buildProperties() : void
	{
		//parent
		parent::buildProperties();
		
		//properties
		$this->addProperty('element')
			->setEvaluator(function (&$value) : bool {
				return is_int($value) || is_float($value) || is_string($value);
			})
			->setAsRequired()
		;
	}
}
