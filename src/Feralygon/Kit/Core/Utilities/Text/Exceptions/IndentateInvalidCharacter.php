<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Utilities\Text\Exceptions;

/**
 * Core text utility <code>indentate</code> method invalid character exception class.
 * 
 * This exception is thrown from the text utility <code>indentate</code> method whenever a given character is invalid.
 * 
 * @since 1.0.0
 * @property-read string $character <p>The character.</p>
 */
class IndentateInvalidCharacter extends Indentate
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Invalid character {{character}}.\n" . 
			"HINT: Only a single ASCII character is allowed.";
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function loadProperties() : void
	{
		$this->addStringProperty('character', true);
	}
}
