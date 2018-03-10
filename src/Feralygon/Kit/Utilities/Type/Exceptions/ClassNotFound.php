<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Utilities\Type\Exceptions;

use Feralygon\Kit\Utilities\Type\Exception;

/**
 * This exception is thrown from the type utility whenever a given class is not found.
 * 
 * @since 1.0.0
 * @property-read string $class <p>The class.</p>
 */
class ClassNotFound extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Class {{class}} not found.";
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function buildProperties() : void
	{
		$this->addProperty('class')->setAsString()->setAsRequired();
	}
}
