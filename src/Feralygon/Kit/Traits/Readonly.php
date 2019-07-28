<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Traits;

use Feralygon\Kit\Managers\Readonly as Manager;
use Feralygon\Kit\Traits\DebugInfo\Info as DebugInfo;
use Feralygon\Kit\Root\System;
use Feralygon\Kit\Root\System\Enumerations\DumpVerbosityLevel as EDumpVerbosityLevel;

/**
 * This trait enables read-only support for a class 
 * and may be used as an implementation of the <code>Feralygon\Kit\Interfaces\Readonlyable</code> interface.
 * 
 * @see \Feralygon\Kit\Interfaces\Readonlyable
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
	 * @param bool $recursive [default = false]
	 * <p>Check if it has been recursively set as read-only.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if is read-only.</p>
	 */
	final public function isReadonly(bool $recursive = false): bool
	{
		return $this->isReadonlyManagerLoaded() ? $this->getReadonlyManager()->isEnabled($recursive) : false;
	}
	
	/**
	 * Set as read-only.
	 * 
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
	 * @param \Feralygon\Kit\Traits\DebugInfo\Info $info
	 * <p>The debug info instance to process.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final protected function processReadonlyDebugInfo(DebugInfo $info): object
	{
		$readonly = $this->isReadonly();
		$info
			->set(
				'@readonly',
				System::getDumpVerbosityLevel() <= EDumpVerbosityLevel::LOW || $readonly === $this->isReadonly(true)
					? $readonly
					: $this->getReadonlyDebugInfo()
			)
			->hideObjectProperty('readonly_manager', self::class)
		;
		return $this;
	}
	
	
	
	//Final private methods
	/**
	 * Check if the read-only manager is loaded.
	 * 
	 * @return bool
	 * <p>Boolean <code>true</code> if the read-only manager is loaded.</p>
	 */
	final private function isReadonlyManagerLoaded(): bool
	{
		return isset($this->readonly_manager);
	}
	
	/**
	 * Get read-only manager instance.
	 * 
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
