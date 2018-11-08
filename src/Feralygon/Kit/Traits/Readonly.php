<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Traits;

use Feralygon\Kit\Managers\Readonly as Manager;

/**
 * This trait enables read-only support for a class 
 * and may be used as an implementation of the <code>Feralygon\Kit\Interfaces\Readonlyable</code> interface.
 * 
 * @since 1.0.0
 */
trait Readonly
{
	//Private properties
	/** @var \Feralygon\Kit\Managers\Readonly|null */
	private $readonly_manager = null;
	
	
	
	//Final public methods
	/**
	 * Check if is read-only.
	 * 
	 * @since 1.0.0
	 * @param bool $recursive [default = false]
	 * <p>Check if it has been recursively set as read-only.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if is read-only.</p>
	 */
	final public function isReadonly(bool $recursive = false): bool
	{
		return $this->getReadonlyManager()->isEnabled($recursive);
	}
	
	/**
	 * Set as read-only.
	 * 
	 * @since 1.0.0
	 * @param bool $recursive [default = false]
	 * <p>Set all possible referenced subobjects as read-only recursively (if applicable).</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsReadonly(bool $recursive = false): object
	{
		$this->getReadonlyManager()->enable($recursive);
		return $this;
	}
	
	
	
	//Final protected methods
	/**
	 * Guard the current function or method in the stack from being called if this object is set as read-only.
	 * 
	 * @since 1.0.0
	 * @return void
	 */
	final protected function guardNonReadonlyCall(): void
	{
		$this->getReadonlyManager()->guardCall(1);
	}
	
	/**
	 * Add read-only callback function.
	 * 
	 * All read-only callback functions are called upon read-only enablement.<br>
	 * <br>
	 * This method may only be called before read-only enablement.
	 * 
	 * @since 1.0.0
	 * @param callable $callback
	 * <p>The callback function to add.<br>
	 * It is expected to be compatible with the following signature:<br>
	 * <br>
	 * <code>function (bool $recursive): void</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>bool $recursive</b></code><br>
	 * &nbsp; &nbsp; &nbsp; Enable recursively.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final protected function addReadonlyCallback(callable $callback): object
	{
		$this->getReadonlyManager()->addCallback($callback);
		return $this;
	}
	
	
	
	//Final private methods
	/**
	 * Get read-only manager instance.
	 * 
	 * @since 1.0.0
	 * @return \Feralygon\Kit\Managers\Readonly
	 * <p>The read-only manager instance.</p>
	 */
	final private function getReadonlyManager(): Manager
	{
		if (!isset($this->readonly_manager)) {
			$this->readonly_manager = new Manager($this);
		}
		return $this->readonly_manager;
	}
}
