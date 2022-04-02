<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Prototypes;

use Dracodeum\Kit\Prototype;
use Dracodeum\Kit\Prototype\Interfaces\Contract as IContract;
use Dracodeum\Kit\Prototypes\Store\Contract;
use Dracodeum\Kit\Structures\Uid;
use Dracodeum\Kit\Components\Store\Enumerations\Halt\Type as EHaltType;

/**
 * @see \Dracodeum\Kit\Components\Store
 * @see \Dracodeum\Kit\Prototypes\Store\Contract
 * @see \Dracodeum\Kit\Prototypes\Store\Interfaces\Checker
 * @see \Dracodeum\Kit\Prototypes\Store\Interfaces\Selecter
 * @see \Dracodeum\Kit\Prototypes\Store\Interfaces\Inserter
 * @see \Dracodeum\Kit\Prototypes\Store\Interfaces\Updater
 * @see \Dracodeum\Kit\Prototypes\Store\Interfaces\Deleter
 * @see \Dracodeum\Kit\Prototypes\Store\Interfaces\UidScopePlaceholderIdString
 */
abstract class Store extends Prototype implements IContract
{
	//Implemented public static methods (Dracodeum\Kit\Prototype\Interfaces\Contract)
	/** {@inheritdoc} */
	public static function getContract(): string
	{
		return Contract::class;
	}
	
	
	
	//Final protected methods
	/**
	 * Halt the current function or method call in the stack with a given UID instance and type.
	 * 
	 * @see \Dracodeum\Kit\Components\Store\Enumerations\Halt\Type
	 * @param \Dracodeum\Kit\Structures\Uid $uid
	 * <p>The UID instance to halt with.</p>
	 * @param string $type
	 * <p>The type to halt with,
	 * as a name or value from the <code>Dracodeum\Kit\Components\Store\Enumerations\Halt\Type</code> enumeration.</p>
	 * @return void
	 */
	final protected function halt(Uid $uid, string $type): void
	{
		$this->contractCall('halt', $uid, $type);
	}
	
	/**
	 * Halt the current function or method call in the stack with a given UID instance over not being found.
	 *
	 * @param \Dracodeum\Kit\Structures\Uid $uid
	 * <p>The UID instance to halt with.</p>
	 * @return void
	 */
	final protected function haltNotFound(Uid $uid): void
	{
		$this->halt($uid, EHaltType::NOT_FOUND);
	}
	
	/**
	 * Halt the current function or method call in the stack with a given UID instance over its scope not being found.
	 *
	 * @param \Dracodeum\Kit\Structures\Uid $uid
	 * <p>The UID instance to halt with.</p>
	 * @return void
	 */
	final protected function haltScopeNotFound(Uid $uid): void
	{
		$this->halt($uid, EHaltType::SCOPE_NOT_FOUND);
	}
	
	/**
	 * Halt the current function or method call in the stack with a given UID instance over a conflict.
	 *
	 * @param \Dracodeum\Kit\Structures\Uid $uid
	 * <p>The UID instance to halt with.</p>
	 * @return void
	 */
	final protected function haltConflict(Uid $uid): void
	{
		$this->halt($uid, EHaltType::CONFLICT);
	}
}
