<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Utilities\Type\Exceptions;

use Feralygon\Kit\Utilities\Type\Exception;

/**
 * Type utility interface not found exception class.
 * 
 * This exception is thrown from the type utility whenever a given interface is not found.
 * 
 * @since 1.0.0
 * @property-read string $interface <p>The interface.</p>
 */
class InterfaceNotFound extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Interface {{interface}} not found.";
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function buildProperties() : void
	{
		$this->addProperty('interface')->setAsString()->setAsRequired();
	}
}
