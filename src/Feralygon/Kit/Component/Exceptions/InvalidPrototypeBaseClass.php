<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Component\Exceptions;

use Feralygon\Kit\Component\Exception;

/**
 * Component invalid prototype base class exception class.
 * 
 * This exception is thrown from a component whenever a given prototype base class is invalid.
 * 
 * @since 1.0.0
 * @property-read string $base_class <p>The base class.</p>
 */
class InvalidPrototypeBaseClass extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Invalid prototype base class {{base_class}} in component {{component}}.";
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function buildProperties() : void
	{
		//parent
		parent::buildProperties();
		
		//properties
		$this->addProperty('base_class')->setAsString()->setAsRequired();
	}
}
