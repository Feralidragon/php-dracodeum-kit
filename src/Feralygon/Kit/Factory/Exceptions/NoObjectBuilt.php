<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Factory\Exceptions;

use Feralygon\Kit\Factory\Exception;
use Feralygon\Kit\Factory\Objects\Type;

/**
 * Factory no object built exception class.
 * 
 * This exception is thrown from a factory whenever no object has been built for a given name and type.
 * 
 * @since 1.0.0
 * @property-read string $name <p>The name.</p>
 * @property-read \Feralygon\Kit\Factory\Objects\Type $type <p>The type instance.</p>
 */
class NoObjectBuilt extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "No object has been built for {{name}} for type {{type.getName()}} " . 
			"using builder {{type.getBuilder()}} in factory {{factory}}.";
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function buildProperties() : void
	{
		//parent
		parent::buildProperties();
		
		//properties
		$this->addProperty('name')->setAsString()->setAsRequired();
		$this->addProperty('type')->setAsStrictObject(Type::class)->setAsRequired();
	}
}
