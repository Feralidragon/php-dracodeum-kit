<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Traits;

use Feralygon\Kit\Core\Traits\Memoization\{
	Options,
	Objects,
	Exceptions
};
use Feralygon\Kit\Core\Utilities\Data as UData;

/**
 * Core memoization trait.
 * 
 * This trait enables memoization capabilities into a class through the usage of protected methods focused exclusively into in-memory internal data caching.
 * 
 * @since 1.0.0
 */
trait Memoization
{
	//Private properties
	/** @var \Feralygon\Kit\Core\Traits\Memoization\Objects\Entry[] */
	private $memoize_entries = [];
	
	/** @var \Feralygon\Kit\Core\Traits\Memoization\Options\Policy[] */
	private $memoize_policy_options = [];
	
	/** @var string */
	private $memoize_selector = 'data';
	
	
	
	//Private static properties
	/** @var \Feralygon\Kit\Core\Traits\Memoization\Objects\Entry[] */
	private static $memoize_static_entries = [];
	
	/** @var \Feralygon\Kit\Core\Traits\Memoization\Options\Policy[] */
	private static $memoize_static_policy_options = [];
	
	/** @var string */
	private static $memoize_static_selector = 'data';
	
	
	
	//Final protected methods
	/**
	 * Check if has a given memoized key.
	 * 
	 * @since 1.0.0
	 * @param string $key <p>The memoization key to check.</p>
	 * @param string $namespace [default = ''] <p>The memoization namespace to check from.</p>
	 * @param mixed $value [reference output] [default = null] <p>The memoized value corresponding to the given checked key.</p>
	 * @return bool <p>Boolean <samp>true</samp> if has the given memoized key.</p>
	 */
	final protected function hasMemoizedKey(string $key, string $namespace = '', &$value = null) : bool
	{
		$value = null;
		if (isset($this->memoize_entries[$this->memoize_selector][$namespace][$key])) {
			$entry = $this->memoize_entries[$this->memoize_selector][$namespace][$key];
			if (isset($entry->expire) && $entry->expire < time()) {
				unset($this->memoize_entries[$this->memoize_selector][$namespace][$key]);
				if (empty($this->memoize_entries[$this->memoize_selector][$namespace])) {
					unset($this->memoize_entries[$this->memoize_selector][$namespace]);
				}
				return false;
			}
			$value = $entry->value;
			return true;
		}
		return false;
	}
	
	/**
	 * Get memoized value from a given key.
	 * 
	 * @since 1.0.0
	 * @param string $key <p>The memoized key to get from.</p>
	 * @param string $namespace [default = ''] <p>The memoized namespace to get from.</p>
	 * @throws \Feralygon\Kit\Core\Traits\Memoization\Exceptions\NoMemoizedValueFound
	 * @return mixed <p>The memoized value from the given key.</p>
	 */
	final protected function getMemoizedValue(string $key, string $namespace = '')
	{
		if (!$this->hasMemoizedKey($key, $namespace, $value)) {
			throw new Exceptions\NoMemoizedValueFound(['key' => $key, 'namespace' => $namespace]);
		}
		return $value;
	}
	
	/**
	 * Set a given memoized value into a given key.
	 * 
	 * @since 1.0.0
	 * @param string $key <p>The memoization key to set into.</p>
	 * @param mixed $value <p>The memoization value to set.</p>
	 * @param string $namespace [default = ''] <p>The memoization namespace to set into.</p>
	 * @return void
	 */
	final protected function setMemoizedValue(string $key, $value, string $namespace = '') : void
	{
		//initialize
		if (!isset($this->memoize_entries[$this->memoize_selector][$namespace])) {
			$this->memoize_entries[$this->memoize_selector][$namespace] = [];
		}
		$policy_options = $this->getMemoizationPolicyOptions($namespace);
		
		//limit
		if (isset($policy_options->limit) && !isset($this->memoize_entries[$this->memoize_selector][$namespace][$key])) {
			//expire
			$expires = count($this->memoize_entries[$this->memoize_selector][$namespace]) - $policy_options->limit + 1;
			if ($expires > 0) {
				$time = time();
				foreach ($this->memoize_entries[$this->memoize_selector][$namespace] as $entry_key => $entry) {
					if (isset($entry->expire) && $entry->expire < $time) {
						unset($this->memoize_entries[$this->memoize_selector][$namespace][$entry_key]);
						if (--$expires <= 0) {
							break;
						}
					}
				}
			}
			
			//evict
			$evictions = count($this->memoize_entries[$this->memoize_selector][$namespace]) - $policy_options->limit + 1;
			if ($evictions > 0) {
				if ($evictions >= count($this->memoize_entries[$this->memoize_selector][$namespace])) {
					$this->memoize_entries[$this->memoize_selector][$namespace] = [];
				} else {
					foreach ($this->memoize_entries[$this->memoize_selector][$namespace] as $entry_key => $entry) {
						unset($this->memoize_entries[$this->memoize_selector][$namespace][$entry_key]);
						if (--$evictions <= 0) {
							break;
						}
					}
				}
			}
		}
		
		//set
		$this->memoize_entries[$this->memoize_selector][$namespace][$key] = new Objects\Entry($value, isset($policy_options->ttl) ? time() + $policy_options->ttl : null);
	}

