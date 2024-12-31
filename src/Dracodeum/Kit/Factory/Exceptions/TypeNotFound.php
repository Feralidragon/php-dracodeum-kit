<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Factory\Exceptions;

use Dracodeum\Kit\Factory\Exception;

/**
 * @property-read string $name
 * <p>The name.</p>
 */
class TypeNotFound extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		return "Type {{name}} not found in factory {{factory}}.";
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		//parent
		parent::loadProperties();
		
		//properties
		$this->addProperty('name')->setAsString();
	}
}
