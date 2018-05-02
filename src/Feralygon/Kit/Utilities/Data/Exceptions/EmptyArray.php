<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Utilities\Data\Exceptions;

use Feralygon\Kit\Utilities\Data\Exception;

/**
 * This exception is thrown from the data utility whenever an empty array has been given.
 * 
 * @since 1.0.0
 */
class EmptyArray extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "An empty array has been given.";
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function loadProperties() : void {}
}