	/**
	 * Delete memoized key.
	 * 
	 * @since 1.0.0
	 * @param string $key <p>The memoized key to delete.</p>
	 * @param string $namespace [default = ''] <p>The memoized namespace to delete from.</p>
	 * @return bool <p>Boolean <samp>true</samp> if the given key existed and was deleted.</p>
	 */
	final protected function deleteMemoizedKey(string $key, string $namespace = '') : bool
	{
		if ($this->hasMemoizedKey($key, $namespace)) {
			unset($this->memoize_entries[$this->memoize_selector][$namespace][$key]);
			if (empty($this->memoize_entries[$this->memoize_selector][$namespace])) {
				unset($this->memoize_entries[$this->memoize_selector][$namespace]);
			}
			return true;
		}
		return false;
	}
	
	/**
	 * Get memoization policy options instance.
	 * 
	 * @since 1.0.0
	 * @param string $namespace [default = ''] <p>The memoized namespace to get from.</p>
	 * @return \Feralygon\Kit\Core\Traits\Memoization\Options\Policy <p>The memoization policy options instance.</p>
	 */
	final protected function getMemoizationPolicyOptions(string $namespace = '') : Options\Policy
	{
		if (!isset($this->memoize_policy_options[$this->memoize_selector][$namespace])) {
			$this->memoize_policy_options[$this->memoize_selector][$namespace] = new Options\Policy();
		}
		return $this->memoize_policy_options[$this->memoize_selector][$namespace];
	}
	
	/**
	 * Set memoization policy options.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Core\Traits\Memoization\Options\Policy|array|null $options <p>The memoization policy options to set, as an instance or <code>name => value</code> pairs.</p>
	 * @param string $namespace [default = ''] <p>The memoization namespace to set to.</p>
	 * @return void
	 */
	final protected function setMemoizationPolicyOptions($options, string $namespace = '') : void
	{
		$this->memoize_policy_options[$this->memoize_selector][$namespace] = Options\Policy::load($options);
	}
	
	
	
	//Final protected static methods
	/**
	 * Memoize a given function.
	 * 
	 * @since 1.0.0
	 * @see https://php.net/manual/en/language.oop5.late-static-bindings.php
	 * @param callable $function <p>The function to memoize.</p>
	 * @param string|null $key [default = null] <p>The key to memoize into.<br>
	 * If not set, the key is automatically generated from the caller function parameters.</p>
	 * @param string $namespace [default = ''] <p>The namespace to memoize into.</p>
	 * @param bool $local [default = false] <p>Use the local class as reference, in other words, 
	 * use the calling class (late static binding) instead of the declaring class.</p>
	 * @param \Feralygon\Kit\Core\Traits\Memoization\Options\Policy|array|null $policy_options [default = null] <p>The memoization policy options to use, as an instance or <code>name => value</code> pairs.</p>
	 * @return mixed <p>The memoized value returned from the given function.</p>
	 */
	final protected static function memoize(callable $function, ?string $key = null, string $namespace = '', bool $local = false, $policy_options = null)
	{
		//initialize
		$value = null;
		$backtrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 2)[1];
		if (!isset($key)) {
			$key = UData::keyfy($backtrace['args'], $safe);
			if (!$safe) {
				return $function();
			}
		}
		$namespace = "{$backtrace['class']}::{$backtrace['function']}" . ($namespace === '' ? '' : ":{$namespace}");
		
		//object
		if ($backtrace['type'] === '->') {
			$object = $backtrace['object'];
			$previous_selector = $object->memoize_selector;
			try {
				$object->memoize_selector = 'functions';
				if (!$object->hasMemoizedKey($key, $namespace, $value)) {
					//policy options
					$object_policy_options = null;
					if (isset($policy_options)) {
						$object_policy_options = $object->getMemoizationPolicyOptions($namespace);
						$object->setMemoizationPolicyOptions($policy_options, $namespace);
					}
					
					//set
					$object->memoize_selector = $previous_selector;
					$value = $function();
					$object->memoize_selector = 'functions';
					$object->setMemoizedValue($key, $value, $namespace);
					if (isset($object_policy_options)) {
						$object->setMemoizationPolicyOptions($object_policy_options, $namespace);
					}
				}
			} finally {
				$object->memoize_selector = $previous_selector;
			}
			return $value;
		}
		
