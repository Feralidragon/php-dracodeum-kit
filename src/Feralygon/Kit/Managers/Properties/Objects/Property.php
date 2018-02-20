<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Managers\Properties\Objects;

use Feralygon\Kit\Managers\Properties as Manager;
use Feralygon\Kit\Managers\Properties\Objects\Property\Exceptions;
use Feralygon\Kit\Enumeration;
use Feralygon\Kit\Utilities\{
	Call as UCall,
	Data as UData,
	Type as UType
};

/**
 * Properties manager property object class.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Managers\Properties
 */
class Property
{	
	//Private properties
	/** @var \Feralygon\Kit\Managers\Properties */
	private $manager;
	
	/** @var string */
	private $name;
	
	/** @var bool */
	private $initialized = false;
	
	/** @var bool */
	private $required = false;
	
	/** @var string|null */
	private $mode = null;
	
	/** @var mixed */
	private $value = null;
	
	/** @var \Closure|null */
	private $default_getter = null;
	
	/** @var \Closure|null */
	private $evaluator = null;
	
	/** @var \Closure|null */
	private $getter = null;
	
	/** @var \Closure|null */
	private $setter = null;
	
	
	
	//Final public magic methods
	/**
	 * Instantiate class.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Managers\Properties $manager <p>The manager instance.</p>
	 * @param string $name <p>The name.</p>
	 */
	final public function __construct(Manager $manager, string $name)
	{
		$this->manager = $manager;
		$this->name = $name;
	}
	
	
	
	//Final public methods
	/**
	 * Get manager instance.
	 * 
	 * @since 1.0.0
	 * @return \Feralygon\Kit\Managers\Properties <p>The manager instance.</p>
	 */
	final public function getManager() : Manager
	{
		return $this->manager;
	}
	
	/**
	 * Get name.
	 * 
	 * @since 1.0.0
	 * @return string <p>The name.</p>
	 */
	final public function getName() : string
	{
		return $this->name;
	}
	
	/**
	 * Check if is initialized.
	 * 
	 * A property becomes implicitly initialized once a value is set.
	 * 
	 * @since 1.0.0
	 * @return bool <p>Boolean <code>true</code> if is initialized.</p>
	 */
	final public function isInitialized() : bool
	{
		return $this->initialized;
	}
	
	/**
	 * Check if is required.
	 * 
	 * @since 1.0.0
	 * @return bool <p>Boolean <code>true</code> if is required.</p>
	 */
	final public function isRequired() : bool
	{
		return $this->required || !$this->hasDefaultValue() || 
			($this->manager->isLazy() && $this->manager->isRequiredPropertyName($this->name));
	}
	
	/**
	 * Initialize.
	 * 
	 * @since 1.0.0
	 * @throws \Feralygon\Kit\Managers\Properties\Objects\Property\Exceptions\AlreadyInitialized
	 * @return $this <p>This instance, for chaining purposes.</p>
	 */
	final public function initialize() : Property
	{
		if ($this->initialized) {
			throw new Exceptions\AlreadyInitialized(['property' => $this]);
		}
		$this->setValue($this->getDefaultValue());
		return $this;
	}
	
	/**
	 * Set as required.
	 * 
	 * Even without being explicitly set as required, a property is considered so if it has no default value set.<br>
	 * <br>
	 * This method may only be called before initialization, of both the property and the manager, 
	 * with lazy-loading disabled and only if the mode is not set to strict read-only.
	 * 
	 * @since 1.0.0
	 * @return $this <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsRequired() : Property
	{
		//guard
		UCall::guard(
			!$this->initialized,
			"This method may only be called before initialization."
		);
		UCall::guard(
			!$this->manager->isLazy(),
			"In order to set a property as required, with lazy-loading enabled, " . 
				"please use the manager \"addRequiredPropertyNames\" method instead."
		);
		UCall::guard(
			!$this->manager->isInitialized(),
			"This method may only be called before the manager initialization."
		);
		UCall::guard(
			$this->getMode() !== 'r',
			"A strictly read-only property cannot be set as required."
		);
		
		//set
		$this->required = true;
		
		//return
		return $this;
	}
	
	/**
	 * Get mode.
	 * 
	 * @since 1.0.0
	 * @return string <p>The mode.</p>
	 */
	final public function getMode() : string
	{
		return $this->mode ?? $this->manager->getMode();
	}
	
