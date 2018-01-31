<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Traits\ExtendedLazyProperties\Objects;

use Feralygon\Kit\Core\Traits\ExtendedLazyProperties\Objects\Property\Exceptions;
use Feralygon\Kit\Core\Traits\NoConstructor as TNoConstructor;
use Feralygon\Kit\Core\Utilities\Call as UCall;

/**
 * Core extended lazy properties trait property object class.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Core\Traits\ExtendedLazyProperties
 */
final class Property
{
	//Traits
	use TNoConstructor;
	
	
	
	//Private constants
	/** Allowed modes. */
	private const MODES = ['rw', 'r', 'w', 'w-'];
	
	
	
	//Private properties
	/** @var bool */
	private $initialized = false;
	
	/** @var string */
	private $mode = 'rw';
	
	/** @var mixed */
	private $value = null;
	
	/** @var bool */
	private $has_default_value = false;
	
	/** @var mixed */
	private $default_value = null;
	
	/** @var \Closure|null */
	private $evaluator = null;
	
	/** @var \Closure|null */
	private $getter = null;
	
	/** @var \Closure|null */
	private $setter = null;
	
	
	
	//Final public methods	
	/**
	 * Check if is initialized.
	 * 
	 * A property is only considered to have been initialized after a value or default value has been set, 
	 * or if either a getter or setter function is set.
	 * 
	 * @since 1.0.0
	 * @return bool <p>Boolean <code>true</code> if is initialized.</p>
	 */
	final public function isInitialized() : bool
	{
		return $this->initialized;
	}
	
	/**
	 * Get mode.
	 * 
	 * @since 1.0.0
	 * @return string <p>The mode.</p>
	 */
	final public function getMode() : string
	{
		return $this->mode;
	}
	
	/**
	 * Set mode.
	 * 
	 * @since 1.0.0
	 * @param string $mode <p>The read and write mode to set, which must be one the following:<br>
	 * &nbsp; &#8226; &nbsp; <samp>rw</samp> : Allow this property to be both read from and written to (read-write).<br>
	 * &nbsp; &#8226; &nbsp; <samp>r</samp> : Allow this property to be only read from (read-only).<br>
	 * &nbsp; &#8226; &nbsp; <samp>w</samp> : Allow this property to be only written to (write-only).<br>
	 * &nbsp; &#8226; &nbsp; <samp>w-</samp> : Allow this property to be only written to, 
	 * and only once during instantiation (write-once).
	 * </p>
	 * @throws \Feralygon\Kit\Core\Traits\ExtendedLazyProperties\Objects\Property\Exceptions\InvalidMode
	 * @return $this <p>This instance, for chaining purposes.</p>
	 */
	final public function setMode(string $mode) : Property
	{
		if (!in_array($mode, self::MODES, true)) {
			throw new Exceptions\InvalidMode(['property' => $this, 'mode' => $mode, 'modes' => self::MODES]);
		}
		$this->mode = $mode;
		return $this;
	}
	
	/**
	 * Get value.
	 * 
	 * @since 1.0.0
	 * @throws \Feralygon\Kit\Core\Traits\ExtendedLazyProperties\Objects\Property\Exceptions\MissingGetter
	 * @throws \Feralygon\Kit\Core\Traits\ExtendedLazyProperties\Objects\Property\Exceptions\NotInitialized
	 * @return mixed <p>The value.</p>
	 */
	final public function getValue()
	{
		if (isset($this->getter)) {
			return ($this->getter)();
		} elseif (isset($this->setter)) {
			throw new Exceptions\MissingGetter(['property' => $this]);
		} elseif (!$this->initialized) {
			throw new Exceptions\NotInitialized(['property' => $this]);
		}
		return $this->value;
	}
	