		//class
		$previous_selector = self::$memoize_static_selector;
		try {
			self::$memoize_static_selector = 'functions';
			$class = $local ? static::class : self::class;
			if (!$class::hasMemoizedStaticKey($key, $namespace, $local, $value)) {
				//policy options
				$class_policy_options = null;
				if (isset($policy_options)) {
					$class_policy_options = $class::getMemoizationStaticPolicyOptions($namespace, $local);
					$class::setMemoizationStaticPolicyOptions($policy_options, $namespace, $local);
				}
				
				//set
				self::$memoize_static_selector = $previous_selector;
				$value = $function();
				self::$memoize_static_selector = 'functions';
				$class::setMemoizedStaticValue($key, $value, $namespace, $local);
				if (isset($class_policy_options)) {
					$class::setMemoizationStaticPolicyOptions($class_policy_options, $namespace, $local);
				}
			}
		} finally {
			self::$memoize_static_selector = $previous_selector;
		}
		return $value;
	}
	
	/**
	 * Check if has a given memoized static key.
	 * 
	 * @since 1.0.0
	 * @param string $key <p>The memoization key to check.</p>
	 * @param string $namespace [default = ''] <p>The memoization namespace to check from.</p>
	 * @param bool $local [default = false] <p>Use the local class as reference, in other words, 
	 * use the calling class (late static binding) instead of the declaring class.</p>
	 * @param mixed $value [reference output] [default = null] <p>The memoized value corresponding to the given checked key.</p>
	 * @return bool <p>Boolean <samp>true</samp> if has the given memoized static key.</p>
	 */
	final protected static function hasMemoizedStaticKey(string $key, string $namespace = '', bool $local = false, &$value = null) : bool
	{
		$value = null;
		$class = $local ? static::class : self::class;
		if (isset(self::$memoize_static_entries[$class][self::$memoize_static_selector][$namespace][$key])) {
			$entry = self::$memoize_static_entries[$class][self::$memoize_static_selector][$namespace][$key];
			if (isset($entry->expire) && $entry->expire < time()) {
				unset(self::$memoize_static_entries[$class][self::$memoize_static_selector][$namespace][$key]);
				if (empty(self::$memoize_static_entries[$class][self::$memoize_static_selector][$namespace])) {
					unset(self::$memoize_static_entries[$class][self::$memoize_static_selector][$namespace]);
				}
				return false;
			}
			$value = $entry->value;
			return true;
		}
		return false;
	}
	
	/**
	 * Get memoized static value from a given key.
	 * 
	 * @since 1.0.0
	 * @param string $key <p>The memoized key to get from.</p>
	 * @param string $namespace [default = ''] <p>The memoized namespace to get from.</p>
	 * @param bool $local [default = false] <p>Use the local class as reference, in other words, 
	 * use the calling class (late static binding) instead of the declaring class.</p>
	 * @throws \Feralygon\Kit\Core\Traits\Memoization\Exceptions\NoMemoizedValueFound
	 * @return mixed <p>The memoized static value from the given key.</p>
	 */
	final protected static function getMemoizedStaticValue(string $key, string $namespace = '', bool $local = false)
	{
		if (!static::hasMemoizedStaticKey($key, $namespace, $local, $value)) {
			throw new Exceptions\NoMemoizedValueFound(['key' => $key, 'namespace' => $namespace]);
		}
		return $value;
	}
	
	/**
	 * Set a given memoized static value into a given key.
	 * 
	 * @since 1.0.0
	 * @param string $key <p>The memoization key to set into.</p>
	 * @param mixed $value <p>The memoization value to set.</p>
	 * @param string $namespace [default = ''] <p>The memoization namespace to set into.</p>
	 * @param bool $local [default = false] <p>Use the local class as reference, in other words, 
	 * use the calling class (late static binding) instead of the declaring class.</p>
	 * @return void
	 */
	final protected static function setMemoizedStaticValue(string $key, $value, string $namespace = '', bool $local = false) : void
	{
		//initialize
		$class = $local ? static::class : self::class;
		if (!isset(self::$memoize_static_entries[$class][self::$memoize_static_selector][$namespace])) {
			self::$memoize_static_entries[$class][self::$memoize_static_selector][$namespace] = [];
		}
		$policy_options = static::getMemoizationStaticPolicyOptions($namespace, $local);
		
		//limit
		if (isset($policy_options->limit) && !isset(self::$memoize_static_entries[$class][self::$memoize_static_selector][$namespace][$key])) {
			//expire
			$expires = count(self::$memoize_static_entries[$class][self::$memoize_static_selector][$namespace]) - $policy_options->limit + 1;
			if ($expires > 0) {
				$time = time();
				foreach (self::$memoize_static_entries[$class][self::$memoize_static_selector][$namespace] as $entry_key => $entry) {
					if (isset($entry->expire) && $entry->expire < $time) {
						unset(self::$memoize_static_entries[$class][self::$memoize_static_selector][$namespace][$entry_key]);
						if (--$expires <= 0) {
							break;
						}
					}
				}
			}
			
			//evict
			$evictions = count(self::$memoize_static_entries[$class][self::$memoize_static_selector][$namespace]) - $policy_options->limit + 1;
			if ($evictions > 0) {
				if ($evictions >= count(self::$memoize_static_entries[$class][self::$memoize_static_selector][$namespace])) {
					self::$memoize_static_entries[$class][self::$memoize_static_selector][$namespace] = [];
				} else {
					foreach (self::$memoize_static_entries[$class][self::$memoize_static_selector][$namespace] as $entry_key => $entry) {
						unset(self::$memoize_static_entries[$class][self::$memoize_static_selector][$namespace][$entry_key]);
						if (--$evictions <= 0) {
							break;
						}
					}
				}
			}
		}
		
		//set
		self::$memoize_static_entries[$class][self::$memoize_static_selector][$namespace][$key] = new Objects\Entry($value, isset($policy_options->ttl) ? time() + $policy_options->ttl : null);
	}
	
	/**
	 * Delete memoized static key.
	 * 
	 * @since 1.0.0
	 * @param string $key <p>The memoized key to delete.</p>
	 * @param string $namespace [default = ''] <p>The memoized namespace to delete from.</p>
	 * @param bool $local [default = false] <p>Use the local class as reference, in other words, 
	 * use the calling class (late static binding) instead of the declaring class.</p>
	 * @return bool <p>Boolean <samp>true</samp> if the given key existed and was deleted.</p>
	 */
	final protected static function deleteMemoizedStaticKey(string $key, string $namespace = '', bool $local = false) : bool
	{
		if (static::hasMemoizedStaticKey($key, $namespace, $local)) {
			$class = $local ? static::class : self::class;
			unset(self::$memoize_static_entries[$class][self::$memoize_static_selector][$namespace][$key]);
			if (empty(self::$memoize_static_entries[$class][self::$memoize_static_selector][$namespace])) {
				unset(self::$memoize_static_entries[$class][self::$memoize_static_selector][$namespace]);
			}
			return true;
		}
		return false;
	}
	
	/**
	 * Get memoization static policy options instance.
	 * 
	 * @since 1.0.0
	 * @param string $namespace [default = ''] <p>The memoization namespace to get from.</p>
	 * @param bool $local [default = false] <p>Use the local class as reference, in other words, 
	 * use the calling class (late static binding) instead of the declaring class.</p>
	 * @return \Feralygon\Kit\Core\Traits\Memoization\Options\Policy <p>The memoization static policy options instance.</p>
	 */
	final protected static function getMemoizationStaticPolicyOptions(string $namespace = '', bool $local = false) : Options\Policy
	{
		$class = $local ? static::class : self::class;
		if (!isset(self::$memoize_static_policy_options[$class][self::$memoize_static_selector][$namespace])) {
			self::$memoize_static_policy_options[$class][self::$memoize_static_selector][$namespace] = new Options\Policy();
		}
		return self::$memoize_static_policy_options[$class][self::$memoize_static_selector][$namespace];
	}
	
	/**
	 * Set memoization static policy options.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Core\Traits\Memoization\Options\Policy|array|null $options <p>The memoization policy options to set, as an instance or <code>name => value</code> pairs.</p>
	 * @param string $namespace [default = ''] <p>The memoization namespace to set to.</p>
	 * @param bool $local [default = false] <p>Use the local class as reference, in other words, 
	 * use the calling class (late static binding) instead of the declaring class.</p>
	 * @return void
	 */
	final protected static function setMemoizationStaticPolicyOptions($options, string $namespace = '', bool $local = false) : void
	{
		self::$memoize_static_policy_options[$local ? static::class : self::class][self::$memoize_static_selector][$namespace] = Options\Policy::load($options);
	}
}
