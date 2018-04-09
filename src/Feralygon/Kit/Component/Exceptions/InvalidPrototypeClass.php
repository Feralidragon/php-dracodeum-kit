<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Component\Exceptions;

use Feralygon\Kit\Component\Exception;
use Feralygon\Kit\Prototype;

/**
 * This exception is thrown from a component whenever a given prototype class is invalid.
 * 
 * @since 1.0.0
 * @property-read string $class
 * <p>The class.</p>
 * @property-read string $base_class
 * <p>The base class.</p>
 */
class InvalidPrototypeClass extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Invalid prototype class {{class}} for component {{component}}.\n" . 
			"HINT: Only a class or subclass of {{base_class}} is allowed for this component.";
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function loadProperties() : void
	{
		//parent
		parent::loadProperties();
		
		//properties
		$this->addProperty('class')->setAsString()->setAsRequired();
		$this->addProperty('base_class')->setAsClass(Prototype::class)->setAsRequired();
	}
}
