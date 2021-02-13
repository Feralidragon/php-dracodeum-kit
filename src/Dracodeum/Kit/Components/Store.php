<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components;

use Dracodeum\Kit\Component;
use Dracodeum\Kit\Components\Store\{
	Proxy,
	Exceptions
};
use Dracodeum\Kit\Structures\Uid;
use Dracodeum\Kit\Factories\Component as Factory;
use Dracodeum\Kit\Prototypes\{
	Store as Prototype,
	Stores as Prototypes
};
use Dracodeum\Kit\Prototypes\Store\Interfaces as PrototypeInterfaces;
use Dracodeum\Kit\Components\Store\Enumerations\Halt\Type as EHaltType;
use Dracodeum\Kit\Utilities\Text as UText;

/**
 * This component represents a store which performs persistent CRUD operations of resources, namely create (insert), 
 * read (exists and select), update and delete.
 * 
 * @method \Dracodeum\Kit\Prototypes\Store getPrototype() [protected]
 * 
 * @see \Dracodeum\Kit\Prototypes\Store
 */
class Store extends Component
{
	//Implemented public static methods
	/** {@inheritdoc} */
	public static function getPrototypeBaseClass(): string
	{
		return Prototype::class;
	}
	
	
	
	//Implemented protected static methods (Dracodeum\Kit\Component\Traits\DefaultBuilder)
	/** {@inheritdoc} */
	protected static function getDefaultBuilder(): ?callable
	{
		return [Factory::class, 'store'];
	}
	
	
	
	//Implemented protected static methods (Dracodeum\Kit\Component\Traits\ProxyClass)
	/** {@inheritdoc} */
	protected static function getProxyClass(): ?string
	{
		return Proxy::class;
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Component\Traits\PrototypeProducer)
	/** {@inheritdoc} */
	protected function producePrototype(string $name, array $properties)
	{
		switch ($name) {
			case 'memory':
				//no break
			case 'mem':
				return Prototypes\Memory::class;
		}
		return null;
	}
	
	
	
	//Protected methods
	/**
	 * Get string for a given UID scope placeholder from a given ID.
	 * 
	 * @param string $placeholder
	 * The placeholder to get for.
	 * 
	 * @param int|string $id
	 * The ID to get from.
	 * 
	 * @return string|null
	 * The string, or `null` if none is set.
	 */
	protected function getUidScopePlaceholderIdString(string $placeholder, $id): ?string
	{
		$prototype = $this->getPrototype();
		return $prototype instanceof PrototypeInterfaces\UidScopePlaceholderIdString
			? $prototype->getUidScopePlaceholderIdString($placeholder, $id)
			: null;
	}
	
	
	
	//Final public methods
	/**
	 * Coerce a given UID into an instance.
	 * 
	 * @param coercible:struct<\Dracodeum\Kit\Structures\Uid> $uid
	 * The UID to coerce.
	 * 
	 * @param bool $clone
	 * If an instance is given, then clone it into a new one with the same properties.
	 * 
	 * @return \Dracodeum\Kit\Structures\Uid
	 * The given UID coerced into an instance.
	 */
	final public function coerceUid($uid, bool $clone = false): Uid
	{
		$uid = Uid::coerce($uid, $clone);
		if ($uid->defaulted('scope') && $uid->base_scope !== null) {
			$uid->scope = $this->getUidScope($uid->base_scope, $uid->scope_ids);
		}
		return $uid;
	}
	
	/**
	 * Get UID scope.
	 * 
	 * @param string $base_scope
	 * The base scope to get from.
	 * 
	 * Placeholders may optionally be set in the given base scope as `{{placeholder}}` to be replaced by a 
	 * corresponding set of parameters, and they must be exclusively composed of identifiers, which are defined as 
	 * words which must start with a letter (`a-z` or `A-Z`) or underscore (`_`), and may only contain letters (`a-z` 
	 * or `A-Z`), digits (`0-9`) and underscores (`_`).
	 * 
	 * They may also contain pointers to specific object properties or associative array values from the given set of 
	 * parameters by using a dot between identifiers, such as `{{object.property}}`, with no limit on the number of 
	 * pointers chained.
	 * 
	 * If suffixed with opening and closing parenthesis, such as `{{object.method()}}`, then the given pointers are 
	 * interpreted as getter method calls, but they cannot be given any arguments.
	 * 
	 * @param (int|string)[] $scope_ids
	 * The scope IDs to get with, as a set of `name => id` pairs.
	 * 
	 * @return string
	 * The scope.
	 */
	final public function getUidScope(string $base_scope, array $scope_ids): string
	{
		return UText::hasPlaceholders($base_scope)
			? UText::fill($base_scope, Uid::coerceScopeIds($scope_ids), null, [
				'stringifier' => \Closure::fromCallable([$this, 'getUidScopePlaceholderIdString'])
			])
			: $base_scope;
	}
	
