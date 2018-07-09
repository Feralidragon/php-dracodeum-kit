<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Managers;

use Feralygon\Kit\Managers\Memoization\{
	Store,
	Exceptions
};
use Feralygon\Kit\Utilities\{
	Call as UCall,
	Data as UData,
	Type as UType
};

/**
 * This manager handles in-memory internal data caching for objects and classes (memoization).
 * 
 * Memoized data may also be cached persistently, to be shared across different requests and processes (TODO).
 * 
 * @todo Support persistent cache.
 * 
 * @since 1.0.0
 */
class Memoization
{
	//Private constants
	/** Values selector. */
	private const SELECTOR_VALUES = 'values';
	
	/** Functions selector. */
	private const SELECTOR_FUNCTIONS = 'functions';
	
	/**
	 * The TTL (Time to Live) of each key, in seconds.<br>
	 * If not set, then no TTL is applied, otherwise it must be greater than <code>0</code>.
	 */
	private const CONFIG_TTL = null;
	
	/**
	 * The limit on the number of keys.<br>
	 * If not set, then no limit is applied, otherwise it must be greater than <code>0</code>.
	 */
	private const CONFIG_LIMIT = null;
	
	
	
	//Private properties
	/** @var object|string */
	private $owner;
	
	/** @var \Feralygon\Kit\Managers\Memoization\Store[] */
	private $stores = [];
	
	
	
	//Private static properties
	/** @var \Feralygon\Kit\Managers\Memoization\Store[] */
	private static $static_stores = [];
	
	
	
	//Final public magic methods
	/**
	 * Instantiate class.
	 * 
	 * @since 1.0.0
	 * @param object|string $owner
	 * <p>The owner object or class.</p>
	 */
	final public function __construct($owner)
	{
		$this->owner = UType::coerceObjectClass($owner);
	}
	
	
	
	//Final public methods
	/**
	 * Get owner object or class.
	 * 
	 * @since 1.0.0
	 * @return object|string
	 * <p>The owner object or class.</p>
	 */
	final public function getOwner()
	{
		return $this->owner;
	}
	
	/**
	 * Check if has value at a given key.
	 * 
	 * @since 1.0.0
	 * @param string $key
	 * <p>The key to check at.</p>
	 * @param mixed $value [reference output] [default = null]
	 * <p>The value from the given key.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if has value at the given key.</p>
	 */
	final public function hasValue(string $key, &$value = null): bool
	{
		$value = null;
		if ($this->getStore(self::SELECTOR_VALUES)->hasKey($key, $key_instance)) {
			$value = $key_instance->getValue();
			return true;
		}
		return false;
	}
	
	/**
	 * Check if has values.
	 * 
	 * @since 1.0.0
	 * @return bool
	 * <p>Boolean <code>true</code> if has values.</p>
	 */
	final public function hasValues(): bool
	{
		return $this->getStore(self::SELECTOR_VALUES)->hasKeys();
	}
	
	/**
	 * Get value from a given key.
	 * 
	 * @since 1.0.0
	 * @param string $key
	 * <p>The key to get from.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Feralygon\Kit\Managers\Memoization\Exceptions\ValueNotSet
	 * @return mixed
	 * <p>The value from the given key.<br>
	 * If <var>$no_throw</var> is set to <code>true</code>, 
	 * then <code>null</code> may also be returned if none is set.</p>
	 */
	final public function getValue(string $key, bool $no_throw = false)
	{
		if (!$this->hasValue($key, $value)) {
			if ($no_throw) {
				return null;
			}
			throw new Exceptions\ValueNotSet(['manager' => $this, 'key' => $key]);
		}
		return $value;
	}
	
