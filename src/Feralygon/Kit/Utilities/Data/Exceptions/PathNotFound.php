<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Utilities\Data\Exceptions;

use Feralygon\Kit\Utilities\Data\Exception;

/**
 * This exception is thrown from the data utility whenever a given path is not found.
 * 
 * @since 1.0.0
 * @property-read string $path <p>The path.</p>
 */
class PathNotFound extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Path {{path}} not found.";
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function buildProperties() : void
	{
		$this->addProperty('path')->setAsString()->setAsRequired();
	}
}