	/**
	 * Check if a resource exists.
	 * 
	 * @param coercible:struct<\Dracodeum\Kit\Structures\Uid> $uid
	 * The UID to check with.
	 * 
	 * @return bool
	 * Boolean `true` if the resource exists.
	 */
	final public function exists($uid): bool
	{
		//uid
		$uid = $this->coerceUid($uid, true)->setAsReadonly();
		
		//prototype
		$prototype = $this->getPrototype();
		if ($prototype instanceof PrototypeInterfaces\Checker) {
			return $prototype->exists($uid, false);
		} elseif ($prototype instanceof PrototypeInterfaces\Selecter) {
			return $prototype->select($uid, false) !== null;
		}
		
		//halt
		$this->haltPrototypeMethodNotImplemented('exists');
	}
	
	/**
	 * Select a resource.
	 * 
	 * @param coercible:struct<\Dracodeum\Kit\Structures\Uid> $uid
	 * The UID to select with.
	 * 
	 * @param bool $no_throw
	 * Do not throw an exception.
	 * 
	 * @throws \Dracodeum\Kit\Components\Store\Exceptions\NotFound
	 * 
	 * @return array|null
	 * The selected resource, as a set of `name => value` pairs.  
	 * If `$no_throw` is set to boolean `true`, then `null` is returned if it was not found.
	 */
	final public function select($uid, bool $no_throw = false): ?array
	{
		//uid
		$uid = $this->coerceUid($uid, true)->setAsReadonly();
		
		//prototype
		$prototype = $this->getPrototype();
		if ($prototype instanceof PrototypeInterfaces\Selecter) {
			//return
			$values = null;
			try {
				$values = $prototype->select($uid, false);
			} catch (Exceptions\NotFound $exception) {
				if ($no_throw) {
					return null;
				}
				throw $exception;
			}
			
			//finalize
			if ($values === null) {
				if ($no_throw) {
					return null;
				}
				throw new Exceptions\NotFound([$this, $prototype, $uid]);
			}
			return $values;
		}
		
		//halt
		$this->haltPrototypeMethodNotImplemented('select');
	}
	
	/**
	 * Insert a resource.
	 * 
	 * @param coercible:struct<\Dracodeum\Kit\Structures\Uid> $uid
	 * The UID to insert with.  
	 * It is coerced into an instance, and may be modified during insertion, such as when any of its properties are 
	 * automatically generated.
	 * 
	 * @param array $values
	 * The values to insert with, as a set of `name => value` pairs.
	 * 
	 * @param bool $no_throw
	 * Do not throw an exception.
	 * 
	 * @throws \Dracodeum\Kit\Components\Store\Exceptions\ScopeNotFound
	 * @throws \Dracodeum\Kit\Components\Store\Exceptions\Conflict
	 * 
	 * @return array|null
	 * The full or partial set of the inserted resource values, as a set of `name => value` pairs.  
	 * If `$no_throw` is set to boolean `true`, then `null` is returned if the resource already exists.
	 */
	final public function insert(&$uid, array $values, bool $no_throw = false): ?array
	{
		//uid
		$insert_uid = $this->coerceUid($uid, true);
		
		//prototype
		$prototype = $this->getPrototype();
		if ($prototype instanceof PrototypeInterfaces\Inserter) {
			//insert
			$inserted_values = null;
			try {
				$inserted_values = $prototype->insert($insert_uid, $values);
			} catch (Exceptions\ScopeNotFound|Exceptions\Conflict $exception) {
				if ($no_throw) {
					return null;
				}
				throw $exception;
			}
			
			//finalize
			if ($inserted_values === null) {
				if ($no_throw) {
					return null;
				}
				throw new Exceptions\Conflict([$this, $prototype, $uid]);
			}
			$uid = $insert_uid->setAsReadonly();
			return $inserted_values;
		}
		
		//halt
		$this->haltPrototypeMethodNotImplemented('insert');
	}
	