	/**
	 * Set mode.
	 * 
	 * @since 1.0.0
	 * @param string $mode <p>The access mode to set, which must be one the following:<br>
	 * &nbsp; &#8226; &nbsp; <samp>r</samp> : Allow this property to be only strictly read from, 
	 * so that it cannot be given during initialization (strict read-only).<br>
	 * &nbsp; &#8226; &nbsp; <samp>r+</samp> : Allow this property to be only read from (read-only), 
	 * although it may still be given during initialization.<br>
	 * &nbsp; &#8226; &nbsp; <samp>rw</samp> : Allow this property to be both read from and written to (read-write).<br>
	 * &nbsp; &#8226; &nbsp; <samp>w</samp> : Allow this property to be only written to (write-only).<br>
	 * &nbsp; &#8226; &nbsp; <samp>w-</samp> : Allow this property to be only written to, 
	 * but only once during initialization (write-once).<br>
	 * <br>
	 * NOTE: The allowed modes may be more restricted depending on the global mode set in the manager:<br>
	 * &nbsp; &#8226; &nbsp; if set to <samp>r</samp> or <samp>r+</samp>, 
	 * only <samp>r</samp>, <samp>r+</samp> and <samp>rw</samp> are allowed;<br>
	 * &nbsp; &#8226; &nbsp; if set to <samp>rw</samp>, all modes are allowed;<br>
	 * &nbsp; &#8226; &nbsp; if set to <samp>w</samp> or <samp>w-</samp>, 
	 * only <samp>rw</samp>, <samp>w</samp> and <samp>w-</samp> are allowed.
	 * </p>
	 * @throws \Feralygon\Kit\Managers\Properties\Objects\Property\Exceptions\InvalidMode
	 * @return $this <p>This instance, for chaining purposes.</p>
	 */
	final public function setMode(string $mode) : Property
	{
		//map
		$map = [];
		switch ($this->manager->getMode()) {
			case 'r':
				$map = ['r' => 'r', 'r+' => 'r', 'rw' => 'r'];
				break;
			case 'r+':
				$map = ['r' => 'r', 'r+' => 'r+', 'rw' => 'r+'];
				break;
			case 'rw':
				$map = array_combine(Manager::MODES, Manager::MODES);
				break;
			case 'w':
				$map = ['rw' => 'w', 'w' => 'w', 'w-' => 'w-'];
				break;
			case 'w-':
				$map = ['rw' => 'w-', 'w' => 'w-', 'w-' => 'w-'];
				break;
		}
		
		//set
		if (!isset($map[$mode])) {
			throw new Exceptions\InvalidMode(['property' => $this, 'mode' => $mode, 'modes' => array_keys($map)]);
		}
		$this->mode = $map[$mode];
		
		//return
		return $this;
	}
	
	/**
	 * Get value.
	 * 
	 * This method may only be called after initialization.
	 * 
	 * @since 1.0.0
	 * @return mixed <p>The value.</p>
	 */
	final public function getValue()
	{
		UCall::guard(
			$this->initialized,
			"This method may only be called after initialization."
		);
		return isset($this->getter) ? ($this->getter)() : $this->value;
	}
	
	/**
	 * Set value.
	 * 
	 * This method may only be called during or after the manager initialization.
	 * 
	 * @since 1.0.0
	 * @param mixed $value <p>The value to set.</p>
	 * @throws \Feralygon\Kit\Managers\Properties\Objects\Property\Exceptions\InvalidValue
	 * @return $this <p>This instance, for chaining purposes.</p>
	 */
	final public function setValue($value) : Property
	{
		//guard
		UCall::guard(
			$this->manager->isInitialized() || $this->manager->isInitializing(),
			"This method may only be called during or after the manager initialization."
		);
		
		//set
		if (isset($this->evaluator) && !($this->evaluator)($value)) {
			throw new Exceptions\InvalidValue(['property' => $this, 'value' => $value]);
		} elseif (isset($this->setter)) {
			($this->setter)($value);
		} else {
			$this->value = $value;
		}
		
		//initialized
		$this->initialized = true;
		
		//return
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
		return isset($this->default_getter);
	}
	
