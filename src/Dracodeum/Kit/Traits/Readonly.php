<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Traits;

use Dracodeum\Kit\Managers\Readonly as Manager;
use Dracodeum\Kit\Traits\DebugInfo\Info as DebugInfo;

/**
 * This trait enables read-only support for a class 
 * and may be used as an implementation of the <code>Dracodeum\Kit\Interfaces\Readonlyable</code> interface.
 * 
 * @see \Dracodeum\Kit\Interfaces\Readonlyable
 */
trait Readonly
{
	//Private properties
	/** @var \Dracodeum\Kit\Managers\Readonly|null */
	private $readonly_manager = null;
	
	
	
	//Final public methods
	/**
	 * Check if is read-only.
	 * 
	 * @return bool
	 * <p>Boolean <code>true</code> if is read-only.</p>
	 */
	final public function isReadonly(): bool
	{
		return $this->isReadonlyManagerLoaded() ? $this->getReadonlyManager()->isEnabled() : false;
	}
	
	/**
	 * Set as read-only.
	 * 
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsReadonly(): object
	{
		$this->getReadonlyManager()->enable();
		return $this;
	}
	
	
	
	//Final protected methods
	/**
	 * Guard the current function or method in the stack so it may only be called if this object is not set as 
	 * read-only.
	 * 
	 * @return void
	 */
	final protected function guardNonReadonlyCall(): void
	{
		if ($this->isReadonlyManagerLoaded()) {
			$this->getReadonlyManager()->guardCall(1);
		}
	}
	
	/**
	 * Add read-only callback function.
	 * 
	 * All read-only callback functions are called upon read-only enablement.<br>
	 * <br>
	 * This method may only be called before read-only enablement.
	 * 
	 * @param callable $callback
	 * <p>The callback function to add.<br>
	 * It is expected to be compatible with the following signature:<br>
	 * <br>
	 * <code>function (): void</code><br>
	 * </p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final protected function addReadonlyCallback(callable $callback): object
	{
		$this->getReadonlyManager()->addCallback($callback);
		return $this;
	}
	
	/**
	 * Get read-only debug info.
	 * 
	 * @see https://www.php.net/manual/en/language.oop5.magic.php#object.debuginfo
	 * @return array
	 * <p>The read-only debug info.</p>
	 */
	final protected function getReadonlyDebugInfo(): array
	{
		return $this->getReadonlyManager()->getDebugInfo();
	}
	
	/**
	 * Process a given read-only debug info instance.
	 * 
	 * @param \Dracodeum\Kit\Traits\DebugInfo\Info $info
	 * <p>The debug info instance to process.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final protected function processReadonlyDebugInfo(DebugInfo $info): object
	{
		$info->set('@readonly', $this->isReadonly())->hideObjectProperty('readonly_manager', self::class);
		return $this;
	}
	
	/**
	 * Process read-only cloning.
	 * 
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final protected function processReadonlyCloning(): object
	{
		if (isset($this->readonly_manager)) {
			$this->readonly_manager = $this->readonly_manager->cloneForOwner($this);
		}
		return $this;
	}
	
	
	
	//Private methods
	/**
	 * Check if the read-only manager is loaded.
	 * 
	 * @return bool
	 * <p>Boolean <code>true</code> if the read-only manager is loaded.</p>
	 */
	private function isReadonlyManagerLoaded(): bool
	{
		return isset($this->readonly_manager);
	}
	
	/**
	 * Get read-only manager instance.
	 * 
	 * @return \Dracodeum\Kit\Managers\Readonly
	 * <p>The read-only manager instance.</p>
	 */
	private function getReadonlyManager(): Manager
	{
		if (!isset($this->readonly_manager)) {
			$this->readonly_manager = new Manager($this);
		}
		return $this->readonly_manager;
	}
}
