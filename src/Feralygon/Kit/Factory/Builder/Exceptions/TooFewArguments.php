<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Factory\Builder\Exceptions;

use Feralygon\Kit\Factory\Builder\Exception;

/**
 * Factory builder too few arguments exception class.
 * 
 * This exception is thrown from a builder whenever too few arguments are given.
 * 
 * @since 1.0.0
 * @property-read int $count <p>The number of arguments.</p>
 * @property-read int $minimum <p>The expected minimum number of arguments.</p>
 */
class TooFewArguments extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		$message = $this->get('count') === 1
			? "Too few arguments: {{count}} was given in builder {{builder}}."
			: "Too few arguments: {{count}} were given in builder {{builder}}.";
		$message .= "\n";
		$message .= $this->get('minimum') === 1
			? "HINT: Only a minimum of {{minimum}} argument is allowed."
			: "HINT: Only a minimum of {{minimum}} arguments is allowed.";
		return $message;	
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function buildProperties() : void
	{
		//parent
		parent::buildProperties();
		
		//properties
		$this->addProperty('count')->setAsInteger()->setAsRequired();
		$this->addProperty('minimum')->setAsInteger()->setAsRequired();
	}
}
