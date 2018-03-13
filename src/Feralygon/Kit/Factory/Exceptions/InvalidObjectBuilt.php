<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Factory\Exceptions;

use Feralygon\Kit\Factory\Exception;
use Feralygon\Kit\Factory\Objects\Type;

/**
 * This exception is thrown from a factory whenever an invalid object has been built for a given type.
 * 
 * @since 1.0.0
 * @property-read \Feralygon\Kit\Factory\Objects\Type $type
 * <p>The type instance.</p>
 * @property-read mixed $object
 * <p>The object.</p>
 */
class InvalidObjectBuilt extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Invalid object {{object}} has been built for type {{type.getName()}} " . 
			"from builder {{type.getBuilder()}} in factory {{factory}}.\n" . 
			"HINT: Only an object is allowed to built.";
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function buildProperties() : void
	{
		//parent
		parent::buildProperties();
		
		//properties
		$this->addProperty('type')->setAsStrictObject(Type::class)->setAsRequired();
		$this->addProperty('object')->setAsRequired();
	}
}
