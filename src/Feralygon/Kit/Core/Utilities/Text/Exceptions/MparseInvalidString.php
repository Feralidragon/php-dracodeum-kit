<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Utilities\Text\Exceptions;

/**
 * Core text utility <code>mparse</code> method invalid string exception class.
 * 
 * This exception is thrown from the text utility <code>mparse</code> method whenever a given string is invalid.
 * 
 * @since 1.0.0
 * @property-read mixed $string <p>The string.</p>
 */
class MparseInvalidString extends Mparse
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Invalid string {{string}}.";
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function loadProperties() : void
	{
		$this->addMixedProperty('string', true);
	}
}