	/**
	 * Get default value.
	 * 
	 * @since 1.0.0
	 * @throws \Feralygon\Kit\Managers\Properties\Objects\Property\Exceptions\NoDefaultValueSet
	 * @throws \Feralygon\Kit\Managers\Properties\Objects\Property\Exceptions\InvalidDefaultValue
	 * @return mixed <p>The default value.</p>
	 */
	final public function getDefaultValue()
	{
		//check
		if (!isset($this->default_getter)) {
			throw new Exceptions\NoDefaultValueSet(['property' => $this]);
		}
		
		//value
		$value = ($this->default_getter)();
		if (isset($this->evaluator) && !($this->evaluator)($value)) {
			throw new Exceptions\InvalidDefaultValue(['property' => $this, 'value' => $value]);
		}
		return $value;
	}
	
	/**
	 * Set default value.
	 * 
	 * This method may only be called before initialization.
	 * 
	 * @since 1.0.0
	 * @param mixed $value <p>The default value to set.</p>
	 * @return $this <p>This instance, for chaining purposes.</p>
	 */
	final public function setDefaultValue($value) : Property
	{
		//guard
		UCall::guard(
			!$this->initialized,
			"This method may only be called before initialization."
		);
		
		//set
		$this->default_getter = function () use ($value) {
			return $value;
		};
		
		//return
		return $this;
	}
	
	/**
	 * Set default getter function.
	 * 
	 * By setting a default getter function, the default value will always be retrieved using that function.<br>
	 * It is only called after all properties have been initialized through the manager.<br>
	 * <br>
	 * This method may only be called before initialization.
	 * 
	 * @since 1.0.0
	 * @param callable $getter <p>The default getter function to set.<br>
	 * It is expected to be compatible with the following signature:<br><br>
	 * <code>function ()</code><br>
	 * <br>
	 * Return: <code><b>mixed</b></code><br>
	 * The default value.
	 * </p>
	 * @return $this <p>This instance, for chaining purposes.</p>
	 */
	final public function setDefaultGetter(callable $getter) : Property
	{
		UCall::guard(
			!$this->initialized,
			"This method may only be called before initialization."
		);
		UCall::assert('default_getter', $getter, function () {}, true);
		$this->default_getter = \Closure::fromCallable($getter);
		return $this;
	}
	
	/**
	 * Reset value.
	 * 
	 * This method may only be called after initialization.
	 * 
	 * @since 1.0.0
	 * @return $this <p>This instance, for chaining purposes.</p>
	 */
	final public function resetValue() : Property
	{
		UCall::guard(
			$this->initialized,
			"This method may only be called after initialization."
		);
		$this->setValue($this->getDefaultValue());
		return $this;
	}
	
	/**
	 * Set evaluator function.
	 * 
	 * This method may only be called before initialization.
	 * 
	 * @since 1.0.0
	 * @param callable $evaluator <p>The evaluator function to set.<br>
	 * It is expected to be compatible with the following signature:<br><br>
	 * <code>function (&$value) : bool</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>mixed $value</b> [reference]</code> : 
	 * The value to evaluate (validate and sanitize).<br>
	 * <br>
	 * Return: <code><b>bool</b></code><br>
	 * Boolean <code>true</code> if the given value is successfully evaluated.
	 * </p>
	 * @return $this <p>This instance, for chaining purposes.</p>
	 */
	final public function setEvaluator(callable $evaluator) : Property
	{
		UCall::guard(
			!$this->initialized,
			"This method may only be called before initialization."
		);
		UCall::assert('evaluator', $evaluator, function (&$value) : bool {}, true);
		$this->evaluator = \Closure::fromCallable($evaluator);
		return $this;
	}
	
