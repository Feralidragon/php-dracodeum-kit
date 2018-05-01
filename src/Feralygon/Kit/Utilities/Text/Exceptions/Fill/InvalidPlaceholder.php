<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Utilities\Text\Exceptions\Fill;

use Feralygon\Kit\Utilities\Text\Exceptions\Fill as Exception;

/**
 * This exception is thrown from the text utility <code>fill</code> method whenever a given placeholder is invalid.
 * 
 * @since 1.0.0
 * @property-read string $string
 * <p>The string.</p>
 * @property-read string $placeholder
 * <p>The placeholder.</p>
 */
class InvalidPlaceholder extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Invalid placeholder {{placeholder}} in string {{string}}.";
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function loadProperties() : void
	{
		$this->addProperty('string')->setAsString()->setAsRequired();
		$this->addProperty('placeholder')->setAsString()->setAsRequired();
	}
}
