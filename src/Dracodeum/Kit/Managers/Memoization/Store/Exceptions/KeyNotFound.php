<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Managers\Memoization\Store\Exceptions;

use Dracodeum\Kit\Managers\Memoization\Store\Exception;

/**
 * @property-read string $name
 * <p>The name.</p>
 */
class KeyNotFound extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		return "Key {{name}} not found in store {{store}}.";
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
