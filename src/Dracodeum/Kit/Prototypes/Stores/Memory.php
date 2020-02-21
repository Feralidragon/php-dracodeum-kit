<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Prototypes\Stores;

use Dracodeum\Kit\Prototypes\Store;
use Dracodeum\Kit\Prototypes\Store\Interfaces\{
	Checker as IChecker,
	Returner as IReturner,
	Inserter as IInserter,
	Updater as IUpdater,
	Deleter as IDeleter
};
use Dracodeum\Kit\Components\Store\Structures\Uid;
use Dracodeum\Kit\Utilities\{
	Call as UCall,
	Data as UData
};

/** This store prototype persists resources in memory, being generally used as a stub for testing. */
class Memory extends Store implements IChecker, IReturner, IInserter, IUpdater, IDeleter
{
	//Private properties
	/** @var array */
	private $values = [];
	
	
	
	//Implemented public methods (Dracodeum\Kit\Prototypes\Store\Interfaces\Checker)
	/** {@inheritdoc} */
	public function exists(Uid $uid, bool $readonly): bool
	{
		return isset($this->values[UData::keyfy($uid->scope)][UData::keyfy($uid->name)][UData::keyfy($uid->value)]);
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Prototypes\Store\Interfaces\Returner)
	/** {@inheritdoc} */
	public function return(Uid $uid, bool $readonly): ?array
	{
		return $this->values[UData::keyfy($uid->scope)][UData::keyfy($uid->name)][UData::keyfy($uid->value)] ?? null;
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Prototypes\Store\Interfaces\Inserter)
	/** {@inheritdoc} */
	public function insert(Uid $uid, array $values): array
	{
		//initialize
		$scope_key = UData::keyfy($uid->scope);
		$name_key = UData::keyfy($uid->name);
		$value_key = UData::keyfy($uid->value);
		
		//guard
		UCall::guard(!isset($this->values[$scope_key][$name_key][$value_key]), [
			'error_message' => "Cannot insert resource {{name}} with UID value {{value}} (scope: {{scope}}).",
			'parameters' => [
				'name' => $uid->name,
				'value' => $uid->value,
				'scope' => $uid->scope
			]
		]);
		
		//insert
		$this->values[$scope_key][$name_key][$value_key] = $values;
		
		//return
		return $values;
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Prototypes\Store\Interfaces\Updater)
	/** {@inheritdoc} */
	public function update(Uid $uid, array $values): ?array
	{
		//initialize
		$scope_key = UData::keyfy($uid->scope);
		$name_key = UData::keyfy($uid->name);
		$value_key = UData::keyfy($uid->value);
		
		//check
		if (!isset($this->values[$scope_key][$name_key][$value_key])) {
			return null;
		}
		
		//update
		$updated_values = [];
		$ref = &$this->values[$scope_key][$name_key][$value_key];
		foreach ($values as $k => $v) {
			if (array_key_exists($k, $ref)) {
				$ref[$k] = $updated_values[$k] = $v;
			}
		}
		unset($ref);
		
		//return
		return $updated_values;
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Prototypes\Store\Interfaces\Deleter)
	/** {@inheritdoc} */
	public function delete(Uid $uid): bool
	{
		//initialize
		$scope_key = UData::keyfy($uid->scope);
		$name_key = UData::keyfy($uid->name);
		$value_key = UData::keyfy($uid->value);
		
		//check
		if (!isset($this->values[$scope_key][$name_key][$value_key])) {
			return false;
		}
		
		//delete
		unset($this->values[$scope_key][$name_key][$value_key]);
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
