<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Managers;

use Feralygon\Kit\Managers\Readonly\Exceptions;
use Feralygon\Kit\Utilities\Call as UCall;

/**
 * Read-only manager class.
 * 
 * This manager handles the read-only state and callbacks of an object.
 * 
 * @since 1.0.0
 */
class Readonly
{
	//Private properties
	/** @var object */
	private $owner;
	
	/** @var bool */
	private $enabled = false;
	
	/** @var \Closure[] */
	private $callbacks = [];
	
	
	
	//Final public magic methods
	/**
	 * Instantiate class.
	 * 
	 * @since 1.0.0
	 * @param object $owner <p>The owner object.</p>
	 * @throws \Feralygon\Kit\Managers\Readonly\Exceptions\InvalidOwner
	 */
	final public function __construct($owner)
	{
		if (!is_object($owner)) {
			throw new Exceptions\InvalidOwner(['manager' => $this, 'owner' => $owner]);
		}
		$this->owner = $owner;
	}
	
	
	
	//Final public methods
	/**
	 * Get owner object.
	 * 
	 * @since 1.0.0
	 * @return object <p>The owner object.</p>
	 */
	final public function getOwner()
	{
		return $this->owner;
	}
	
	/**
	 * Check if is enabled.
	 * 
	 * @since 1.0.0
	 * @return bool <p>Boolean <code>true</code> if is enabled.</p>
	 */
	final public function isEnabled() : bool
	{
		return $this->enabled;
	}
	
	/**
	 * Enable.
	 * 
	 * @since 1.0.0
	 * @return $this <p>This instance, for chaining purposes.</p>
	 */
	final public function enable() : Readonly
	{
		if (!$this->enabled) {
			foreach ($this->callbacks as $callback) {
				$callback();
			}
			$this->enabled = true;
		}
	}
	
	/**
	 * Add callback function.
	 * 
	 * All callback functions are called upon enablement.
	 * 
	 * @since 1.0.0
	 * @param callable $callback <p>The callback function to add.<br>
	 * It is expected to be compatible with the following signature:<br><br>
	 * <code>function () : void</code><br>
	 * <br>
	 * Return: <code><b>void</b></code>
	 * </p>
	 * @return $this <p>This instance, for chaining purposes.</p>
	 */
	final public function addCallback(callable $callback) : Readonly
	{
		if ($this->enabled) {
			
			//TODO: throw exception
			
		}
		
		UCall::assert('callback', $callback, function () : void {}, true);
		$this->callbacks[] = \Closure::fromCallable($callback);
		return $this;
	}
	
	/**
	 * Guard the current function or method in the stack from being called if this is enabled.
	 * 
	 * @since 1.0.0
	 * @param int $stack_offset [default = 0] <p>The stack offset to use.<br>
	 * It must be greater than or equal to <code>0</code>.</p>
	 * @return void
	 */
	final public function guardCall(int $stack_offset = 0) : void
	{
		UCall::guard(
			!$this->readonly,
			"This method cannot be called as this object is currently set as read-only.",
			null, $stack_offset + 1
		);
	}
}
