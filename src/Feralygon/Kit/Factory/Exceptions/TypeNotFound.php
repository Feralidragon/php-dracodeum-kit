<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Factory\Exceptions;

use Feralygon\Kit\Factory\Exception;

/**
 * This exception is thrown from a factory whenever a given type is not found.
 * 
 * @since 1.0.0
 * @property-read string $name <p>The type name.</p>
 */
class TypeNotFound extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Type {{name}} not found in factory {{factory}}.";
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function buildProperties() : void
	{
		//parent
		parent::buildProperties();
		
		//properties
		$this->addProperty('name')->setAsString()->setAsRequired();
	}
}
