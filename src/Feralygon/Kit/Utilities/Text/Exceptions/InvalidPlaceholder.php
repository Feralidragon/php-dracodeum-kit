<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Utilities\Text\Exceptions;

use Feralygon\Kit\Utilities\Text\Exception;

/**
 * This exception is thrown from the text utility whenever a given placeholder is invalid.
 * 
 * @since 1.0.0
 * @property-read string $placeholder <p>The placeholder.</p>
 */
class InvalidPlaceholder extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Invalid placeholder {{placeholder}}.";
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function buildProperties() : void
	{
		$this->addProperty('placeholder')->setAsString()->setAsRequired();
	}
}
