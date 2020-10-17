<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Entity\Exceptions;

use Dracodeum\Kit\Entity\Exception;
use Dracodeum\Kit\Structures\Uid;

/**
 * This exception is thrown from an entity whenever it is not found.
 * 
 * @property-read int|string|null $id [default = null]
 * <p>The ID.</p>
 * @property-read string|null $scope [default = null]
 * <p>The scope.</p>
 */
class NotFound extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		//initialize
		$id = $this->id;
		$scope = $this->scope;
		$message = "Entity {{entity}}";
		
		//id and scope
		if ($id !== null && $scope !== null) {
			$message .= " with ID {{id}} and scope {{scope}}";
		} elseif ($scope !== null) {
			$message .= " with scope {{scope}}";
		} elseif ($id !== null) {
			$message .= " with ID {{id}}";
		}
		
		//finalize
		$message .= " not found.";
		
		//return
		return $message;
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
