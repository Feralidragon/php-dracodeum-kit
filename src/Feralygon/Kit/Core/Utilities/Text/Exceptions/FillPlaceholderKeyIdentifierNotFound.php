<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Utilities\Text\Exceptions;

/**
 * Core text utility <code>fill</code> method placeholder key identifier not found exception class.
 * 
 * This exception is thrown from the text utility <code>fill</code> method whenever 
 * a given placeholder key identifier is not found.
 * 
 * @since 1.0.0
 * @property-read string $placeholder <p>The placeholder.</p>
 * @property-read string $identifier <p>The key identifier.</p>
 */
class FillPlaceholderKeyIdentifierNotFound extends Fill
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Placeholder {{placeholder}} key identifier {{identifier}} not found.";
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function buildProperties() : void
	{
		$this->addProperty('placeholder')->setAsString()->setAsRequired();
		$this->addProperty('identifier')->setAsString()->setAsRequired();
	}
}