	/**
	 * Update a resource.
	 * 
	 * @param coercible:struct<\Dracodeum\Kit\Structures\Uid> $uid
	 * The UID to update with.
	 * 
	 * @param array $values
	 * The values to update with, as a set of `name => value` pairs.
	 * 
	 * @param bool $no_throw
	 * Do not throw an exception.
	 * 
	 * @throws \Dracodeum\Kit\Components\Store\Exceptions\NotFound
	 * 
	 * @return array|null
	 * The full or partial set of the updated resource values, as a set of `name => value` pairs.  
	 * If `$no_throw` is set to boolean `true`, then `null` is returned if the resource was not found.
	 */
	final public function update($uid, array $values, bool $no_throw = false): ?array
	{
		//uid
		$uid = $this->coerceUid($uid, true)->setAsReadonly();
		
		//prototype
		$prototype = $this->getPrototype();
		if ($prototype instanceof PrototypeInterfaces\Updater) {
			//update
			$updated_values = null;
			try {
				$updated_values = $prototype->update($uid, $values);
			} catch (Exceptions\NotFound $exception) {
				if ($no_throw) {
					return null;
				}
				throw $exception;
			}
			
			//finalize
			if ($updated_values === null) {
				if ($no_throw) {
					return null;
				}
				throw new Exceptions\NotFound([$this, $prototype, $uid]);
			}
			return $updated_values;
		}
		
		//halt
		$this->haltPrototypeMethodNotImplemented('update');
	}
	
	/**
	 * Delete a resource.
	 * 
	 * @param coercible:struct<\Dracodeum\Kit\Structures\Uid> $uid
	 * The UID to delete with.
	 * 
	 * @param bool $no_throw
	 * Do not throw an exception.
	 * 
	 * @throws \Dracodeum\Kit\Components\Store\Exceptions\NotFound
	 * 
	 * @return void|bool
	 * If `$no_throw` is set to boolean `true`, then boolean `true` is returned if the resource was found and deleted, 
	 * or boolean `false` if otherwise.
	 */
	final public function delete($uid, bool $no_throw = false)
	{
		//uid
		$uid = $this->coerceUid($uid, true)->setAsReadonly();
		
		//prototype
		$prototype = $this->getPrototype();
		if ($prototype instanceof PrototypeInterfaces\Deleter) {
			//delete
			$deleted = false;
			try {
				$deleted = $prototype->delete($uid);
			} catch (Exceptions\NotFound $exception) {
				if ($no_throw) {
					return false;
				}
				throw $exception;
			}
			
			//finalize
			if ($no_throw) {
				return $deleted;
			} elseif (!$deleted) {
				throw new Exceptions\NotFound([$this, $prototype, $uid]);
			}
			return;
		}
		
		//halt
		$this->haltPrototypeMethodNotImplemented('delete');
	}
	
	
	
	//Protected methods
	/**
	 * Halt the current function or method call in the stack.
	 * 
	 * @param \Dracodeum\Kit\Structures\Uid $uid
	 * The UID instance to halt with.
	 * 
	 * @param coercible:enum<\Dracodeum\Kit\Components\Store\Enumerations\Halt\Type> $type
	 * The type to halt with.
	 * 
	 * @throws \Dracodeum\Kit\Components\Store\Exceptions\Halted
	 * @throws \Dracodeum\Kit\Components\Store\Exceptions\NotFound
	 * @throws \Dracodeum\Kit\Components\Store\Exceptions\ScopeNotFound
	 * @throws \Dracodeum\Kit\Components\Store\Exceptions\Conflict
	 * 
	 * @return void
	 */
	protected function halt(Uid $uid, string $type): void
	{
		//initialize
		$type = EHaltType::coerceValue($type);
		$prototype = $this->getPrototype();
		
		//halt
		switch ($type) {
			case EHaltType::NOT_FOUND:
				throw new Exceptions\NotFound([$this, $prototype, $uid]);
			case EHaltType::SCOPE_NOT_FOUND:
				throw new Exceptions\ScopeNotFound([$this, $prototype, $uid]);
			case EHaltType::CONFLICT:
				throw new Exceptions\Conflict([$this, $prototype, $uid]);
		}
		throw new Exceptions\Halted([$this, $prototype, $uid, $type]);
	}
}
