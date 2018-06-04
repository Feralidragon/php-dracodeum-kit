<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Managers\Memoization\Exceptions;

use Feralygon\Kit\Managers\Memoization\Exception;

/**
 * This exception is thrown from a memoization manager whenever a value is not found at a given key.
 * 
 * @since 1.0.0
 * @property-read string $key
 * <p>The key.</p>
 * @property-read string $namespace [default = '']
 * <p>The namespace.</p>
 */
class ValueNotFound extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return $this->get('namespace') !== ''
			? "No value found at key {{key}} in namespace {{namespace}} in manager with owner {{manager.getOwner()}}."
			: "No value found at key {{key}} in manager with owner {{manager.getOwner()}}.";
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function loadProperties() : void
	{
		//parent
		parent::loadProperties();
		
		//properties
		$this->addProperty('key')->setAsString();
		$this->addProperty('namespace')->setAsString()->setDefaultValue('');
	}
}
