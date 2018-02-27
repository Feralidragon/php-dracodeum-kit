<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Component\Exceptions;

use Feralygon\Kit\Component\Exception;

/**
 * Component prototype name not found exception class.
 * 
 * This exception is thrown from a component whenever a given prototype name is not found.
 * 
 * @since 1.0.0
 * @property-read string $name <p>The prototype name.</p>
 */
class PrototypeNameNotFound extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Prototype name {{name}} not found in component {{component}}.";
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