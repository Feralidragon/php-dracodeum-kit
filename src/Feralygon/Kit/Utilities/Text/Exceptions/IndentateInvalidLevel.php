<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Utilities\Text\Exceptions;

/**
 * Text utility <code>indentate</code> method invalid level exception class.
 * 
 * This exception is thrown from the text utility <code>indentate</code> method whenever a given level is invalid.
 * 
 * @since 1.0.0
 * @property-read int $level <p>The level.</p>
 */
class IndentateInvalidLevel extends Indentate
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Invalid level {{level}}.\n" . 
			"HINT: Only a value greater than or equal to 0 is allowed.";
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function buildProperties() : void
	{
		$this->addProperty('level')->setAsInteger()->setAsRequired();
	}
}
