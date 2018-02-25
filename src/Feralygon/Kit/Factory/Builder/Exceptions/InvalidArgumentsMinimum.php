<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Factory\Builder\Exceptions;

use Feralygon\Kit\Factory\Builder\Exception;

/**
 * Factory builder invalid arguments minimum exception class.
 * 
 * This exception is thrown from a builder whenever a given minimum number of arguments is invalid.
 * 
 * @since 1.0.0
 * @property-read int $minimum <p>The minimum.</p>
 */
class InvalidArgumentsMinimum extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Invalid minimum number of arguments {{minimum}} in builder {{builder}}.\n" . 
			"HINT: Only a minimum greater than or equal to 0 is allowed.";
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function buildProperties() : void
	{
		//parent
		parent::buildProperties();
		
		//properties
		$this->addProperty('minimum')->setAsInteger()->setAsRequired();
	}
}