	/**
	 * Set value.
	 * 
	 * @since 1.0.0
	 * @param mixed $value <p>The value to set.</p>
	 * @throws \Feralygon\Kit\Core\Traits\ExtendedLazyProperties\Objects\Property\Exceptions\InvalidValue
	 * @throws \Feralygon\Kit\Core\Traits\ExtendedLazyProperties\Objects\Property\Exceptions\MissingSetter
	 * @return $this <p>This instance, for chaining purposes.</p>
	 */
	final public function setValue($value) : Property
	{
		//evaluator
		if (isset($this->evaluator) && !($this->evaluator)($value)) {
			throw new Exceptions\InvalidValue(['property' => $this, 'value' => $value]);
		}
		
		//set
		if (isset($this->setter)) {
			($this->setter)($value);
		} elseif (isset($this->getter)) {
			throw new Exceptions\MissingSetter(['property' => $this]);
		} else {
			$this->value = $value;
		}
		
		//finish
		$this->initialized = true;
		return $this;
	}
	
	/**
	 * Check if has default value.
	 * 
	 * @since 1.0.0
	 * @return bool <p>Boolean <code>true</code> if has default value.</p>
	 */
	final public function hasDefaultValue() : bool
	{
		return $this->has_default_value;
	}
	
	/**
	 * Get default value.
	 * 
	 * @since 1.0.0
	 * @throws \Feralygon\Kit\Core\Traits\ExtendedLazyProperties\Objects\Property\Exceptions\NoDefaultValueSet
	 * @return mixed <p>The default value.</p>
	 */
	final public function getDefaultValue()
	{
		if (!$this->has_default_value) {
			throw new Exceptions\NoDefaultValueSet(['property' => $this]);
		}
		return $this->default_value;
	}
	
	/**
	 * Set default value.
	 * 
	 * @since 1.0.0
	 * @param mixed $value <p>The default value to set.</p>
	 * @throws \Feralygon\Kit\Core\Traits\ExtendedLazyProperties\Objects\Property\Exceptions\InvalidValue
	 * @return $this <p>This instance, for chaining purposes.</p>
	 */
	final public function setDefaultValue($value) : Property
	{
		if (!$this->initialized) {
			$this->setValue($value);
		} elseif (isset($this->evaluator) && !($this->evaluator)($value)) {
			throw new Exceptions\InvalidValue(['property' => $this, 'value' => $value]);
		}
		$this->default_value = $value;
		$this->has_default_value = true;
		return $this;
	}
	
	/**
	 * Reset value.
	 * 
	 * @since 1.0.0
	 * @return $this <p>This instance, for chaining purposes.</p>
	 */
	final public function resetValue() : Property
	{
		$this->setValue($this->getDefaultValue());
		return $this;
	}
	
	/**
	 * Set evaluator function.
	 * 
	 * @since 1.0.0
	 * @param callable $evaluator <p>The evaluator function to set.<br>
	 * The expected function signature is represented as:<br><br>
	 * <code>function (&$value) : bool</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>mixed $value</b> [reference]</code> : 
	 * The value to evaluate (validate and sanitize).<br>
	 * <br>
	 * Return: <code><b>bool</b></code><br>
	 * Boolean <code>true</code> if the given value is valid.
	 * </p>
	 * @return $this <p>This instance, for chaining purposes.</p>
	 */
	final public function setEvaluator(callable $evaluator) : Property
	{
		//set
		UCall::assertSignature('evaluator', $evaluator, function (&$value) : bool {}, true);
		$this->evaluator = \Closure::fromCallable($evaluator);
		
		//values
		if ($this->has_default_value) {
			$this->setDefaultValue($this->default_value);
		}
		if ($this->initialized) {
			$this->setValue($this->value);
		}
		
		//return
		return $this;
	}
	
	/**
	 * Set getter function.
	 * 
	 * By setting a getter function, the value will be always retrieved using that function.
	 * 
	 * @since 1.0.0
	 * @param callable $getter <p>The getter function to set.</p>
	 * @return $this <p>This instance, for chaining purposes.</p>
	 */
	final public function setGetter(callable $getter) : Property
	{
		$this->getter = \Closure::fromCallable($getter);
		$this->value = null;
		$this->initialized = true;
		return $this;
	}
	
	/**
	 * Set setter function.
	 * 
	 * By setting a setter function, the value will be always set using that function.
	 * 
	 * @since 1.0.0
	 * @param callable $setter <p>The setter function to set.</p>
	 * @return $this <p>This instance, for chaining purposes.</p>
	 */
	final public function setSetter(callable $setter) : Property
	{
		$this->setter = \Closure::fromCallable($setter);
		$this->value = null;
		$this->initialized = true;
		return $this;
	}
}
