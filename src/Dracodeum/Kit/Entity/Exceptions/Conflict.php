<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Entity\Exceptions;

use Dracodeum\Kit\Entity\Exception;

/**
 * This exception is thrown from an entity whenever it conflicts with an existing one.
 * 
 * @property-read mixed $id
 * <p>The ID.</p>
 * @property-read string|null $scope [coercive] [default = null]
 * <p>The scope.<br>
 * If set, then it cannot be empty.</p>
 */
class Conflict extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		return $this->get('scope') !== null
			? "Entity {{entity}} with ID {{id}} and scope {{scope}} conflicts with an existing one."
			: "Entity {{entity}} with ID {{id}} conflicts with an existing one.";
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		//parent
		parent::loadProperties();
		
		//properties
		$this->addProperty('id');
		$this->addProperty('scope')->setAsString(true, true)->setDefaultValue(null);
	}
}
