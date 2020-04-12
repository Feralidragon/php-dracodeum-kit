<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Entity\Exceptions;

use Dracodeum\Kit\Entity\Exception;
use Dracodeum\Kit\Components\Store\Structures\Uid;

/**
 * This exception is thrown from an entity whenever it conflicts with an existing one.
 * 
 * @property-read int|float|string|null $id [default = null]
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
		$id = $this->get('id');
		$scope = $this->get('scope');
		if ($id !== null && $scope !== null) {
			return "Entity {{entity}} with ID {{id}} and scope {{scope}} conflicts with an existing one.";
		} elseif ($scope !== null) {
			return "Entity {{entity}} with scope {{scope}} conflicts with an existing one.";
		} elseif ($id !== null) {
			return "Entity {{entity}} with ID {{id}} conflicts with an existing one.";
		}
		return "Entity {{entity}} conflicts with an existing one.";
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
