<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Factory\Builder\Exceptions;

use Feralygon\Kit\Factory\Builder\Exception;

/**
 * Factory builder invalid arguments maximum exception class.
 * 
 * This exception is thrown from a builder whenever a given maximum number of arguments is invalid.
 * 
 * @since 1.0.0
 * @property-read int $maximum <p>The maximum.</p>
 */
class InvalidArgumentsMaximum extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Invalid maximum number of arguments {{maximum}} in builder {{builder}}.\n" . 
			"HINT: Only a maximum greater than or equal to 0 is allowed.";
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function buildProperties() : void
	{
		//parent
		parent::buildProperties();
		
		//properties
		$this->addProperty('maximum')->setAsInteger()->setAsRequired();
	}
}
