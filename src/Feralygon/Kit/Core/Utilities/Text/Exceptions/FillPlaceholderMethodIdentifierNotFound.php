<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Utilities\Text\Exceptions;

/**
 * Core text utility <code>fill</code> method placeholder method identifier not found exception class.
 * 
 * This exception is thrown from the text utility <code>fill</code> method whenever a given placeholder method identifier 
 * is not found.
 * 
 * @since 1.0.0
 * @property-read string $placeholder <p>The placeholder.</p>
 * @property-read string $identifier <p>The method identifier.</p>
 */
class FillPlaceholderMethodIdentifierNotFound extends Fill
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Placeholder {{placeholder}} method identifier {{identifier}} not found.";
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function loadProperties() : void
	{
		$this->addStringProperty('placeholder', true);
		$this->addStringProperty('identifier', true);
	}
}
