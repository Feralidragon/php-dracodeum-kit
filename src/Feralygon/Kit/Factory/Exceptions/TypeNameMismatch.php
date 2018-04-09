<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Factory\Exceptions;

use Feralygon\Kit\Factory\Exception;
use Feralygon\Kit\Factory\Objects\Type;

/**
 * This exception is thrown from a factory whenever a given type name mismatches the expected one.
 * 
 * @since 1.0.0
 * @property-read string $name
 * <p>The expected name.</p>
 * @property-read \Feralygon\Kit\Factory\Objects\Type $type
 * <p>The type instance.</p>
 */
class TypeNameMismatch extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Type instance name {{type.getName()}} mismatches the expected name {{name}} in factory {{factory}}.";
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function loadProperties() : void
	{
		//parent
		parent::loadProperties();
		
		//properties
		$this->addProperty('name')->setAsString()->setAsRequired();
		$this->addProperty('type')->setAsStrictObject(Type::class)->setAsRequired();
	}
}
