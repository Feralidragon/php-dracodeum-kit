<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Utilities\Data\Exceptions;

use Feralygon\Kit\Core\Utilities\Data\Exception;

/**
 * Core data utility invalid path delimiter exception class.
 * 
 * This exception is thrown from the data utility whenever a given path delimiter is invalid.
 * 
 * @since 1.0.0
 * @property-read string $delimiter <p>The delimiter.</p>
 */
class InvalidPathDelimiter extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Invalid path delimiter {{delimiter}}.\n" . 
			"HINT: Only a single ASCII character is allowed.";
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function buildProperties() : void
	{
		$this->addProperty('delimiter')->setAsString()->setAsRequired();
	}
}