	/**
	 * Set to only allow a value evaluated as a boolean.
	 * 
	 * Only the following types and formats can be evaluated into a boolean:<br>
	 * &nbsp; &#8226; &nbsp; a boolean, as: <code>false</code> for boolean <code>false</code>, 
	 * and <code>true</code> for boolean <code>true</code>;<br>
	 * &nbsp; &#8226; &nbsp; an integer, as: <code>0</code> for boolean <code>false</code>, 
	 * and <code>1</code> for boolean <code>true</code>;<br>
	 * &nbsp; &#8226; &nbsp; a float, as: <code>0.0</code> for boolean <code>false</code>, 
	 * and <code>1.0</code> for boolean <code>true</code>;<br>
	 * &nbsp; &#8226; &nbsp; a string, as: <code>"0"</code>, <code>"f"</code>, <code>"false"</code>, 
	 * <code>"off"</code> or <code>"no"</code> for boolean <code>false</code>, 
	 * and <code>"1"</code>, <code>"t"</code>, <code>"true"</code>, 
	 * <code>"on"</code> or <code>"yes"</code> for boolean <code>true</code>.<br>
	 * <br>
	 * This method may only be called before initialization.
	 * 
	 * @since 1.0.0
	 * @param bool $nullable [default = false] <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsBoolean(bool $nullable = false) : Property
	{
		$this->setEvaluator(function (&$value) use ($nullable) : bool {
			return UType::evaluateBoolean($value, $nullable);
		});
		return $this;
	}
	
	/**
	 * Set to only allow a value strictly evaluated as a boolean.
	 * 
	 * This method may only be called before initialization.
	 * 
	 * @since 1.0.0
	 * @param bool $nullable [default = false] <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsStrictBoolean(bool $nullable = false) : Property
	{
		$this->setEvaluator(function (&$value) use ($nullable) : bool {
			return isset($value) ? is_bool($value) : $nullable;
		});
		return $this;
	}
	
	/**
	 * Set to only allow a value evaluated as a number.
	 * 
	 * Only the following types and formats can be evaluated into a number:<br>
	 * &nbsp; &#8226; &nbsp; an integer, such as: <code>123000</code> for <code>123000</code>;<br>
	 * &nbsp; &#8226; &nbsp; a float, such as: <code>123000.45</code> for <code>123000.45</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string, 
	 * such as: <code>"123000.45"</code> or <code>"123000,45"</code> for <code>123000.45</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string in exponential notation, 
	 * such as: <code>"123e3"</code> or <code>"123E3"</code> for <code>123000</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string in octal notation, 
	 * such as: <code>"0360170"</code> for <code>123000</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string in hexadecimal notation, 
	 * such as: <code>"0x1e078"</code> or <code>"0x1E078"</code> for <code>123000</code>;<br>
	 * &nbsp; &#8226; &nbsp; a human-readable numeric string, 
	 * such as: <code>"123k"</code> or <code>"123 thousand"</code> for <code>123000</code>;<br>
	 * &nbsp; &#8226; &nbsp; a human-readable numeric string in bytes, 
	 * such as: <code>"123kB"</code> or <code>"123 kilobytes"</code> for <code>123000</code>.<br>
	 * <br>
	 * This method may only be called before initialization.
	 * 
	 * @since 1.0.0
	 * @param bool $nullable [default = false] <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsNumber(bool $nullable = false) : Property
	{
		$this->setEvaluator(function (&$value) use ($nullable) : bool {
			return UType::evaluateNumber($value, $nullable);
		});
		return $this;
	}
	
	/**
	 * Set to only allow a value strictly evaluated as a number.
	 * 
	 * This method may only be called before initialization.
	 * 
	 * @since 1.0.0
	 * @param bool $nullable [default = false] <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsStrictNumber(bool $nullable = false) : Property
	{
		$this->setEvaluator(function (&$value) use ($nullable) : bool {
			return isset($value) ? is_int($value) || is_float($value) : $nullable;
		});
		return $this;
	}
	
	/**
	 * Set to only allow a value evaluated as an integer.
	 * 
	 * Only the following types and formats can be evaluated into an integer:<br>
	 * &nbsp; &#8226; &nbsp; an integer, such as: <code>123000</code> for <code>123000</code>;<br>
	 * &nbsp; &#8226; &nbsp; a whole float, such as: <code>123000.0</code> for <code>123000</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string, such as: <code>"123000"</code> for <code>123000</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string in exponential notation, 
	 * such as: <code>"123e3"</code> or <code>"123E3"</code> for <code>123000</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string in octal notation, 
	 * such as: <code>"0360170"</code> for <code>123000</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string in hexadecimal notation, 
	 * such as: <code>"0x1e078"</code> or <code>"0x1E078"</code> for <code>123000</code>;<br>
	 * &nbsp; &#8226; &nbsp; a human-readable numeric string, 
	 * such as: <code>"123k"</code> or <code>"123 thousand"</code> for <code>123000</code>;<br>
	 * &nbsp; &#8226; &nbsp; a human-readable numeric string in bytes, 
	 * such as: <code>"123kB"</code> or <code>"123 kilobytes"</code> for <code>123000</code>.<br>
	 * <br>
	 * This method may only be called before initialization.
	 * 
	 * @since 1.0.0
	 * @param bool $nullable [default = false] <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsInteger(bool $nullable = false) : Property
	{
		$this->setEvaluator(function (&$value) use ($nullable) : bool {
			return UType::evaluateInteger($value, $nullable);
		});
		return $this;
	}
	
	/**
	 * Set to only allow a value strictly evaluated as an integer.
	 * 
	 * This method may only be called before initialization.
	 * 
	 * @since 1.0.0
	 * @param bool $nullable [default = false] <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsStrictInteger(bool $nullable = false) : Property
	{
		$this->setEvaluator(function (&$value) use ($nullable) : bool {
			return isset($value) ? is_int($value) : $nullable;
		});
		return $this;
	}
	
	/**
	 * Set to only allow a value evaluated as a float.
	 * 
	 * Only the following types and formats can be evaluated into a float:<br>
	 * &nbsp; &#8226; &nbsp; an integer, such as: <code>123000</code> for <code>123000.0</code>;<br>
	 * &nbsp; &#8226; &nbsp; a float, such as: <code>123000.45</code> for <code>123000.45</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string, 
	 * such as: <code>"123000.45"</code> or <code>"123000,45"</code> for <code>123000.45</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string in exponential notation, 
	 * such as: <code>"123e3"</code> or <code>"123E3"</code> for <code>123000.0</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string in octal notation, 
	 * such as: <code>"0360170"</code> for <code>123000.0</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string in hexadecimal notation, 
	 * such as: <code>"0x1e078"</code> or <code>"0x1E078"</code> for <code>123000.0</code>;<br>
	 * &nbsp; &#8226; &nbsp; a human-readable numeric string, 
	 * such as: <code>"123.45k"</code> or <code>"123.45 thousand"</code> for <code>123450.0</code>;<br>
	 * &nbsp; &#8226; &nbsp; a human-readable numeric string in bytes, 
	 * such as: <code>"123.45kB"</code> or <code>"123.45 kilobytes"</code> for <code>123450.0</code>.<br>
	 * <br>
	 * This method may only be called before initialization.
	 * 
	 * @since 1.0.0
	 * @param bool $nullable [default = false] <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsFloat(bool $nullable = false) : Property
	{
		$this->setEvaluator(function (&$value) use ($nullable) : bool {
			return UType::evaluateFloat($value, $nullable);
		});
		return $this;
	}
	
	/**
	 * Set to only allow a value strictly evaluated as a float.
	 * 
	 * This method may only be called before initialization.
	 * 
	 * @since 1.0.0
	 * @param bool $nullable [default = false] <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsStrictFloat(bool $nullable = false) : Property
	{
		$this->setEvaluator(function (&$value) use ($nullable) : bool {
			return isset($value) ? is_float($value) : $nullable;
		});
		return $this;
	}
	
	/**
	 * Set to only allow a value evaluated as a string.
	 * 
	 * Only a string, integer or float can be evaluated into a string.<br>
	 * <br>
	 * This method may only be called before initialization.
	 * 
	 * @since 1.0.0
	 * @param bool $non_empty [default = false] <p>Do not allow an empty string value.</p>
	 * @param bool $nullable [default = false] <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsString(bool $non_empty = false, bool $nullable = false) : Property
	{
		$this->setEvaluator(function (&$value) use ($non_empty, $nullable) : bool {
			return UType::evaluateString($value, $non_empty, $nullable);
		});
		return $this;
	}
	
	/**
	 * Set to only allow a value strictly evaluated as a string.
	 * 
	 * This method may only be called before initialization.
	 * 
	 * @since 1.0.0
	 * @param bool $non_empty [default = false] <p>Do not allow an empty string value.</p>
	 * @param bool $nullable [default = false] <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsStrictString(bool $non_empty = false, bool $nullable = false) : Property
	{
		$this->setEvaluator(function (&$value) use ($non_empty, $nullable) : bool {
			return isset($value) ? is_string($value) && (!$non_empty || $value !== '') : $nullable;
		});
		return $this;
	}
	
	/**
	 * Set to only allow a value evaluated as a class.
	 * 
	 * Only a class string or object can be evaluated into a class.<br>
	 * <br>
	 * This method may only be called before initialization.
	 * 
	 * @since 1.0.0
	 * @param object|string|null $base_object_class [default = null] <p>The base object or class 
	 * which a value must be or extend from.</p>
	 * @param bool $nullable [default = false] <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsClass($base_object_class = null, bool $nullable = false) : Property
	{
		$this->setEvaluator(function (&$value) use ($base_object_class, $nullable) : bool {
			return UType::evaluateClass($value, $base_object_class, $nullable);
		});
		return $this;
	}
	
	/**
	 * Set to only allow a value strictly evaluated as a class.
	 * 
	 * This method may only be called before initialization.
	 * 
	 * @since 1.0.0
	 * @param object|string|null $base_object_class [default = null] <p>The base object or class 
	 * which a value must be or extend from.</p>
	 * @param bool $nullable [default = false] <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsStrictClass($base_object_class = null, bool $nullable = false) : Property
	{
		$this->setEvaluator(function (&$value) use ($base_object_class, $nullable) : bool {
			return isset($value) ? is_string($value) && UType::evaluateClass($value, $base_object_class) : $nullable;
		});
		return $this;
	}
	
	/**
	 * Set to only allow a value evaluated as an object.
	 * 
	 * Only a class string or object can be evaluated into an object.<br>
	 * <br>
	 * This method may only be called before initialization.
	 * 
	 * @since 1.0.0
	 * @param object|string|null $base_object_class [default = null] <p>The base object or class 
	 * which a value must be or extend from.</p>
	 * @param array $arguments [default = []] <p>The class constructor arguments to instantiate with.</p>
	 * @param bool $nullable [default = false] <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsObject(
		$base_object_class = null, array $arguments = [], bool $nullable = false
	) : Property
	{
		$this->setEvaluator(function (&$value) use ($base_object_class, $arguments, $nullable) : bool {
			return UType::evaluateObject($value, $base_object_class, $arguments, $nullable);
		});
		return $this;
	}
	
	/**
	 * Set to only allow a value strictly evaluated as an object.
	 * 
	 * This method may only be called before initialization.
	 * 
	 * @since 1.0.0
	 * @param object|string|null $base_object_class [default = null] <p>The base object or class 
	 * which a value must be or extend from.</p>
	 * @param bool $nullable [default = false] <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsStrictObject($base_object_class = null, bool $nullable = false) : Property
	{
		$this->setEvaluator(function (&$value) use ($base_object_class, $nullable) : bool {
			return isset($value) ? is_object($value) && UType::evaluateObject($value, $base_object_class) : $nullable;
		});
		return $this;
	}
	
	/**
	 * Set to only allow a value evaluated as a class or object.
	 * 
	 * Only a class string or object can be evaluated into an object or class.<br>
	 * <br>
	 * This method may only be called before initialization.
	 * 
	 * @since 1.0.0
	 * @param object|string|null $base_object_class [default = null] <p>The base object or class 
	 * which a value must be or extend from.</p>
	 * @param bool $nullable [default = false] <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsObjectClass($base_object_class = null, bool $nullable = false) : Property
	{
		$this->setEvaluator(function (&$value) use ($base_object_class, $nullable) : bool {
			return UType::evaluateObjectClass($value, $base_object_class, $nullable);
		});
		return $this;
	}
	
	/**
	 * Set to only allow a value evaluated as a callable.
	 * 
	 * This method may only be called before initialization.
	 * 
	 * @since 1.0.0
	 * @param callable|null $template [default = null] <p>The template callable declaration 
	 * to validate the compatibility against.</p>
	 * @param bool $nullable [default = false] <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @param bool $assertive [default = false] <p>Evaluate in an assertive manner, in other words, 
	 * perform the heavier validations, such as the template compatibility one, only when in a debug environment.</p>
	 * @return $this <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsCallable(
		?callable $template = null, bool $nullable = false, bool $assertive = false
	) : Property
	{
		$this->setEvaluator(function (&$value) use ($template, $nullable, $assertive) : bool {
			return UCall::evaluate($value, $template, $nullable, $assertive);
		});
		return $this;
	}
	
	/**
	 * Set to only allow a value evaluated as a closure.
	 * 
	 * This method may only be called before initialization.
	 * 
	 * @since 1.0.0
	 * @param callable|null $template [default = null] <p>The template callable declaration 
	 * to validate the compatibility against.</p>
	 * @param bool $nullable [default = false] <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @param bool $assertive [default = false] <p>Evaluate in an assertive manner, in other words, 
	 * perform the heavier validations, such as the template compatibility one, only when in a debug environment.</p>
	 * @return $this <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsClosure(
		?callable $template = null, bool $nullable = false, bool $assertive = false
	) : Property
	{
		$this->setEvaluator(function (&$value) use ($template, $nullable, $assertive) : bool {
			return isset($value)
				? is_object($value) && UType::isA($value, \Closure::class) && 
					UCall::evaluate($value, $template, false, $assertive)
				: $nullable;
		});
		return $this;
	}
	
	/**
	 * Set to only allow a value evaluated as an array.
	 * 
	 * Only the following types and formats can be evaluated into an array:<br>
	 * &nbsp; &#8226; &nbsp; an array;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Feralygon\Kit\Interfaces\Arrayable</code> interface.<br>
	 * <br>
	 * This method may only be called before initialization.
	 * 
	 * @since 1.0.0
	 * @param callable|null $evaluator [default = null] <p>The evaluator function to use for each element 
	 * in the resulting array value.<br>
	 * It is expected to be compatible with the following signature:<br><br>
	 * <code>function (&$key, &$value) : bool</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>int|string $key</b> [reference]</code> : 
	 * The array element key to evaluate (validate and sanitize).<br>
	 * &nbsp; &#8226; &nbsp; <code><b>mixed $value</b> [reference]</code> : 
	 * The array element value to evaluate (validate and sanitize).<br>
	 * <br>
	 * Return: <code><b>bool</b></code><br>
	 * Boolean <code>true</code> if the given array element is successfully evaluated.
	 * </p>
	 * @param bool $non_associative [default = false] <p>Do not allow an associative array value.</p>
	 * @param bool $non_empty [default = false] <p>Do not allow an empty array value.</p>
	 * @param bool $nullable [default = false] <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsArray(
		?callable $evaluator = null, bool $non_associative = false, bool $non_empty = false, bool $nullable = false
	) : Property
	{
		$this->setEvaluator(function (&$value) use ($evaluator, $non_associative, $non_empty, $nullable) : bool {
			return UData::evaluate($value, $evaluator, $non_associative, $non_empty, $nullable);
		});
		return $this;
	}
	
	/**
	 * Set to only allow a value strictly evaluated as an array.
	 * 
	 * This method may only be called before initialization.
	 * 
	 * @since 1.0.0
	 * @param callable|null $evaluator [default = null] <p>The evaluator function to use for each element 
	 * in the resulting array value.<br>
	 * It is expected to be compatible with the following signature:<br><br>
	 * <code>function (&$key, &$value) : bool</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>int|string $key</b> [reference]</code> : 
	 * The array element key to evaluate (validate and sanitize).<br>
	 * &nbsp; &#8226; &nbsp; <code><b>mixed $value</b> [reference]</code> : 
	 * The array element value to evaluate (validate and sanitize).<br>
	 * <br>
	 * Return: <code><b>bool</b></code><br>
	 * Boolean <code>true</code> if the given array element is successfully evaluated.
	 * </p>
	 * @param bool $non_associative [default = false] <p>Do not allow an associative array value.</p>
	 * @param bool $non_empty [default = false] <p>Do not allow an empty array value.</p>
	 * @param bool $nullable [default = false] <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsStrictArray(
		?callable $evaluator = null, bool $non_associative = false, bool $non_empty = false, bool $nullable = false
	) : Property
	{
		$this->setEvaluator(function (&$value) use ($evaluator, $non_associative, $non_empty, $nullable) : bool {
			return isset($value)
				? is_array($value) && UData::evaluate($value, $evaluator, $non_associative, $non_empty)
				: $nullable;
		});
		return $this;
	}
	
	/**
	 * Set to only allow a value evaluated as an enumeration value.
	 * 
	 * Only an enumeration element given as an integer, float or string can be evaluated into an enumeration value.<br>
	 * <br>
	 * This method may only be called before initialization.
	 * 
	 * @since 1.0.0
	 * @param string $enumeration <p>The enumeration class to use.</p>
	 * @param bool $nullable [default = false] <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsEnumerationValue(string $enumeration, bool $nullable = false) : Property
	{
		$enumeration = UType::coerceClass($enumeration, Enumeration::class);
		$this->setEvaluator(function (&$value) use ($enumeration, $nullable) : bool {
			return $enumeration::evaluateValue($value, $nullable);
		});
		return $this;
	}
	
	/**
	 * Set to only allow a value strictly evaluated as an enumeration value.
	 * 
	 * This method may only be called before initialization.
	 * 
	 * @since 1.0.0
	 * @param string $enumeration <p>The enumeration class to use.</p>
	 * @param bool $nullable [default = false] <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsStrictEnumerationValue(string $enumeration, bool $nullable = false) : Property
	{
		$enumeration = UType::coerceClass($enumeration, Enumeration::class);
		$this->setEvaluator(function (&$value) use ($enumeration, $nullable) : bool {
			if ((is_int($value) || is_float($value) || is_string($value)) && $enumeration::hasValue($value)) {
				$value = $enumeration::getValue($value);
				return true;
			}
			return false;
		});
		return $this;
	}
	
	/**
	 * Set to only allow a value evaluated as an enumeration name.
	 * 
	 * Only an enumeration element given as an integer, float or string can be evaluated into an enumeration name.<br>
	 * <br>
	 * This method may only be called before initialization.
	 * 
	 * @since 1.0.0
	 * @param string $enumeration <p>The enumeration class to use.</p>
	 * @param bool $nullable [default = false] <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsEnumerationName(string $enumeration, bool $nullable = false) : Property
	{
		$enumeration = UType::coerceClass($enumeration, Enumeration::class);
		$this->setEvaluator(function (&$value) use ($enumeration, $nullable) : bool {
			return $enumeration::evaluateName($value, $nullable);
		});
		return $this;
	}
	
	/**
	 * Set to only allow a value strictly evaluated as an enumeration name.
	 * 
	 * This method may only be called before initialization.
	 * 
	 * @since 1.0.0
	 * @param string $enumeration <p>The enumeration class to use.</p>
	 * @param bool $nullable [default = false] <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsStrictEnumerationName(string $enumeration, bool $nullable = false) : Property
	{
		$enumeration = UType::coerceClass($enumeration, Enumeration::class);
		$this->setEvaluator(function (&$value) use ($enumeration, $nullable) : bool {
			return is_string($value) && $enumeration::hasName($value);
		});
		return $this;
	}
	
	/**
	 * Set accessor functions.
	 * 
	 * By setting a getter and a setter function, 
	 * the value will always be retrieved and modified using those functions.<br>
	 * <br>
	 * This method may only be called before initialization.
	 * 
	 * @since 1.0.0
	 * @param callable $getter <p>The getter function to set.<br>
	 * It is expected to be compatible with the following signature:<br><br>
	 * <code>function ()</code><br>
	 * <br>
	 * Return: <code><b>mixed</b></code><br>
	 * The value.
	 * </p>
	 * @param callable $setter <p>The setter function to set.<br>
	 * It is expected to be compatible with the following signature:<br><br>
	 * <code>function ($value) : void</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>mixed $value</b></code> : The value to set.<br>
	 * <br>
	 * Return: <code><b>void</b></code>
	 * </p>
	 * @return $this <p>This instance, for chaining purposes.</p>
	 */
	final public function setAccessors(callable $getter, callable $setter) : Property
	{
		//guard
		UCall::guard(
			!$this->initialized,
			"This method may only be called before initialization."
		);
		
		//set
		UCall::assert('getter', $getter, function () {}, true);
		UCall::assert('setter', $setter, function ($value) : void {}, true);
		$this->getter = \Closure::fromCallable($getter);
		$this->setter = \Closure::fromCallable($setter);
		
		//default
		if (!$this->hasDefaultValue()) {
			$this->setDefaultValue(($this->getter)());
		}
		
		//return
		return $this;
	}
	
