<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Utilities\Text\Exceptions;

/**
 * This exception is thrown from the text utility <code>fill</code> method whenever 
 * a given placeholder identifier is invalid.
 * 
 * @since 1.0.0
 * @property-read string $placeholder <p>The placeholder.</p>
 * @property-read string $identifier <p>The identifier.</p>
 */
class FillInvalidPlaceholderIdentifier extends Fill
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Invalid identifier {{identifier}} in placeholder {{placeholder}}.";
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function buildProperties() : void
	{
		$this->addProperty('placeholder')->setAsString()->setAsRequired();
		$this->addProperty('identifier')->setAsString()->setAsRequired();
	}
}
