<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components;

use Dracodeum\Kit\Component;
use Dracodeum\Kit\Components\Store\Exceptions;
use Dracodeum\Kit\Factories\Component as Factory;
use Dracodeum\Kit\Prototypes\{
	Store as Prototype,
	Stores as Prototypes
};
use Dracodeum\Kit\Prototypes\Store\Interfaces as PrototypeInterfaces;

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
	
	
	
	//Final public methods
	/**
	 * Check if a resource with a given name and UID (unique identifier) exists.
	 * 
	 * @param string $name
	 * <p>The name to check with.</p>
	 * @param mixed $uid
	 * <p>The UID (unique identifier) to check with.</p>
	 * @param string|null $scope [default = null]
	 * <p>The scope to check with.</p>
	 * @throws \Dracodeum\Kit\Components\Store\Exceptions\MethodNotImplemented
	 * @return bool
	 * <p>Boolean <code>true</code> if a resource with the given name and UID (unique identifier) exists.</p>
	 */
	final public function exists(string $name, $uid, ?string $scope = null): bool
	{
		$prototype = $this->getPrototype();
		if ($prototype instanceof PrototypeInterfaces\Checker) {
			return $prototype->exists($name, $uid, $scope, false);
		} elseif ($prototype instanceof PrototypeInterfaces\Returner) {
			return $prototype->return($name, $uid, $scope, false) !== null;
		}
		throw new Exceptions\MethodNotImplemented([$this, $prototype, 'exists']);
	}
	
	/**
	 * Return a resource with a given name and UID (unique identifier).
	 * 
	 * @param string $name
	 * <p>The name to return with.</p>
	 * @param mixed $uid
	 * <p>The UID (unique identifier) to return with.</p>
	 * @param string|null $scope [default = null]
	 * <p>The scope to return with.</p>
	 * @throws \Dracodeum\Kit\Components\Store\Exceptions\MethodNotImplemented
	 * @return array|null
	 * <p>The resource with the given name and UID (unique identifier), as <samp>name => value</samp> pairs, 
	 * or <code>null</code> if none is set.</p>
	 */
	final public function return(string $name, $uid, ?string $scope = null): ?array
	{
		$prototype = $this->getPrototype();
		if ($prototype instanceof PrototypeInterfaces\Returner) {
			return $prototype->return($name, $uid, $scope, false);
		}
		throw new Exceptions\MethodNotImplemented([$this, $prototype, 'return']);
	}
	
	/**
	 * Insert a resource with a given name and UID (unique identifier) with a given set of values.
	 * 
	 * @param string $name
	 * <p>The name to insert with.</p>
	 * @param mixed $uid [reference]
	 * <p>The UID (unique identifier) to insert with.<br>
	 * It may be modified during insertion into a new one, such as when it is meant to be automatically generated.</p>
	 * @param array $values
	 * <p>The values to insert with, as <samp>name => value</samp> pairs.</p>
	 * @param string|null $scope [default = null]
	 * <p>The scope to insert with.</p>
	 * @throws \Dracodeum\Kit\Components\Store\Exceptions\MethodNotImplemented
	 * @return array
	 * <p>The inserted values of the resource with the given name and UID (unique identifier), 
	 * as <samp>name => value</samp> pairs.</p>
	 */
	final public function insert(string $name, &$uid, array $values, ?string $scope = null): array
	{
		$prototype = $this->getPrototype();
		if ($prototype instanceof PrototypeInterfaces\Inserter) {
			$insert_uid = $uid;
			$inserted_values = $prototype->insert($name, $insert_uid, $values, $scope);
			$uid = $insert_uid;
			return $inserted_values;
		}
		throw new Exceptions\MethodNotImplemented([$this, $prototype, 'insert']);
	}
	
	/**
	 * Update a resource with a given name and UID (unique identifier) with a given set of values.
	 * 
	 * @param string $name
	 * <p>The name to update with.</p>
	 * @param mixed $uid
	 * <p>The UID (unique identifier) to update with.</p>
	 * @param array $values
	 * <p>The values to update with, as <samp>name => value</samp> pairs.</p>
	 * @param string|null $scope [default = null]
	 * <p>The scope to update with.</p>
	 * @throws \Dracodeum\Kit\Components\Store\Exceptions\MethodNotImplemented
	 * @return array|null
	 * <p>The updated values of the resource with the given name and UID (unique identifier), 
	 * as <samp>name => value</samp> pairs, or <code>null</code> if the resource does not exist.</p>
	 */
	final public function update(string $name, $uid, array $values, ?string $scope = null): ?array
	{
		$prototype = $this->getPrototype();
		if ($prototype instanceof PrototypeInterfaces\Updater) {
			return $prototype->update($name, $uid, $values, $scope);
		}
		throw new Exceptions\MethodNotImplemented([$this, $prototype, 'update']);
	}
	
	/**
	 * Delete a resource with a given name and UID (unique identifier).
	 * 
	 * @param string $name
	 * <p>The name to delete with.</p>
	 * @param mixed $uid
	 * <p>The UID (unique identifier) to delete with.</p>
	 * @param string|null $scope [default = null]
	 * <p>The scope to delete with.</p>
	 * @throws \Dracodeum\Kit\Components\Store\Exceptions\MethodNotImplemented
	 * @return bool
	 * <p>Boolean <code>true</code> if the resource with the given name and UID (unique identifier) was deleted.</p>
	 */
	final public function delete(string $name, $uid, ?string $scope = null): bool
	{
		$prototype = $this->getPrototype();
		if ($prototype instanceof PrototypeInterfaces\Deleter) {
			return $prototype->delete($name, $uid, $scope);
		}
		throw new Exceptions\MethodNotImplemented([$this, $prototype, 'delete']);
	}
}
