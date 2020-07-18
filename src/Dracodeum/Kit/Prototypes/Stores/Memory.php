<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Prototypes\Stores;

use Dracodeum\Kit\Prototypes\Store;
use Dracodeum\Kit\Prototypes\Store\Interfaces\{
	Checker as IChecker,
	Selecter as ISelecter,
	Inserter as IInserter,
	Updater as IUpdater,
	Deleter as IDeleter
};
use Dracodeum\Kit\Structures\Uid;
use Dracodeum\Kit\Utilities\Data as UData;

/** This store prototype persists resources in static memory, being generally used as a stub for testing. */
class Memory extends Store implements IChecker, ISelecter, IInserter, IUpdater, IDeleter
{
	//Private static properties
	/** @var array */
	private static $values = [];
	
	
	
	//Implemented public methods (Dracodeum\Kit\Prototypes\Store\Interfaces\Checker)
	/** {@inheritdoc} */
	public function exists(Uid $uid, bool $readonly): bool
	{
		return isset(self::$values[UData::keyfy($uid->scope)][UData::keyfy($uid->name)][UData::keyfy($uid->id)]);
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Prototypes\Store\Interfaces\Selecter)
	/** {@inheritdoc} */
	public function select(Uid $uid, bool $readonly): ?array
	{
		return self::$values[UData::keyfy($uid->scope)][UData::keyfy($uid->name)][UData::keyfy($uid->id)] ?? null;
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Prototypes\Store\Interfaces\Inserter)
	/** {@inheritdoc} */
	public function insert(Uid $uid, array $values): ?array
	{
		//initialize
		$scope_key = UData::keyfy($uid->scope);
		$name_key = UData::keyfy($uid->name);
		$id_key = UData::keyfy($uid->id);
		
		//check
		if (isset(self::$values[$scope_key][$name_key][$id_key])) {
			return null;
		}
		
		//insert
		self::$values[$scope_key][$name_key][$id_key] = $values;
		
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
		$id_key = UData::keyfy($uid->id);
		
		//check
		if (!isset(self::$values[$scope_key][$name_key][$id_key])) {
			return null;
		}
		
		//update
		$updated_values = [];
		$ref = &self::$values[$scope_key][$name_key][$id_key];
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
		$id_key = UData::keyfy($uid->id);
		
		//check
		if (!isset(self::$values[$scope_key][$name_key][$id_key])) {
			return false;
		}
		
		//delete
		unset(self::$values[$scope_key][$name_key][$id_key]);
		if (empty(self::$values[$scope_key][$name_key])) {
			unset(self::$values[$scope_key][$name_key]);
			if (empty(self::$values[$scope_key])) {
				unset(self::$values[$scope_key]);
			}
		}
		
		//return
		return true;
	}
}
