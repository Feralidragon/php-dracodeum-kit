<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Managers;

use Feralygon\Kit\Managers\Memoization\{
	Objects,
	Exceptions
};
use Feralygon\Kit\Utilities\Type as UType;

/**
 * This manager handles global in-memory internal data caching for object classes (memoization), 
 * resulting from both method and explicit data caching calls.
 * 
 * Memoized data may also be cached persistently, to be shared across different requests and processes.
 * 
 * @todo Support persistent cache.
 * 
 * @since 1.0.0
 */
class Memoization
{
	//Private constants
	/** Data selector. */
	private const SELECTOR_DATA = 'data';
	
	/** Functions selector. */
	private const SELECTOR_FUNCTIONS = 'functions';
	
	/**
	 * The TTL (Time to Live) of each entry, in seconds.<br>
	 * If not set, then no TTL is applied, otherwise it must always be greater than <code>0</code>.
	 */
	private const CONFIG_TTL = null;
	
	/**
	 * The limit on the number of entries.<br>
	 * If not set, then no limit is applied, otherwise it must always be greater than <code>0</code>.
	 */
	private const CONFIG_LIMIT = null;
	
	
	
	//Private properties
	/** @var object|string */
	private $owner;
	
	/** @var string */
	private $selector = self::SELECTOR_DATA;
	
	
	
	//Private static properties
	/** @var \Feralygon\Kit\Managers\Memoization\Objects\Entry[] */
	private static $entries = [];
	
	/** @var \Feralygon\Kit\Managers\Memoization\Objects\Entry[] */
	private static $entries_tree = [];
	
	
	
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
	 * @param string $namespace [default = '']
	 * <p>The namespace to check at.</p>
	 * @param mixed $value [reference output] [default = null]
	 * <p>The value at the given key.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if has value at the given key.</p>
	 */
	final public function hasValue(string $key, string $namespace = '', &$value = null) : bool
	{
		//initialize
		$value = null;
		$class = UType::class($this->owner);
		$selector = $this->selector;
		
		//check
		if (isset(self::$entries_tree[$class][$selector][$namespace][$key])) {
			$entry = self::$entries_tree[$selector][$namespace][$key];
			if (isset($entry->expire) && $entry->expire < time()) {
				$this->deleteValue($key, $namespace);
				return false;
			}
			$value = $entry->value;
			return true;
		}
		return false;
	}
	
	/**
	 * Get value from a given key.
	 * 
	 * @since 1.0.0
	 * @param string $key
	 * <p>The key to get from.</p>
	 * @param string $namespace [default = '']
	 * <p>The namespace to get from.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Feralygon\Kit\Managers\Memoization\Exceptions\ValueNotFound
	 * @return mixed
	 * <p>The value from the given key.<br>
	 * If <var>$no_throw</var> is set to <code>true</code>, 
	 * then <code>null</code> may also be returned if none is set.</p>
	 */
	final public function getValue(string $key, string $namespace = '', bool $no_throw = false)
	{
		if (!$this->hasValue($key, $namespace, $value)) {
			if ($no_throw) {
				return null;
			}
			throw new Exceptions\ValueNotFound(['manager' => $this, 'key' => $key, 'namespace' => $namespace]);
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
	 * @param string $namespace [default = '']
	 * <p>The namespace to set at.</p>
	 * 
	 * @todo
	 * 
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final protected function setValue(
		string $key, $value, string $namespace = '', ?int $ttl = null, bool $persist = false
	) : Memoization
	{
		//TODO
	}
	
	/**
	 * Delete value from a given key.
	 * 
	 * @since 1.0.0
	 * @param string $key
	 * <p>The key to delete from.</p>
	 * @param string $namespace [default = '']
	 * <p>The namespace to delete from.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function deleteValue(string $key, string $namespace = '') : Memoization
	{
		//initialize
		$class = UType::class($this->owner);
		$selector = $this->selector;
		
		//delete
		if (isset(self::$entries_tree[$class][$selector][$namespace][$key])) {
			//entry
			$entry = self::$entries_tree[$class][$selector][$namespace][$key];
			unset(self::$entries_tree[$class][$selector][$namespace][$key], self::$entries[$entry->index]);
			
			//clean
			if (empty(self::$entries_tree[$class][$selector][$namespace])) {
				unset(self::$entries_tree[$class][$selector][$namespace]);
				if (empty(self::$entries_tree[$class][$selector])) {
					unset(self::$entries_tree[$class][$selector]);
					if (empty(self::$entries_tree[$class])) {
						unset(self::$entries_tree[$class]);
					}
				}
			}
		}
		
		//return
		return $this;
	}
}