	/**
	 * Bind to an existing property from the manager owner object.
	 * 
	 * By binding to an existing property, getter and setter functions are automatically set for that property, 
	 * using the given class scope, so it can be accessed and modified directly from outside.<br>
	 * All restrictions set in this property still apply however, therefore attempts at accessing and modifying it 
	 * may still fail accordingly.<br>
	 * <br>
	 * This method may only be called before initialization.
	 * 
	 * @since 1.0.0
	 * @param string|null $class [default = null] <p>The class scope to use.<br>
	 * If not set, the manager owner object of this instance is used.</p>
	 * @param string|null $name [default = null] <p>The property name to bind to.<br>
	 * If not set, the name set in this instance is used.</p>
	 * @return $this <p>This instance, for chaining purposes.</p>
	 */
	final public function bind(?string $class = null, ?string $name = null) : Property
	{
		//guard
		UCall::guard(
			!$this->initialized,
			"This method may only be called before initialization."
		);
		
		//initialize
		$owner = $this->manager->getOwner();
		if (!isset($class)) {
			$class = get_class($owner);
		}
		if (!isset($name)) {
			$name = $this->name;
		}
		
		//bind
		$this->setAccessors(
			\Closure::bind(function () use ($name) {
				return $this->$name;
			}, $owner, $class),
			\Closure::bind(function ($value) use ($name) : void {
				$this->$name = $value;
			}, $owner, $class)
		);
		
		//return
		return $this;
	}
}
