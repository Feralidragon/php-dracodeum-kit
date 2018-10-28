<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Traits;

use Feralygon\Kit\Managers\Readonly as Manager;
use Feralygon\Kit\Utilities\Call as UCall;

/**
 * This trait enables read-only support for a class.
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
	 * @return bool
	 * <p>Boolean <code>true</code> if is read-only.</p>
	 */
	final public function isReadonly(): bool
	{
		$this->guardReadonlyManagerCall();
		return $this->readonly_manager->isEnabled();
	}
	
	/**
	 * Set as read-only.
	 * 
	 * @since 1.0.0
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsReadonly(): object
	{
		$this->guardReadonlyManagerCall();
		$this->readonly_manager->enable();
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
		$this->guardReadonlyManagerCall();
		$this->readonly_manager->guardCall(1);
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
	 * <code>function (): void</code></p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final protected function addReadonlyCallback(callable $callback): object
	{
		$this->guardReadonlyManagerCall();
		$this->readonly_manager->addCallback($callback);
		return $this;
	}
	
	
	
	//Final private methods
	/**
	 * Initialize read-only.
	 * 
	 * @since 1.0.0
	 * @param bool $enable [default = false]
	 * <p>Enable the read-only state.</p>
	 * @param callable[] $callbacks [default = []]
	 * <p>The callback functions to call upon read-only enablement.<br>
	 * Each one is expected to be compatible with the following signature:<br>
	 * <br>
	 * <code>function (): void</code></p>
	 * @return void
	 */
	final private function initializeReadonly(bool $enable = false, array $callbacks = []): void
	{
		//initialize
		UCall::guard(!isset($this->readonly_manager), [
			'error_message' => "Read-only support has already been initialized."
		]);
		$this->readonly_manager = new Manager($this);
		
		//callbacks
		foreach ($callbacks as $callback) {
			$this->readonly_manager->addCallback($callback);
		}
		
		//enable
		if ($enable) {
			$this->readonly_manager->enable();
		}
	}
	
	/**
	 * Guard the current function or method in the stack from being called until the read-only manager 
	 * has been initialized.
	 * 
	 * @since 1.0.0
	 * @return void
	 */
	final private function guardReadonlyManagerCall(): void
	{
		UCall::guard(isset($this->readonly_manager), [
			'hint_message' => "This method may only be called after the read-only manager initialization.",
			'stack_offset' => 1
		]);
	}
}
