<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Managers\Memoization;

use Feralygon\Kit\Managers\Memoization\Store\{
	Key,
	Exceptions
};
use Feralygon\Kit\Utilities\{
	Call as UCall,
	Data as UData
};

/**
 * @since 1.0.0
 * @internal
 * @see \Feralygon\Kit\Managers\Memoization
 */
final class Store
{
	//Private properties
	/** @var int|null */
	private $limit = null;
	
	/** @var \Feralygon\Kit\Managers\Memoization\Store\Key[] */
	private $keys = [];
	
	/** @var \Feralygon\Kit\Managers\Memoization\Store\Key[] */
	private $expiring_keys = [];
	
	
	
	//Final public magic methods
	/**
	 * Instantiate class.
	 * 
	 * @since 1.0.0
	 * @param int|null $limit [default = null]
	 * <p>The limit on the number of keys.<br>
	 * If not set, then no limit is applied, otherwise it must be greater than <code>0</code>.</p>
	 */
	final public function __construct(?int $limit = null)
	{
		UCall::guardParameter('limit', $limit, !isset($limit) || $limit > 0, [
			'hint_message' => "Only null or a value greater than 0 is allowed."
		]);
		$this->limit = $limit;
	}
	
	
	
	//Final public methods
	/**
	 * Check if has key with a given name.
	 * 
	 * @since 1.0.0
	 * @param string $name
	 * <p>The name to check with.</p>
	 * @param \Feralygon\Kit\Managers\Memoization\Store\Key|null $key [reference output] [default = null]
	 * <p>The key instance with the given name.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if has key with the given name.</p>
	 */
	final public function hasKey(string $name, ?Key &$key = null): bool
	{
		$key = null;
		if (isset($this->keys[$name])) {
			$key = $this->keys[$name];
			if ($key->hasExpiry() && $key->getExpiry() < microtime(true)) {
				$key = null;
				$this->unsetKey($name);
				return false;
			}
			return true;
		}
		return false;
	}
	
	/**
	 * Get key instance with a given name.
	 * 
	 * @since 1.0.0
	 * @param string $name
	 * <p>The name to get with.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Feralygon\Kit\Managers\Memoization\Store\Exceptions\KeyNotFound
	 * @return \Feralygon\Kit\Managers\Memoization\Store\Key|null
	 * <p>The key instance with the given name.<br>
	 * If <var>$no_throw</var> is set to <code>true</code>, then <code>null</code> is returned if it was not found.</p>
	 */
	final public function getKey(string $name, bool $no_throw = false): ?Key
	{
		if (!$this->hasKey($name, $key)) {
			if ($no_throw) {
				return null;
			}
			throw new Exceptions\KeyNotFound(['store' => $this, 'name' => $name]);
		}
		return $key;
	}
	
	/**
	 * Set key with a given name and value.
	 * 
	 * @since 1.0.0
	 * @param string $name
	 * <p>The name to set with.</p>
	 * @param mixed $value
	 * <p>The value to set with.</p>
	 * @param float|null $ttl [default = null]
	 * <p>The TTL (Time to Live) to set with, in seconds.<br>
	 * If not set, then no TTL is applied, otherwise it must be greater than <code>0</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setKey(string $name, $value, ?float $ttl = null): Store
	{
		//initialize
		UCall::guardParameter('ttl', $ttl, !isset($ttl) || $ttl > 0.0, [
			'hint_message' => "Only null or a value greater than 0 is allowed."
		]);
		if ($this->hasKey($name)) {
			$this->unsetKey($name);
		}
		
		//limit
		if (isset($this->limit)) {
			//expiring
			if (!empty($this->expiring_keys) && count($this->keys) >= $this->limit) {
				foreach ($this->expiring_keys as $key_name => $key) {
					if ($key->getExpiry() < microtime(true)) {
						$this->unsetKey($key_name);
					}
				}
				unset($key);
			}
			
			//evict
			while (count($this->keys) >= $this->limit) {
				$this->unsetKey(UData::kfirst(empty($this->expiring_keys) ? $this->keys : $this->expiring_keys));
			}
		}
		
		//key
		$this->keys[$name] = new Key($name, $value, isset($ttl) ? microtime(true) + $ttl : null);
		if (isset($ttl)) {
			$this->expiring_keys[$name] = $this->keys[$name];
		}
		
		//return
		return $this;
	}
	
	/**
	 * Unset key with a given name.
	 * 
	 * @since 1.0.0
	 * @param string $name
	 * <p>The name to unset with.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function unsetKey(string $name): Store
	{
		unset($this->keys[$name], $this->expiring_keys[$name]);
		return $this;
	}
}
