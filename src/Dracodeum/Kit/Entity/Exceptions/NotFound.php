<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Entity\Exceptions;

use Dracodeum\Kit\Entity\Exception;
use Dracodeum\Kit\Components\Store\Structures\Uid;

/**
 * This exception is thrown from an entity whenever it is not found.
 * 
 * @property-read int|string|null $id [default = null]
 * <p>The ID.</p>
 * @property-read string|null $scope [coercive] [default = null]
 * <p>The scope.<br>
 * If set, then it cannot be empty.</p>
 */
class NotFound extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		$id = $this->get('id');
		$scope = $this->get('scope');
		if ($id !== null && $scope !== null) {
			return "Entity {{entity}} with ID {{id}} and scope {{scope}} not found.";
		} elseif ($scope !== null) {
			return "Entity {{entity}} with scope {{scope}} not found.";
		} elseif ($id !== null) {
			return "Entity {{entity}} with ID {{id}} not found.";
		}
		return "Entity {{entity}} not found.";
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		//parent
		parent::loadProperties();
		
		//properties
		$this->addProperty('id')
			->addEvaluator(function (&$value): bool {
				return Uid::evaluateId($value, true);
			})
			->setDefaultValue(null)
		;
		$this->addProperty('scope')->setAsString(true, true)->setDefaultValue(null);
	}
}
