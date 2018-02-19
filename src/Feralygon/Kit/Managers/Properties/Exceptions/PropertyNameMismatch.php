<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Managers\Properties\Exceptions;

use Feralygon\Kit\Managers\Properties\Exception;
use Feralygon\Kit\Managers\Properties\Objects\Property;

/**
 * Properties manager property name mismatch exception class.
 * 
 * This exception is thrown from a properties manager whenever a given property name mismatches the expected one.
 * 
 * @since 1.0.0
 * @property-read string $name <p>The expected property name.</p>
 * @property-read \Feralygon\Kit\Managers\Properties\Objects\Property $property <p>The property instance.</p>
 */
class PropertyNameMismatch extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Property instance name {{property.getName()}} mismatches the expected name {{name}} " . 
			"in properties manager with owner {{manager.getOwner()}}.";
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function buildProperties() : void
	{
		//parent
		parent::buildProperties();
		
		//properties
		$this->addProperty('name')->setAsString()->setAsRequired();
		$this->addProperty('property')->setAsStrictObject(Property::class)->setAsRequired();
	}
}