	/**
	 * Set a given value at a given key.
	 * 
	 * @since 1.0.0
	 * @param string $key
	 * <p>The key to set at.</p>
	 * @param mixed $value
	 * <p>The value to set.</p>
	 * @param float|null $ttl [default = null]
	 * <p>The TTL (Time to Live) to set with, in seconds.<br>
	 * If not set, then no TTL is applied, otherwise it must be greater than <code>0</code>.</p>
	 * @param bool $persist [default = false]
	 * <p><i>Not implemented</i> (TODO).</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setValue(string $key, $value, ?float $ttl = null, bool $persist = false): Memoization
	{
		//guard
		UCall::guardParameter('ttl', $ttl, !isset($ttl) || $ttl > 0.0, [
			'hint_message' => "Only null or a value greater than 0 is allowed."
		]);
		
		//ttl
		if (!isset($ttl)) {
			$ttl = self::CONFIG_TTL;
		} elseif (self::CONFIG_TTL !== null) {
			$ttl = min($ttl, self::CONFIG_TTL);
		}
		
		//set
		$this->getStore(self::SELECTOR_VALUES)->setKey($key, $value, $ttl);
		
		//return
		return $this;
	}
	
	/**
	 * Unset value with a given key.
	 * 
	 * @since 1.0.0
	 * @param string $key
	 * <p>The key to unset with.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function unsetValue(string $key): Memoization
	{
		$this->getStore(self::SELECTOR_VALUES)->unsetKey($key);
		return $this;
	}
	
	/**
	 * Memoize function with a given name.
	 * 
	 * The memoization of a given function consists in calling it only once and caching its returning value.<br>
	 * In other words, after the given function is called once, 
	 * all subsequent call attempts will return the cached value instead.
	 * 
	 * @since 1.0.0
	 * @param string $name
	 * <p>The name to memoize with.</p>
	 * @param callable $function
	 * <p>The function to memoize (call and cache).</p>
	 * @param array $arguments [default = []]
	 * <p>The arguments to memoize the given function with (call and cache).</p>
	 * @param float|null $ttl [default = null]
	 * <p>The TTL (Time to Live) to memoize with, in seconds.<br>
	 * If not set, then no TTL is applied, otherwise it must be greater than <code>0</code>.</p>
	 * @param bool $persist [default = false]
	 * <p><i>Not implemented</i> (TODO).</p>
	 * @param array $key_parameters [default = []]
	 * <p>Additional parameters to generate the internal memoization key with, as <samp>name => value</samp> pairs.</p>
	 * @return mixed
	 * <p>The memoized value returned from the given function with the given name.</p>
	 */
	final public function memoizeFunction(
		string $name, callable $function, array $arguments = [], ?float $ttl = null, bool $persist = false,
		array $key_parameters = []
	) {
		//key
		$key = UData::keyfy([$name, $arguments, $key_parameters], $safe);
		if (!$safe) {
			return $function(...$arguments);
		}
		
		//store
		$store = $this->getStore(self::SELECTOR_FUNCTIONS);
		if ($store->hasKey($key, $key_instance)) {
			return $key_instance->getValue();
		}
		
		//memoize
		$value = $function(...$arguments);
		$store->setKey($key, $value, $ttl);
		return $value;
	}
	
	
	
	//Final protected methods
	/**
	 * Get store instance for a given selector.
	 * 
	 * @since 1.0.0
	 * @param string $selector
	 * <p>The selector to get for.</p>
	 * @return \Feralygon\Kit\Managers\Memoization\Store
	 * <p>The store instance for the given selector.</p>
	 */
	final protected function getStore(string $selector): Store
	{
		if (!isset($this->stores[$selector])) {
			if (is_object($this->owner)) {
				$this->stores[$selector] = new Store(self::CONFIG_LIMIT);
			} else {
				$class = $this->owner;
				if (!isset(self::$static_stores[$class][$selector])) {
					self::$static_stores[$class][$selector] = new Store(self::CONFIG_LIMIT);
				}
				$this->stores[$selector] = self::$static_stores[$class][$selector];
			}
		}
		return $this->stores[$selector];
	}
}
