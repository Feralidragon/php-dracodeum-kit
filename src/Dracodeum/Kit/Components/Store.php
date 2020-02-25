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
	 * @param \Dracodeum\Kit\Components\Store\Structures\Uid|array|string|float|int $uid
	 * <p>The UID to coerce, as an instance, <samp>name => value</samp> pairs, a string, a float or an integer.</p>
	 * @param bool $clone [default = false]
	 * <p>If an instance is given, then clone it into a new one with the same properties.</p>
	 * @return \Dracodeum\Kit\Components\Store\Structures\Uid
	 * <p>The given UID coerced into an instance.</p>
	 */
	final public function coerceUid($uid, bool $clone = false): Uid
	{
		$uid = Uid::coerce($uid, $clone);
		if ($uid->defaulted('scope') && $uid->base_scope !== null) {
			$uid->scope = $this->getUidScope($uid->base_scope, $uid->scope_values);
		}
		return $uid;
	}
	
	/**
	 * Get UID scope from a given base scope with a given set of scope values.
	 * 
	 * @param string $base_scope
	 * <p>The base scope to get from, optionally set with placeholders as <samp>{{placeholder}}</samp>, 
	 * corresponding directly to given scope values.<br>
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
	 * @param array $scope_values
	 * <p>The scope values to get with, as <samp>name => value</samp> pairs.</p>
	 * @return string
	 * <p>The UID scope from the given base scope with the given set of scope values.</p>
	 */
	final public function getUidScope(string $base_scope, array $scope_values): string
	{
		return empty($scope_values)
			? $base_scope
			: UText::fill($base_scope, $scope_values, null, [
				'stringifier' => \Closure::fromCallable([$this, 'getUidScopePlaceholderValueString'])
			]);
	}
	
	/**
	 * Check if a resource with a given UID exists.
	 * 
	 * @param \Dracodeum\Kit\Components\Store\Structures\Uid|array|string|float|int $uid
	 * <p>The UID to check with, as an instance, <samp>name => value</samp> pairs, a string, a float or an integer.</p>
	 * @throws \Dracodeum\Kit\Components\Store\Exceptions\MethodNotImplemented
	 * @return bool
	 * <p>Boolean <code>true</code> if the resource with the given UID exists.</p>
	 */
	final public function exists($uid): bool
	{
		$uid = $this->coerceUid($uid, true)->setAsReadonly();
		$prototype = $this->getPrototype();
		if ($prototype instanceof PrototypeInterfaces\Checker) {
			return $prototype->exists($uid, false);
		} elseif ($prototype instanceof PrototypeInterfaces\Returner) {
			return $prototype->return($uid, false) !== null;
		}
		throw new Exceptions\MethodNotImplemented([$this, $prototype, 'exists']);
	}
	
	/**
	 * Return a resource with a given UID.
	 * 
	 * @param \Dracodeum\Kit\Components\Store\Structures\Uid|array|string|float|int $uid
	 * <p>The UID to return with, as an instance, <samp>name => value</samp> pairs, a string, a float or an integer.</p>
	 * @throws \Dracodeum\Kit\Components\Store\Exceptions\MethodNotImplemented
	 * @return array|null
	 * <p>The resource with the given UID, as <samp>name => value</samp> pairs, or <code>null</code> if none is set.</p>
	 */
	final public function return($uid): ?array
	{
		$uid = $this->coerceUid($uid, true)->setAsReadonly();
		$prototype = $this->getPrototype();
		if ($prototype instanceof PrototypeInterfaces\Returner) {
			return $prototype->return($uid, false);
		}
		throw new Exceptions\MethodNotImplemented([$this, $prototype, 'return']);
	}
	
	/**
	 * Insert a resource with a given UID with a given set of values.
	 * 
	 * @param \Dracodeum\Kit\Components\Store\Structures\Uid|array|string|float|int $uid [reference]
	 * <p>The UID to insert with, as an instance, <samp>name => value</samp> pairs, a string, a float or an integer.<br>
	 * It is coerced into an instance, and may be modified during insertion, 
	 * such as when any of its properties is automatically generated.</p>
	 * @param array $values
	 * <p>The values to insert with, as <samp>name => value</samp> pairs.</p>
	 * @throws \Dracodeum\Kit\Components\Store\Exceptions\MethodNotImplemented
	 * @return array
	 * <p>The inserted values of the resource with the given UID, as <samp>name => value</samp> pairs.</p>
	 */
	final public function insert(&$uid, array $values): array
	{
		$insert_uid = $this->coerceUid($uid, true);
		$prototype = $this->getPrototype();
		if ($prototype instanceof PrototypeInterfaces\Inserter) {
			$inserted_values = $prototype->insert($insert_uid, $values);
			$uid = $insert_uid->setAsReadonly();
			return $inserted_values;
		}
		throw new Exceptions\MethodNotImplemented([$this, $prototype, 'insert']);
	}
	
	
	/**
	 * Update a resource with a given UID with a given set of values.
	 * 
	 * @param \Dracodeum\Kit\Components\Store\Structures\Uid|array|string|float|int $uid
	 * <p>The UID to update with, as an instance, <samp>name => value</samp> pairs, a string, a float or an integer.</p>
	 * @param array $values
	 * <p>The values to update with, as <samp>name => value</samp> pairs.</p>
	 * @throws \Dracodeum\Kit\Components\Store\Exceptions\MethodNotImplemented
	 * @return array|null
	 * <p>The updated values of the resource with the given UID, as <samp>name => value</samp> pairs, 
	 * or <code>null</code> if the resource does not exist.</p>
	 */
	final public function update($uid, array $values): ?array
	{
		$uid = $this->coerceUid($uid, true)->setAsReadonly();
		$prototype = $this->getPrototype();
		if ($prototype instanceof PrototypeInterfaces\Updater) {
			return $prototype->update($uid, $values);
		}
		throw new Exceptions\MethodNotImplemented([$this, $prototype, 'update']);
	}
	
	/**
	 * Delete a resource with a given UID.
	 * 
	 * @param \Dracodeum\Kit\Components\Store\Structures\Uid|array|string|float|int $uid
	 * <p>The UID to delete with, as an instance, <samp>name => value</samp> pairs, a string, a float or an integer.</p>
	 * @throws \Dracodeum\Kit\Components\Store\Exceptions\MethodNotImplemented
	 * @return bool
	 * <p>Boolean <code>true</code> if the resource with the given UID was deleted.</p>
	 */
	final public function delete($uid): bool
	{
		$uid = $this->coerceUid($uid, true)->setAsReadonly();
		$prototype = $this->getPrototype();
		if ($prototype instanceof PrototypeInterfaces\Deleter) {
			return $prototype->delete($uid);
		}
		throw new Exceptions\MethodNotImplemented([$this, $prototype, 'delete']);
	}
}
