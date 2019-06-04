<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Managers\Memoization\Exceptions;

use Feralygon\Kit\Managers\Memoization\Exception;

/**
 * This exception is thrown from a memoization manager whenever no value is set at a given key.
 * 
 * @since 1.0.0
 * @property-read string $key [coercive]
 * <p>The key.</p>
 */
class ValueNotSet extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		return "No value set at key {{key}} in manager with owner {{manager.getOwner()}}.";
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		//parent
		parent::loadProperties();
		
		//properties
		$this->addProperty('key')->setAsString();
	}
}
