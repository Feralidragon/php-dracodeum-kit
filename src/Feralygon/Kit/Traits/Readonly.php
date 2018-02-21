<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Traits;

use Feralygon\Kit\Managers\Readonly as Manager;
use Feralygon\Kit\Traits\Readonly\Exceptions;
use Feralygon\Kit\Utilities\Call as UCall;

/**
 * Read-only trait.
 * 
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
	 * @return bool <p>Boolean <code>true</code> if is read-only.</p>
	 */
	final public function isReadonly() : bool
	{
		$this->guardReadonlyManagerCall();
		return $this->readonly_manager->isEnabled();
	}
	
	/**
	 * Set as read-only.
	 * 
	 * @since 1.0.0
	 * @return $this <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsReadonly()
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
	final protected function guardNonReadonlyCall() : void
	{
		$this->guardReadonlyManagerCall();
		$this->readonly_manager->guardCall(1);
	}
	
	
	
	//Final private methods
	/**
	 * Initialize read-only.
	 * 
	 * @since 1.0.0
	 * @param callable[] $callbacks [default = []] <p>The callback functions to call upon read-only enablement.<br>
	 * Each one is expected to be compatible with the following signature:<br><br>
	 * <code>function () : void</code>
	 * </p>
	 * @throws \Feralygon\Kit\Traits\Readonly\Exceptions\ReadonlyAlreadyInitialized
	 * @return void
	 */
	final private function initializeReadonly(array $callbacks = []) : void
	{
		//manager
		if (isset($this->readonly_manager)) {
			throw new Exceptions\ReadonlyAlreadyInitialized(['object' => $this]);
		}
		$this->readonly_manager = new Manager($this);
		
		//callbacks
		foreach ($callbacks as $callback) {
			$this->readonly_manager->addCallback($callback);
		}
	}
	
	/**
	 * Guard the current function or method in the stack from being called until the read-only manager 
	 * has been initialized.
	 * 
	 * @since 1.0.0
	 * @return void
	 */
	final private function guardReadonlyManagerCall() : void
	{
		UCall::guard(
			isset($this->readonly_manager),
			"This method may only be called after the read-only manager initialization.",
			null, 1
		);
	}
}
