<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Traits;

use Feralygon\Kit\Traits\Functions\Exceptions;
use Feralygon\Kit\Utilities\Call as UCall;
use Feralygon\Kit\Root\System;

/**
 * This trait enables the support for a separate layer of custom functions in a class.
 * 
 * All these functions have their signatures validated, and are meant to be bound to existing functions or methods.<br>
 * They may also be set as bind-once, so that any given already bound function with the same name 
 * cannot be rebound to another.
 * 
 * @since 1.0.0
 */
trait Functions
{
	//Private properties
	/** @var \Closure[] */
	private $functions = [];
	
	/** @var bool */
	private $functions_initialized = false;
	
	/** @var \Closure|null */
	private $functions_templater = null;
	
	/** @var bool */
	private $functions_bindonce = false;
	
	
	
	//Final public methods
	/**
	 * Bind a given function to a given name.
	 * 
	 * This method may only be called after the functions initialization.
	 * 
	 * @since 1.0.0
	 * @param string $name
	 * <p>The name to bind to.</p>
	 * @param callable $function
	 * <p>The function to bind.</p>
	 * @throws \Feralygon\Kit\Traits\Functions\Exceptions\FunctionAlreadyBound
	 * @throws \Feralygon\Kit\Traits\Functions\Exceptions\FunctionNotFound
	 * @throws \Feralygon\Kit\Traits\Functions\Exceptions\InvalidFunction
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function bind(string $name, callable $function) : object
	{
		//guard
		UCall::guard($this->functions_initialized, [
			'hint_message' => "This method may only be called after the functions initialization."
		]);
		
		//bind-once
		if ($this->functions_bindonce && isset($this->functions[$name])) {
			throw new Exceptions\FunctionAlreadyBound(['object' => $this, 'name' => $name]);
		}
		
		//template
		$template = ($this->functions_templater)($name);
		if (!isset($template)) {
			throw new Exceptions\FunctionNotFound(['object' => $this, 'name' => $name]);
		} elseif (System::isDebug() && !UCall::isCompatible($function, $template)) {
			throw new Exceptions\InvalidFunction([
				'object' => $this,
				'name' => $name,
				'function' => $function,
				'template' => $template
			]);
		}
		
		//bind
		$this->functions[$name] = \Closure::fromCallable($function);
		
		//return
		return $this;
	}
	
	
	
	//Final protected methods
	/**
	 * Call function with a given name.
	 * 
	 * This method may only be called after the functions initialization.
	 * 
	 * @since 1.0.0
	 * @param string $name
	 * <p>The name to call.</p>
	 * @param mixed ...$arguments
	 * <p>The arguments to call with.</p>
	 * @throws \Feralygon\Kit\Traits\Functions\Exceptions\FunctionNotFound
	 * @return mixed
	 * <p>The returned value from the called function with the given name.</p>
	 */
	final protected function call(string $name, ...$arguments)
	{
		UCall::guard($this->functions_initialized, [
			'hint_message' => "This method may only be called after the functions initialization."
		]);
		if (!isset($this->functions[$name])) {
			throw new Exceptions\FunctionNotFound(['object' => $this, 'name' => $name]);
		}
		return ($this->functions[$name])(...$arguments);
	}
	
	
	
	//Final private methods
	/**
	 * Initialize functions with a given templater function.
	 * 
	 * @since 1.0.0
	 * @param callable $templater
	 * <p>The function to use to retrieve the function template for a given name.<br>
	 * It is expected to be compatible with the following signature:<br><br>
	 * <code>function (string $name) : ?callable</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>string $name</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The name to retrieve for.<br>
	 * <br>
	 * Return: <code><b>callable|null</b></code><br>
	 * The function template for the given name or <code>null</code> if none exists.</p>
	 * @param bool $bindonce [default = false]
	 * <p>Set functions as bind-once.</p>
	 * @throws \Feralygon\Kit\Traits\Functions\Exceptions\FunctionsAlreadyInitialized
	 * @return void
	 */
	final private function initializeFunctions(callable $templater, bool $bindonce = false) : void
	{
		//check
		if ($this->functions_initialized) {
			throw new Exceptions\FunctionsAlreadyInitialized(['object' => $this]);
		}
		
		//templater
		UCall::assert('templater', $templater, function (string $name) : ?callable {});
		$this->functions_templater = \Closure::fromCallable($templater);
		
		//finish
		$this->functions_initialized = true;
		$this->functions_bindonce = $bindonce;
	}
}
