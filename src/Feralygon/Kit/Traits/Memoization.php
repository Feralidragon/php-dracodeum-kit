<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Traits;

use Feralygon\Kit\Managers\Memoization as Manager;
use Feralygon\Kit\Utilities\Call as UCall;

/**
 * This trait enables memoization support for a class.
 * 
 * @since 1.0.0
 */
trait Memoization
{
	//Private properties
	/** @var \Feralygon\Kit\Managers\Memoization|null */
	private $memoization_manager = null;
	
	
	
	//Private static properties
	/** @var \Feralygon\Kit\Managers\Memoization[] */
	private static $memoization_static_managers = [];
	
	
	
	//Final protected methods
	/**
	 * Get memoization manager instance.
	 * 
	 * @since 1.0.0
	 * @return \Feralygon\Kit\Managers\Memoization
	 * <p>The memoization manager instance.</p>
	 */
	final protected function getMemoizationManager() : Manager
	{
		if (!isset($this->memoization_manager)) {
			$this->memoization_manager = new Manager($this);
		}
		return $this->memoization_manager;
	}
	
	
	
	//Final protected static methods
	/**
	 * Get memoization static manager instance.
	 * 
	 * @since 1.0.0
	 * @return \Feralygon\Kit\Managers\Memoization
	 * <p>The memoization static manager instance.</p>
	 */
	final protected static function getMemoizationStaticManager() : Manager
	{
		if (!isset(self::$memoization_static_managers[static::class])) {
			self::$memoization_static_managers[static::class] = new Manager(static::class);
		}
		return self::$memoization_static_managers[static::class];
	}
	
	/**
	 * Memoize a given function for the previous caller method in the stack.
	 * 
	 * The memoization of a given function consists in calling it only once and caching its returning value.<br>
	 * In other words, after the given function is called once, 
	 * all subsequent call attempts will return the cached value instead.<br>
	 * <br>
	 * This method is specifically meant to fully memoize the previous caller method in the stack, 
	 * thus the given function is expected to be a mirror of that method, or to have a compatible signature at least, 
	 * since it is called and cached with the same arguments the previous caller method was called with.
	 * 
	 * @since 1.0.0
	 * @param callable $function
	 * <p>The function to memoize (call and cache).<br>
	 * It is expected to be compatible with the signature from the previous caller method in the stack.</p>
	 * @param float|null $ttl [default = null]
	 * <p>The TTL (Time to Live) to memoize with, in seconds.<br>
	 * If not set, then no TTL is applied, otherwise it must be greater than <code>0</code>.</p>
	 * @param bool $persist [default = false]
	 * <p><i>Not implemented</i> (TODO).</p>
	 * @param array $key_parameters [default = []]
	 * <p>Additional parameters to generate the internal memoization key with, as <samp>name => value</samp> pairs.</p>
	 * @return mixed
	 * <p>The memoized value returned from the given function for the previous caller method in the stack.</p>
	 */
	final protected static function memoize(
		callable $function, ?float $ttl = null, bool $persist = false, array $key_parameters = []
	)
	{
		//backtrace
		$backtrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 2)[1];
		
		//object
		if ($backtrace['type'] === '->') {
			UCall::assert('function', $function, [$backtrace['object'], $backtrace['function']]);
			return $backtrace['object']->getMemoizationManager()->memoizeFunction(
				$backtrace['function'], $function, $backtrace['args'], $ttl, $persist, $key_parameters
			);
		}
		
		//class
		if ($backtrace['class'] !== UCall::class) {
			UCall::assert('function', $function, [$backtrace['class'], $backtrace['function']]);
		}
		return static::getMemoizationStaticManager()->memoizeFunction(
			$backtrace['function'], $function, $backtrace['args'], $ttl, $persist, $key_parameters
		);
	}
}
