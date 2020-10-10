<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Managers;

use Dracodeum\Kit\{
	Manager,
	Traits
};
use Dracodeum\Kit\Interfaces\DebugInfo as IDebugInfo;
use Dracodeum\Kit\Traits\DebugInfo\Interfaces\DebugInfoProcessor as IDebugInfoProcessor;
use Dracodeum\Kit\Traits\DebugInfo\Info as DebugInfo;
use Dracodeum\Kit\Utilities\Call as UCall;

/** This manager handles the read-only state and the resulting callback functions of an object. */
class Readonly extends Manager implements IDebugInfo, IDebugInfoProcessor
{
	//Traits
	use Traits\DebugInfo;
	
	
	
	//Private properties
	/** @var object */
	private $owner;
	
	/** @var bool */
	private $enabled = false;
	
	/** @var bool */
	private $recursive = false;
	
	/** @var \Closure[] */
	private $callbacks = [];
	
	
	
	//Final public magic methods
	/**
	 * Instantiate class.
	 * 
	 * @param object $owner
	 * <p>The owner object to instantiate with.</p>
	 */
	final public function __construct(object $owner)
	{
		$this->owner = $owner;
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Traits\DebugInfo\Interfaces\DebugInfoProcessor)
	/** {@inheritdoc} */
	public function processDebugInfo(DebugInfo $info): void
	{
		$info->set('enabled', $this->enabled)->set('recursive', $this->recursive);
	}
	
	
	
	//Public methods
	/**
	 * Clone into a new instance for a given owner.
	 * 
	 * @param object $owner
	 * <p>The owner object to clone for.</p>
	 * @return \Dracodeum\Kit\Managers\Readonly
	 * <p>The new cloned instance from this one for the given owner.</p>
	 */
	public function cloneForOwner(object $owner): Readonly
	{
		$clone = new static($owner);
		$clone->callbacks = $this->callbacks;
		return $clone;
	}
	
	
	
	//Final public methods
	/**
	 * Get owner object.
	 * 
	 * @return object
	 * <p>The owner object.</p>
	 */
	final public function getOwner(): object
	{
		return $this->owner;
	}
	
	/**
	 * Check if is enabled.
	 * 
	 * @param bool $recursive [default = false]
	 * <p>Check if it has been recursively enabled.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if is enabled.</p>
	 */
	final public function isEnabled(bool $recursive = false): bool
	{
		return $this->enabled && (!$recursive || $this->recursive);
	}
	
	/**
	 * Enable.
	 * 
	 * @param bool $recursive [default = false]
	 * <p>Enable recursively.<br>
	 * <br>
	 * Any potential recursion may only be implemented in the callback functions.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function enable(bool $recursive = false): Readonly
	{
		if (!$this->isEnabled($recursive)) {
			foreach ($this->callbacks as $callback) {
				$callback($recursive);
			}
			$this->enabled = true;
			$this->recursive = $recursive;
		}
		return $this;
	}
	
	/**
	 * Add callback function.
	 * 
	 * All callback functions are called upon enablement.<br>
	 * <br>
	 * This method may only be called before enablement.
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
	final public function addCallback(callable $callback): Readonly
	{
		UCall::guard(!$this->enabled, [
			'hint_message' => "This method may only be called before enablement, in manager with owner {{owner}}.",
			'parameters' => ['owner' => $this->owner]
		]);
		UCall::assert('callback', $callback, function (bool $recursive): void {});
		$this->callbacks[] = \Closure::fromCallable($callback);
		return $this;
	}
	
	/**
	 * Guard the current function or method in the stack so it may only be called if this instance is not enabled.
	 * 
	 * @param int $stack_offset [default = 0]
	 * <p>The stack offset to use.</p>
	 * @return void
	 */
	final public function guardCall(int $stack_offset = 0): void
	{
		UCall::guard(!$this->enabled, [
			'error_message' => "This method cannot be called as this object is currently set as read-only.",
			'stack_offset' => $stack_offset + 1
		]);
	}
}
