<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Prototypes\Providers;

use Dracodeum\Kit\Prototypes\Provider;
use Dracodeum\Kit\Prototypes\Provider\Interfaces\{
	Checker as IChecker,
	Returner as IReturner,
	Inserter as IInserter,
	Updater as IUpdater,
	Deleter as IDeleter
};
use Dracodeum\Kit\Utilities\{
	Call as UCall,
	Data as UData
};

/** This provider prototype persists resources in memory, being generally used as a stub for testing. */
class Memory extends Provider implements IChecker, IReturner, IInserter, IUpdater, IDeleter
{
	//Private properties
	/** @var array */
	private $values = [];
	
	
	
	//Implemented public methods (Dracodeum\Kit\Prototypes\Provider\Interfaces\Checker)
	/** {@inheritdoc} */
	public function exists(string $name, $uid, ?string $scope, bool $readonly): bool
	{
		return isset($this->values[UData::keyfy($scope)][UData::keyfy($name)][UData::keyfy($uid)]);
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Prototypes\Provider\Interfaces\Returner)
	/** {@inheritdoc} */
	public function return(string $name, $uid, ?string $scope, bool $readonly): ?array
	{
		return $this->values[UData::keyfy($scope)][UData::keyfy($name)][UData::keyfy($uid)] ?? null;
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Prototypes\Provider\Interfaces\Inserter)
	/** {@inheritdoc} */
	public function insert(string $name, &$uid, array $values, ?string $scope): array
	{
		//initialize
		$scope_key = UData::keyfy($scope);
		$name_key = UData::keyfy($name);
		$uid_key = UData::keyfy($uid);
		
		//guard
		UCall::guard(!isset($this->values[$scope_key][$name_key][$uid_key]), [
			'error_message' => "Cannot insert resource {{name}} with UID {{uid}} (scope: {{scope}}).",
			'parameters' => [
				'name' => $name,
				'uid' => $uid,
				'scope' => $scope
			]
		]);
		
		//insert
		$this->values[$scope_key][$name_key][$uid_key] = $values;
		
		//return
		return $values;
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Prototypes\Provider\Interfaces\Updater)
	/** {@inheritdoc} */
	public function update(string $name, $uid, array $values, ?string $scope): ?array
	{
		//initialize
		$scope_key = UData::keyfy($scope);
		$name_key = UData::keyfy($name);
		$uid_key = UData::keyfy($uid);
		
		//check
		if (!isset($this->values[$scope_key][$name_key][$uid_key])) {
			return null;
		}
		
		//update
		$updated_values = [];
		$ref = &$this->values[$scope_key][$name_key][$uid_key];
		foreach ($values as $k => $v) {
			if (array_key_exists($k, $ref)) {
				$ref[$k] = $updated_values[$k] = $v;
			}
		}
		unset($ref);
		
		//return
		return $updated_values;
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Prototypes\Provider\Interfaces\Deleter)
	/** {@inheritdoc} */
	public function delete(string $name, $uid, ?string $scope): bool
	{
		//initialize
		$scope_key = UData::keyfy($scope);
		$name_key = UData::keyfy($name);
		$uid_key = UData::keyfy($uid);
		
		//check
		if (!isset($this->values[$scope_key][$name_key][$uid_key])) {
			return false;
		}
		
		//delete
		unset($this->values[$scope_key][$name_key][$uid_key]);
		if (empty($this->values[$scope_key][$name_key])) {
			unset($this->values[$scope_key][$name_key]);
			if (empty($this->values[$scope_key])) {
				unset($this->values[$scope_key]);
			}
		}
		
		//return
		return true;
	}
}
