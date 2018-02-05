<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Utilities\Text\Exceptions;

/**
 * Core text utility <code>slugify</code> method invalid delimiter exception class.
 * 
 * This exception is thrown from the text utility <code>slugify</code> method whenever a given delimiter is invalid.
 * 
 * @since 1.0.0
 * @property-read string $delimiter <p>The delimiter.</p>
 */
class SlugifyInvalidDelimiter extends Slugify
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Invalid delimiter {{delimiter}}.\n" . 
			"HINT: Only a single ASCII character is allowed.";
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function loadProperties() : void
	{
		$this->addStringProperty('delimiter', true);
	}
}
