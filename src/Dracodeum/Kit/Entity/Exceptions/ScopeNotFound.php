<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Entity\Exceptions;

use Dracodeum\Kit\Entity\Exception;

/**
 * This exception is thrown from an entity whenever a given scope is not found.
 * 
 * @property-read string|null $scope [coercive]
 * <p>The scope.<br>
 * If set, then it cannot be empty.</p>
 */
class ScopeNotFound extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		return $this->scope !== null
			? "Entity {{entity}} scope {{scope}} not found."
			: "Entity {{entity}} scope not found.";
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		//parent
		parent::loadProperties();
		
		//properties
		$this->addProperty('scope')->setAsString(true, true);
	}
}
