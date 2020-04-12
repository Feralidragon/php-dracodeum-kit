<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components;

use Dracodeum\Kit\Component;
use Dracodeum\Kit\Components\Store\Exceptions;
use Dracodeum\Kit\Components\Store\Structures\Uid;
use Dracodeum\Kit\Factories\Component as Factory;
use Dracodeum\Kit\Prototypes\{
	Store as Prototype,
	Stores as Prototypes
};
use Dracodeum\Kit\Prototypes\Store\Interfaces as PrototypeInterfaces;
use Dracodeum\Kit\Utilities\Text as UText;

/**
 * This component represents a store which performs persistent CRUD operations of resources, namely create (insert), 
 * read (exists and return), update and delete.
 * 
 * @see \Dracodeum\Kit\Prototypes\Store
 * @see \Dracodeum\Kit\Prototypes\Stores\Memory
 * [prototype, name = 'memory' or 'mem']
 */
class Store extends Component
{
	//Implemented public static methods
	/** {@inheritdoc} */
	public static function getBasePrototypeClass(): string
	{
		return Prototype::class;
	}
	
	
	
	//Implemented protected static methods (Dracodeum\Kit\Component\Traits\DefaultBuilder)
	/** {@inheritdoc} */
	protected static function getDefaultBuilder(): ?callable
	{
		return [Factory::class, 'store'];
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
	 * Get string for a given UID scope placeholder from a given value.
	 * 
	 * @param string $placeholder
	 * <p>The placeholder to get for.</p>
	 * @param mixed $value
	 * <p>The value to get from.</p>
	 * @return string|null
	 * <p>The string for the given UID scope placeholder from the given value or <code>null</code> if none is set.</p>
	 */
	protected function getUidScopePlaceholderValueString(string $placeholder, $value): ?string
	{
		$prototype = $this->getPrototype();
		return $prototype instanceof PrototypeInterfaces\UidScopePlaceholderValueString
			? $prototype->getUidScopePlaceholderValueString($placeholder, $value)
			: null;
	}
	
	
	
	//Final public methods
	/**
	 * Coerce a given UID into an instance.
	 * 
	 * @param \Dracodeum\Kit\Components\Store\Structures\Uid|array|string|float|int|null $uid
	 * <p>The UID to coerce, as an instance, <samp>name => value</samp> pairs, a string, a float, an integer 
	 * or <code>null</code>.</p>
	 * @param bool|null $clone_recursive [default = null]
	 * <p>Clone the given UID recursively.<br>
	 * If set to boolean <code>false</code> and an instance is given, then clone it into a new one with the same 
	 * properties, but not recursively.<br>
	 * If not set, then the given UID is not cloned.</p>
	 * @return \Dracodeum\Kit\Components\Store\Structures\Uid
	 * <p>The given UID coerced into an instance.</p>
	 */
	final public function coerceUid($uid, ?bool $clone_recursive = null): Uid
	{
		$uid = Uid::coerce($uid, $clone_recursive);
		if ($uid->defaulted('scope') && $uid->base_scope !== null) {
			$uid->scope = $this->getUidScope($uid->base_scope, $uid->scope_ids);
		}
		return $uid;
	}
	
	/**
	 * Get UID scope from a given base scope with a given set of scope IDs.
	 * 
	 * @param string $base_scope
	 * <p>The base scope to get from, optionally set with placeholders as <samp>{{placeholder}}</samp>, 
	 * corresponding directly to given scope IDs.<br>
	 * <br>
	 * If set, then it cannot be empty, and placeholders must be exclusively composed by identifiers, 
	 * which are defined as words which must start with a letter (<samp>a-z</samp> and <samp>A-Z</samp>) 
	 * or underscore (<samp>_</samp>), and may only contain letters (<samp>a-z</samp> and <samp>A-Z</samp>), 
	 * digits (<samp>0-9</samp>) and underscores (<samp>_</samp>).<br>
	 * <br>
	 * They may also be used with pointers to specific object properties or associative array values, 
	 * by using a dot between identifiers, such as <samp>{{object.property}}</samp>, 
	 * with no limit on the number of chained pointers.<br>
	 * <br>
	 * If suffixed with opening and closing parenthesis, such as <samp>{{object.method()}}</samp>, 
	 * then the identifiers are interpreted as getter method calls, but they cannot be given any arguments.</p>
	 * @param int[]|float[]|string[] $scope_ids
	 * <p>The scope IDs to get with, as <samp>name => id</samp> pairs.</p>
	 * @return string
	 * <p>The UID scope from the given base scope with the given set of scope IDs.</p>
	 */
	final public function getUidScope(string $base_scope, array $scope_ids): string
	{
		return UText::hasPlaceholders($base_scope)
			? UText::fill($base_scope, Uid::coerceScopeIds($scope_ids), null, [
				'stringifier' => \Closure::fromCallable([$this, 'getUidScopePlaceholderValueString'])
			])
			: $base_scope;
	}
	
	/**
	 * Check if a resource with a given UID exists.
	 * 
	 * @param \Dracodeum\Kit\Components\Store\Structures\Uid|array|string|float|int|null $uid
	 * <p>The UID to check with, as an instance, <samp>name => value</samp> pairs, a string, a float, an integer 
	 * or <code>null</code>.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the resource with the given UID exists.</p>
	 */
	final public function exists($uid): bool
	{
		//uid
		$uid = $this->coerceUid($uid, true)->setAsReadonly();
		
		//prototype
		$prototype = $this->getPrototype();
		if ($prototype instanceof PrototypeInterfaces\Checker) {
			return $prototype->exists($uid, false);
		} elseif ($prototype instanceof PrototypeInterfaces\Returner) {
			return $prototype->return($uid, false) !== null;
		}
		
		//halt
		$this->haltPrototypeMethodNotImplemented('exists');
	}
	
	/**
	 * Return a resource with a given UID.
	 * 
	 * @param \Dracodeum\Kit\Components\Store\Structures\Uid|array|string|float|int|null $uid
	 * <p>The UID to return with, as an instance, <samp>name => value</samp> pairs, a string, a float, an integer 
	 * or <code>null</code>.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Components\Store\Exceptions\NotFound
	 * @return array|null
	 * <p>The resource with the given UID, as <samp>name => value</samp> pairs.<br>
	 * If <var>$no_throw</var> is set to boolean <code>true</code>, 
	 * then <code>null</code> is returned if it was not found.</p>
	 */
	final public function return($uid, bool $no_throw = false): ?array
	{
		//uid
		$uid = $this->coerceUid($uid, true)->setAsReadonly();
		
		//prototype
		$prototype = $this->getPrototype();
		if ($prototype instanceof PrototypeInterfaces\Returner) {
			$values = $prototype->return($uid, false);
			if ($values === null) {
				if ($no_throw) {
					return null;
				}
				throw new Exceptions\NotFound([$this, $prototype, $uid]);
			}
			return $values;
		}
		
		//halt
		$this->haltPrototypeMethodNotImplemented('return');
	}
	
	/**
	 * Insert a resource with a given UID with a given set of values.
	 * 
	 * @param \Dracodeum\Kit\Components\Store\Structures\Uid|array|string|float|int|null $uid [reference]
	 * <p>The UID to insert with, as an instance, <samp>name => value</samp> pairs, a string, a float, an integer 
	 * or <code>null</code>.<br>
	 * It is coerced into an instance, and may be modified during insertion, 
	 * such as when any of its properties are automatically generated.</p>
	 * @param array $values
	 * <p>The values to insert with, as <samp>name => value</samp> pairs.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Components\Store\Exceptions\Conflict
	 * @return array|null
	 * <p>The inserted values of the resource with the given UID, as <samp>name => value</samp> pairs.<br>
	 * If <var>$no_throw</var> is set to boolean <code>true</code>, 
	 * then <code>null</code> is returned if the resource already exists.</p>
	 */
	final public function insert(&$uid, array $values, bool $no_throw = false): ?array
	{
		//uid
		$insert_uid = $this->coerceUid($uid, true);
		
		//prototype
		$prototype = $this->getPrototype();
		if ($prototype instanceof PrototypeInterfaces\Inserter) {
			$inserted_values = $prototype->insert($insert_uid, $values);
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
	 * Update a resource with a given UID with a given set of values.
	 * 
	 * @param \Dracodeum\Kit\Components\Store\Structures\Uid|array|string|float|int|null $uid
	 * <p>The UID to update with, as an instance, <samp>name => value</samp> pairs, a string, a float, an integer 
	 * or <code>null</code>.</p>
	 * @param array $values
	 * <p>The values to update with, as <samp>name => value</samp> pairs.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Components\Store\Exceptions\NotFound
	 * @return array|null
	 * <p>The updated values of the resource with the given UID, as <samp>name => value</samp> pairs.<br>
	 * If <var>$no_throw</var> is set to boolean <code>true</code>, 
	 * then <code>null</code> is returned if the resource was not found.</p>
	 */
	final public function update($uid, array $values, bool $no_throw = false): ?array
	{
		//uid
		$uid = $this->coerceUid($uid, true)->setAsReadonly();
		
		//prototype
		$prototype = $this->getPrototype();
		if ($prototype instanceof PrototypeInterfaces\Updater) {
			$updated_values = $prototype->update($uid, $values);
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
	 * Delete a resource with a given UID.
	 * 
	 * @param \Dracodeum\Kit\Components\Store\Structures\Uid|array|string|float|int|null $uid
	 * <p>The UID to delete with, as an instance, <samp>name => value</samp> pairs, a string, a float, an integer 
	 * or <code>null</code>.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Components\Store\Exceptions\NotFound
	 * @return void|bool
	 * <p>If <var>$no_throw</var> is set to boolean <code>true</code>, 
	 * then boolean <code>true</code> is returned if the resource with the given UID was found and deleted, 
	 * or boolean <code>false</code> if otherwise.</p>
	 */
	final public function delete($uid, bool $no_throw = false)
	{
		//uid
		$uid = $this->coerceUid($uid, true)->setAsReadonly();
		
		//prototype
		$prototype = $this->getPrototype();
		if ($prototype instanceof PrototypeInterfaces\Deleter) {
			$deleted = $prototype->delete($uid);
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
}
