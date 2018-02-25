<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Factory\Exceptions;

use Feralygon\Kit\Factory\Exception;
use Feralygon\Kit\Factory\Objects\Type;

/**
 * Factory invalid object built exception class.
 * 
 * This exception is thrown from a factory whenever an invalid object has been built for a given type.
 * 
 * @since 1.0.0
 * @property-read \Feralygon\Kit\Factory\Objects\Type $type <p>The type instance.</p>
 * @property-read object $object <p>The object.</p>
 * @property-read string|null $name [default = null] <p>The name.</p>
 */
class InvalidObjectBuilt extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		//message
		$message = $this->isset('name')
			? "Invalid object {{object}} has been built for type {{type.getName()}} using name {{name}} " . 
				"from builder {{type.getBuilder()}} in factory {{factory}}."
			: "Invalid object {{object}} has been built for type {{type.getName()}} " . 
				"from builder {{type.getBuilder()}} in factory {{factory}}.";
		
		//hint
		if ($this->get('type')->hasClass()) {
			$message .= "\n" . 
				"HINT: Only an object class or subclass of {{type.getClass()}} is allowed to be built for this type.";
		}
		
		//return
		return $message;
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function buildProperties() : void
	{
		//parent
		parent::buildProperties();
		
		//properties
		$this->addProperty('type')->setAsStrictObject(Type::class)->setAsRequired();
		$this->addProperty('object')->setAsStrictObject()->setAsRequired();
		$this->addProperty('name')->setAsString(false, true)->setDefaultValue(null);
	}
}
