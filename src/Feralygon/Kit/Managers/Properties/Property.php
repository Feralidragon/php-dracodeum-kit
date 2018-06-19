<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Managers\Properties;

use Feralygon\Kit\Managers\Properties as Manager;
use Feralygon\Kit\Managers\Properties\Property\Exceptions;
use Feralygon\Kit\{
	Enumeration,
	Structure
};
use Feralygon\Kit\Utilities\{
	Byte as UByte,
	Call as UCall,
	Data as UData,
	Text as UText,
	Type as UType
};

/**
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
	
	/** @var \Closure[] */
	private $evaluators = [];
	
	/** @var \Closure|null */
	private $getter = null;
	
	/** @var \Closure|null */
	private $setter = null;
	
	
	
	//Final public magic methods
	/**
	 * Instantiate class.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Managers\Properties $manager
	 * <p>The manager instance.</p>
	 * @param string $name
	 * <p>The name.</p>
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
	 * @return \Feralygon\Kit\Managers\Properties
	 * <p>The manager instance.</p>
	 */
	final public function getManager() : Manager
	{
		return $this->manager;
	}
	
	/**
	 * Get name.
	 * 
	 * @since 1.0.0
	 * @return string
	 * <p>The name.</p>
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
	 * @return bool
	 * <p>Boolean <code>true</code> if is initialized.</p>
	 */
	final public function isInitialized() : bool
	{
		return $this->initialized;
	}
	
	/**
	 * Check if is required.
	 * 
	 * @since 1.0.0
	 * @return bool
	 * <p>Boolean <code>true</code> if is required.</p>
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
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function initialize() : Property
	{
		UCall::guard(!$this->initialized, [
			'error_message' => "Property {{property.getName()}} already initialized " . 
				"in manager with owner {{property.getManager().getOwner()}}.",
			'parameters' => ['property' => $this]
		]);
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
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsRequired() : Property
	{
		//guard
		UCall::guard(!$this->initialized, [
			'hint_message' => "This method may only be called before initialization, " . 
				"in property {{property.getName()}} in manager with owner {{property.getManager().getOwner()}}.",
			'parameters' => ['property' => $this]
		]);
		UCall::guard(!$this->manager->isLazy(), [
			'hint_message' => "In order to set property {{property.getName()}} as required " . 
				"in manager with owner {{property.getManager().getOwner()}}, " . 
				"please use the manager {{method}} method instead, as lazy-loading is enabled.",
			'parameters' => ['property' => $this, 'method' => 'addRequiredPropertyNames']
		]);
		UCall::guard(!$this->manager->isInitialized(), [
			'hint_message' => "This method may only be called before the manager initialization, " . 
				"in property {{property.getName()}} in manager with owner {{property.getManager().getOwner()}}.",
			'parameters' => ['property' => $this]
		]);
		UCall::guard($this->getMode() !== 'r', [
			'hint_message' => "Property {{property.getName()}} is strictly read-only " . 
				"in manager with owner {{property.getManager().getOwner()}} and thus cannot be set as required.",
			'parameters' => ['property' => $this]
		]);
		
		//set
		$this->required = true;
		
		//return
		return $this;
	}
	
	/**
	 * Get mode.
	 * 
	 * @since 1.0.0
	 * @return string
	 * <p>The mode.</p>
	 */
	final public function getMode() : string
	{
		return $this->mode ?? $this->manager->getMode();
	}
	
	/**
	 * Set mode.
	 * 
	 * @since 1.0.0
	 * @param string $mode
	 * <p>The access mode to set, which must be one the following:<br>
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
	 * only <samp>rw</samp>, <samp>w</samp> and <samp>w-</samp> are allowed.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
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
		UCall::guardParameter('mode', $mode, isset($map[$mode]), function () use ($map) {
			return [
				'hint_message' => "Only the following mode is allowed for property {{property.getName()}} " . 
					"in manager with owner {{property.getManager().getOwner()}}: {{modes}}.",
				'hint_message_plural' => "Only the following modes are allowed for property {{property.getName()}} " . 
					"in manager with owner {{property.getManager().getOwner()}}: {{modes}}.",
				'hint_message_number' => count($map),
				'parameters' => ['property' => $this, 'modes' => array_keys($map)],
				'string_options' => ['non_assoc_mode' => UText::STRING_NONASSOC_MODE_COMMA_LIST_AND]
			];
		});
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
	 * @return mixed
	 * <p>The value.</p>
	 */
	final public function getValue()
	{
		UCall::guard($this->initialized, [
			'hint_message' => "This method may only be called after initialization, " . 
				"in property {{property.getName()}} in manager with owner {{property.getManager().getOwner()}}.",
			'parameters' => ['property' => $this]
		]);
		return isset($this->getter) ? ($this->getter)() : $this->value;
	}
	
	/**
	 * Set value.
	 * 
	 * This method may only be called during or after the manager initialization.
	 * 
	 * @since 1.0.0
	 * @param mixed $value
	 * <p>The value to set.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Feralygon\Kit\Managers\Properties\Property\Exceptions\InvalidValue
	 * @return $this|bool
	 * <p>This instance, for chaining purposes.<br>
	 * If <var>$no_throw</var> is set to <code>true</code>, 
	 * then boolean <code>true</code> is returned if the value was successfully set, 
	 * or boolean <code>false</code> if otherwise.</p>
	 */
	final public function setValue($value, bool $no_throw = false)
	{
		//guard
		UCall::guard($this->manager->isInitialized() || $this->manager->isInitializing(), [
			'hint_message' => "This method may only be called during or after the manager initialization, " . 
				"in property {{property.getName()}} in manager with owner {{property.getManager().getOwner()}}.",
			'parameters' => ['property' => $this]
		]);
		
		//evaluate
		$v = $value;
		foreach ($this->evaluators as $evaluator) {
			if (!$evaluator($v)) {
				if ($no_throw) {
					return false;
				}
				throw new Exceptions\InvalidValue(['property' => $this, 'value' => $value]);
			}
		}
		$value = $v;
		unset($v);
		
		//set
		if (isset($this->setter)) {
			($this->setter)($value);
		} else {
			$this->value = $value;
		}
		
		//initialized
		$this->initialized = true;
		
		//return
		return $no_throw ? true : $this;
	}
	
	/**
	 * Check if has default value.
	 * 
	 * @since 1.0.0
	 * @return bool
	 * <p>Boolean <code>true</code> if has default value.</p>
	 */
	final public function hasDefaultValue() : bool
	{
		return isset($this->default_getter);
	}
	
	/**
	 * Get default value.
	 * 
	 * @since 1.0.0
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Feralygon\Kit\Managers\Properties\Property\Exceptions\DefaultValueNotSet
	 * @return mixed
	 * <p>The default value.<br>
	 * If <var>$no_throw</var> is set to <code>true</code>, 
	 * then <code>null</code> may also be returned if none is set.</p>
	 */
	final public function getDefaultValue(bool $no_throw = false)
	{
		//check
		if (!isset($this->default_getter)) {
			if ($no_throw) {
				return null;
			}
			throw new Exceptions\DefaultValueNotSet(['property' => $this]);
		}
		
		//value
		$value = ($this->default_getter)();
		
		//evaluate
		$v = $value;
		foreach ($this->evaluators as $evaluator) {
			UCall::guardInternal($evaluator($v), [
				'error_message' => "Invalid default value {{value}} for property {{property.getName()}} " . 
					"in manager with owner {{property.getManager().getOwner()}}.",
				'parameters' => ['property' => $this, 'value' => $value]
			]);
		}
		$value = $v;
		unset($v);
		
		//return
		return $value;
	}
	
	/**
	 * Set default value.
	 * 
	 * This method may only be called before initialization.
	 * 
	 * @since 1.0.0
	 * @param mixed $value
	 * <p>The default value to set.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setDefaultValue($value) : Property
	{
		//guard
		UCall::guard(!$this->initialized, [
			'hint_message' => "This method may only be called before initialization, " . 
				"in property {{property.getName()}} in manager with owner {{property.getManager().getOwner()}}.",
			'parameters' => ['property' => $this]
		]);
		
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
	 * By setting a default getter function, the default value will always be got using that function.<br>
	 * It is only called after all properties have been initialized through the manager.<br>
	 * <br>
	 * This method may only be called before initialization.
	 * 
	 * @since 1.0.0
	 * @param callable $getter
	 * <p>The default getter function to set.<br>
	 * It is expected to be compatible with the following signature:<br><br>
	 * <code>function ()</code><br>
	 * <br>
	 * Return: <code><b>mixed</b></code><br>
	 * The default value.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setDefaultGetter(callable $getter) : Property
	{
		UCall::guard(!$this->initialized, [
			'hint_message' => "This method may only be called before initialization, " . 
				"in property {{property.getName()}} in manager with owner {{property.getManager().getOwner()}}.",
			'parameters' => ['property' => $this]
		]);
		UCall::assert('default_getter', $getter, function () {});
		$this->default_getter = \Closure::fromCallable($getter);
		return $this;
	}
	
	/**
	 * Reset value.
	 * 
	 * This method may only be called after initialization.
	 * 
	 * @since 1.0.0
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Feralygon\Kit\Managers\Properties\Property\Exceptions\DefaultValueNotSet
	 * @throws \Feralygon\Kit\Managers\Properties\Property\Exceptions\InvalidValue
	 * @return $this|bool
	 * <p>This instance, for chaining purposes.<br>
	 * If <var>$no_throw</var> is set to <code>true</code>, 
	 * then boolean <code>true</code> is returned if the value was successfully reset, 
	 * or boolean <code>false</code> if otherwise.</p>
	 */
	final public function resetValue(bool $no_throw = false)
	{
		//guard
		UCall::guard($this->initialized, [
			'hint_message' => "This method may only be called after initialization, " . 
				"in property {{property.getName()}} in manager with owner {{property.getManager().getOwner()}}.",
			'parameters' => ['property' => $this]
		]);
		
		//reset
		$reset = false;
		try {
			$reset = $this->setValue($this->getDefaultValue(), $no_throw);
		} catch (Exceptions\DefaultValueNotSet $exception) {
			if (!$no_throw) {
				throw $exception;
			}
		}
		
		//return
		return $no_throw ? $reset : $this;
	}
	
	/**
	 * Add evaluator function.
	 * 
	 * This method may only be called before initialization.
	 * 
	 * @since 1.0.0
	 * @param callable $evaluator
	 * <p>The evaluator function to add.<br>
	 * It is expected to be compatible with the following signature:<br><br>
	 * <code>function (&$value) : bool</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>mixed $value</b> [reference]</code><br>
	 * &nbsp; &nbsp; &nbsp; The value to evaluate (validate and sanitize).<br>
	 * <br>
	 * Return: <code><b>bool</b></code><br>
	 * Boolean <code>true</code> if the given value is successfully evaluated.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function addEvaluator(callable $evaluator) : Property
	{
		UCall::guard(!$this->initialized, [
			'hint_message' => "This method may only be called before initialization, " . 
				"in property {{property.getName()}} in manager with owner {{property.getManager().getOwner()}}.",
			'parameters' => ['property' => $this]
		]);
		UCall::assert('evaluator', $evaluator, function (&$value) : bool {});
		$this->evaluators[] = \Closure::fromCallable($evaluator);
		return $this;
	}
	
	/**
	 * Clear all evaluator functions.
	 * 
	 * This method may only be called before initialization.
	 * 
	 * @since 1.0.0
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function clearEvaluators() : Property
	{
		UCall::guard(!$this->initialized, [
			'hint_message' => "This method may only be called before initialization, " . 
				"in property {{property.getName()}} in manager with owner {{property.getManager().getOwner()}}.",
			'parameters' => ['property' => $this]
		]);
		$this->evaluators = [];
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
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsBoolean(bool $nullable = false) : Property
	{
		$this->clearEvaluators()->addEvaluator(
			function (&$value) use ($nullable) : bool {
				return UType::evaluateBoolean($value, $nullable);
			}
		);
		return $this;
	}
	
	/**
	 * Set to only allow a value strictly evaluated as a boolean.
	 * 
	 * This method may only be called before initialization.
	 * 
	 * @since 1.0.0
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsStrictBoolean(bool $nullable = false) : Property
	{
		$this->clearEvaluators()->addEvaluator(
			function (&$value) use ($nullable) : bool {
				return isset($value) ? is_bool($value) : $nullable;
			}
		);
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
	 * such as: <code>"123k"</code> or <code>"123 thousand"</code> for <code>123000</code>.<br>
	 * <br>
	 * This method may only be called before initialization.
	 * 
	 * @since 1.0.0
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsNumber(bool $nullable = false) : Property
	{
		$this->clearEvaluators()->addEvaluator(
			function (&$value) use ($nullable) : bool {
				return UType::evaluateNumber($value, $nullable);
			}
		);
		return $this;
	}
	
	/**
	 * Set to only allow a value strictly evaluated as a number.
	 * 
	 * This method may only be called before initialization.
	 * 
	 * @since 1.0.0
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsStrictNumber(bool $nullable = false) : Property
	{
		$this->clearEvaluators()->addEvaluator(
			function (&$value) use ($nullable) : bool {
				return isset($value) ? is_int($value) || is_float($value) : $nullable;
			}
		);
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
	 * such as: <code>"123k"</code> or <code>"123 thousand"</code> for <code>123000</code>.<br>
	 * <br>
	 * This method may only be called before initialization.
	 * 
	 * @since 1.0.0
	 * @param bool $unsigned [default = false]
	 * <p>Set as an unsigned integer.</p>
	 * @param int|null $bits [default = null]
	 * <p>The number of bits to use.<br>
	 * If set, then it must be greater than <code>0</code>.<br>
	 * <br>
	 * For signed integers, the maximum allowed number is <code>64</code>, 
	 * while for unsigned integers this number is <code>63</code>.<br>
	 * If not set, then the number of bits to use becomes system dependent.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsInteger(bool $unsigned = false, ?int $bits = null, bool $nullable = false) : Property
	{
		$this->clearEvaluators()->addEvaluator(
			function (&$value) use ($unsigned, $bits, $nullable) : bool {
				return UType::evaluateInteger($value, $unsigned, $bits, $nullable);
			}
		);
		return $this;
	}
	
	/**
	 * Set to only allow a value strictly evaluated as an integer.
	 * 
	 * This method may only be called before initialization.
	 * 
	 * @since 1.0.0
	 * @param bool $unsigned [default = false]
	 * <p>Set as an unsigned integer.</p>
	 * @param int|null $bits [default = null]
	 * <p>The number of bits to use.<br>
	 * If set, then it must be greater than <code>0</code>.<br>
	 * <br>
	 * For signed integers, the maximum allowed number is <code>64</code>, 
	 * while for unsigned integers this number is <code>63</code>.<br>
	 * If not set, then the number of bits to use becomes system dependent.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsStrictInteger(
		bool $unsigned = false, ?int $bits = null, bool $nullable = false
	) : Property
	{
		$this->clearEvaluators()->addEvaluator(
			function (&$value) use ($unsigned, $bits, $nullable) : bool {
				return isset($value)
					? (is_int($value) ? UType::evaluateInteger($value, $unsigned, $bits) : false)
					: $nullable;
			}
		);
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
	 * such as: <code>"123.45k"</code> or <code>"123.45 thousand"</code> for <code>123450.0</code>.<br>
	 * <br>
	 * This method may only be called before initialization.
	 * 
	 * @since 1.0.0
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsFloat(bool $nullable = false) : Property
	{
		$this->clearEvaluators()->addEvaluator(
			function (&$value) use ($nullable) : bool {
				return UType::evaluateFloat($value, $nullable);
			}
		);
		return $this;
	}
	
	/**
	 * Set to only allow a value strictly evaluated as a float.
	 * 
	 * This method may only be called before initialization.
	 * 
	 * @since 1.0.0
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsStrictFloat(bool $nullable = false) : Property
	{
		$this->clearEvaluators()->addEvaluator(
			function (&$value) use ($nullable) : bool {
				return isset($value) ? is_float($value) : $nullable;
			}
		);
		return $this;
	}
	
	/**
	 * Set to only allow a value evaluated as a size in bytes.
	 * 
	 * Only the following types and formats can be evaluated into a size in bytes:<br>
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
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsSize(bool $nullable = false) : Property
	{
		$this->clearEvaluators()->addEvaluator(
			function (&$value) use ($nullable) : bool {
				return UByte::evaluateSize($value, $nullable);
			}
		);
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
	 * @param bool $non_empty [default = false]
	 * <p>Do not allow an empty string value.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsString(bool $non_empty = false, bool $nullable = false) : Property
	{
		$this->clearEvaluators()->addEvaluator(
			function (&$value) use ($non_empty, $nullable) : bool {
				return UType::evaluateString($value, $non_empty, $nullable);
			}
		);
		return $this;
	}
	
	/**
	 * Set to only allow a value strictly evaluated as a string.
	 * 
	 * This method may only be called before initialization.
	 * 
	 * @since 1.0.0
	 * @param bool $non_empty [default = false]
	 * <p>Do not allow an empty string value.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsStrictString(bool $non_empty = false, bool $nullable = false) : Property
	{
		$this->clearEvaluators()->addEvaluator(
			function (&$value) use ($non_empty, $nullable) : bool {
				return isset($value) ? is_string($value) && (!$non_empty || $value !== '') : $nullable;
			}
		);
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
	 * @param object|string|null $object_class_interface [default = null]
	 * <p>The object or class which a value must be or extend from or the interface which a value must implement.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsClass($object_class_interface = null, bool $nullable = false) : Property
	{
		$this->clearEvaluators()->addEvaluator(
			function (&$value) use ($object_class_interface, $nullable) : bool {
				return UType::evaluateClass($value, $object_class_interface, $nullable);
			}
		);
		return $this;
	}
	
	/**
	 * Set to only allow a value strictly evaluated as a class.
	 * 
	 * This method may only be called before initialization.
	 * 
	 * @since 1.0.0
	 * @param object|string|null $object_class_interface [default = null]
	 * <p>The object or class which a value must be or extend from or the interface which a value must implement.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsStrictClass($object_class_interface = null, bool $nullable = false) : Property
	{
		$this->clearEvaluators()->addEvaluator(
			function (&$value) use ($object_class_interface, $nullable) : bool {
				return isset($value)
					? is_string($value) && UType::evaluateClass($value, $object_class_interface)
					: $nullable;
			}
		);
		return $this;
	}
	
	/**
	 * Set to only allow a value evaluated as an object.
	 * 
	 * Only the following types and formats can be evaluated into an object:<br>
	 * &nbsp; &#8226; &nbsp; a class string or object;<br>
	 * &nbsp; &#8226; &nbsp; an array with an <var>$object_class_interface</var> implementing 
	 * the <code>Feralygon\Kit\Interfaces\ArrayInstantiable</code> interface.<br>
	 * <br>
	 * This method may only be called before initialization.
	 * 
	 * @since 1.0.0
	 * @param object|string|null $object_class_interface [default = null]
	 * <p>The object or class which a value must be or extend from or the interface which a value must implement.</p>
	 * @param array $arguments [default = []]
	 * <p>The class constructor arguments to instantiate with.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsObject(
		$object_class_interface = null, array $arguments = [], bool $nullable = false
	) : Property
	{
		$this->clearEvaluators()->addEvaluator(
			function (&$value) use ($object_class_interface, $arguments, $nullable) : bool {
				return UType::evaluateObject($value, $object_class_interface, $arguments, $nullable);
			}
		);
		return $this;
	}
	
	/**
	 * Set to only allow a value strictly evaluated as an object.
	 * 
	 * This method may only be called before initialization.
	 * 
	 * @since 1.0.0
	 * @param object|string|null $object_class_interface [default = null]
	 * <p>The object or class which a value must be or extend from or the interface which a value must implement.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsStrictObject($object_class_interface = null, bool $nullable = false) : Property
	{
		$this->clearEvaluators()->addEvaluator(
			function (&$value) use ($object_class_interface, $nullable) : bool {
				return isset($value)
					? is_object($value) && UType::evaluateObject($value, $object_class_interface)
					: $nullable;
			}
		);
		return $this;
	}
	
	/**
	 * Set to only allow a value evaluated as a class or object.
	 * 
	 * Only the following types and formats can be evaluated into an object or class:<br>
	 * &nbsp; &#8226; &nbsp; a class string or object;<br>
	 * &nbsp; &#8226; &nbsp; an array with an <var>$object_class_interface</var> implementing 
	 * the <code>Feralygon\Kit\Interfaces\ArrayInstantiable</code> interface.<br>
	 * <br>
	 * This method may only be called before initialization.
	 * 
	 * @since 1.0.0
	 * @param object|string|null $object_class_interface [default = null]
	 * <p>The object or class which a value must be or extend from or the interface which a value must implement.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsObjectClass($object_class_interface = null, bool $nullable = false) : Property
	{
		$this->clearEvaluators()->addEvaluator(
			function (&$value) use ($object_class_interface, $nullable) : bool {
				return UType::evaluateObjectClass($value, $object_class_interface, $nullable);
			}
		);
		return $this;
	}
	
	/**
	 * Set to only allow a value evaluated as a callable.
	 * 
	 * This method may only be called before initialization.
	 * 
	 * @since 1.0.0
	 * @param callable|null $template [default = null]
	 * <p>The template callable declaration to validate the compatibility against.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @param bool $assertive [default = false]
	 * <p>Evaluate in an assertive manner, in other words, perform the heavier validations, 
	 * such as the template compatibility one, only when in a debug environment.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsCallable(
		?callable $template = null, bool $nullable = false, bool $assertive = false
	) : Property
	{
		$this->clearEvaluators()->addEvaluator(
			function (&$value) use ($template, $nullable, $assertive) : bool {
				return UCall::evaluate($value, $template, $nullable, $assertive);
			}
		);
		return $this;
	}
	
	/**
	 * Set to only allow a value evaluated as a closure.
	 * 
	 * This method may only be called before initialization.
	 * 
	 * @since 1.0.0
	 * @param callable|null $template [default = null]
	 * <p>The template callable declaration to validate the compatibility against.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @param bool $assertive [default = false]
	 * <p>Evaluate in an assertive manner, in other words, perform the heavier validations, 
	 * such as the template compatibility one, only when in a debug environment.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsClosure(
		?callable $template = null, bool $nullable = false, bool $assertive = false
	) : Property
	{
		$this->clearEvaluators()->addEvaluator(
			function (&$value) use ($template, $nullable, $assertive) : bool {
				return isset($value)
					? is_object($value) && UType::isA($value, \Closure::class) && 
						UCall::evaluate($value, $template, false, $assertive)
					: $nullable;
			}
		);
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
	 * @param callable|null $evaluator [default = null]
	 * <p>The evaluator function to use for each element in the resulting array value.<br>
	 * It is expected to be compatible with the following signature:<br><br>
	 * <code>function (&$key, &$value) : bool</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>int|string $key</b> [reference]</code><br>
	 * &nbsp; &nbsp; &nbsp; The key to evaluate (validate and sanitize).<br>
	 * &nbsp; &#8226; &nbsp; <code><b>mixed $value</b> [reference]</code><br>
	 * &nbsp; &nbsp; &nbsp; The value to evaluate (validate and sanitize).<br>
	 * <br>
	 * Return: <code><b>bool</b></code><br>
	 * Boolean <code>true</code> if the given array element is successfully evaluated.</p>
	 * @param bool $non_associative [default = false]
	 * <p>Do not allow an associative array value.</p>
	 * @param bool $non_empty [default = false]
	 * <p>Do not allow an empty array value.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsArray(
		?callable $evaluator = null, bool $non_associative = false, bool $non_empty = false, bool $nullable = false
	) : Property
	{
		$this->clearEvaluators()->addEvaluator(
			function (&$value) use ($evaluator, $non_associative, $non_empty, $nullable) : bool {
				return UData::evaluate($value, $evaluator, $non_associative, $non_empty, $nullable);
			}
		);
		return $this;
	}
	
	/**
	 * Set to only allow a value strictly evaluated as an array.
	 * 
	 * This method may only be called before initialization.
	 * 
	 * @since 1.0.0
	 * @param callable|null $evaluator [default = null]
	 * <p>The evaluator function to use for each element in the resulting array value.<br>
	 * It is expected to be compatible with the following signature:<br><br>
	 * <code>function (&$key, &$value) : bool</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>int|string $key</b> [reference]</code><br>
	 * &nbsp; &nbsp; &nbsp; The key to evaluate (validate and sanitize).<br>
	 * &nbsp; &#8226; &nbsp; <code><b>mixed $value</b> [reference]</code><br>
	 * &nbsp; &nbsp; &nbsp; The value to evaluate (validate and sanitize).<br>
	 * <br>
	 * Return: <code><b>bool</b></code><br>
	 * Boolean <code>true</code> if the given array element is successfully evaluated.</p>
	 * @param bool $non_associative [default = false]
	 * <p>Do not allow an associative array value.</p>
	 * @param bool $non_empty [default = false]
	 * <p>Do not allow an empty array value.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsStrictArray(
		?callable $evaluator = null, bool $non_associative = false, bool $non_empty = false, bool $nullable = false
	) : Property
	{
		$this->clearEvaluators()->addEvaluator(
			function (&$value) use ($evaluator, $non_associative, $non_empty, $nullable) : bool {
				return isset($value)
					? is_array($value) && UData::evaluate($value, $evaluator, $non_associative, $non_empty)
					: $nullable;
			}
		);
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
	 * @param string $enumeration
	 * <p>The enumeration class to use.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsEnumerationValue(string $enumeration, bool $nullable = false) : Property
	{
		$enumeration = UType::coerceClass($enumeration, Enumeration::class);
		$this->clearEvaluators()->addEvaluator(
			function (&$value) use ($enumeration, $nullable) : bool {
				return $enumeration::evaluateValue($value, $nullable);
			}
		);
		return $this;
	}
	
	/**
	 * Set to only allow a value strictly evaluated as an enumeration value.
	 * 
	 * This method may only be called before initialization.
	 * 
	 * @since 1.0.0
	 * @param string $enumeration
	 * <p>The enumeration class to use.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsStrictEnumerationValue(string $enumeration, bool $nullable = false) : Property
	{
		$enumeration = UType::coerceClass($enumeration, Enumeration::class);
		$this->clearEvaluators()->addEvaluator(
			function (&$value) use ($enumeration, $nullable) : bool {
				if ((is_int($value) || is_float($value) || is_string($value)) && $enumeration::hasValue($value)) {
					$value = $enumeration::getValue($value);
					return true;
				}
				return false;
			}
		);
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
	 * @param string $enumeration
	 * <p>The enumeration class to use.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsEnumerationName(string $enumeration, bool $nullable = false) : Property
	{
		$enumeration = UType::coerceClass($enumeration, Enumeration::class);
		$this->clearEvaluators()->addEvaluator(
			function (&$value) use ($enumeration, $nullable) : bool {
				return $enumeration::evaluateName($value, $nullable);
			}
		);
		return $this;
	}
	
	/**
	 * Set to only allow a value strictly evaluated as an enumeration name.
	 * 
	 * This method may only be called before initialization.
	 * 
	 * @since 1.0.0
	 * @param string $enumeration
	 * <p>The enumeration class to use.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsStrictEnumerationName(string $enumeration, bool $nullable = false) : Property
	{
		$enumeration = UType::coerceClass($enumeration, Enumeration::class);
		$this->clearEvaluators()->addEvaluator(
			function (&$value) use ($enumeration, $nullable) : bool {
				return is_string($value) && $enumeration::hasName($value);
			}
		);
		return $this;
	}
	
	/**
	 * Set to only allow a value evaluated as a structure instance.
	 * 
	 * Only <code>null</code>, an instance or array of properties, given as <samp>name => value</samp> pairs, 
	 * can be evaluated into a structure instance.<br>
	 * <br>
	 * This method may only be called before initialization.
	 * 
	 * @since 1.0.0
	 * @param string $class
	 * <p>The class to use.</p>
	 * @param bool $clone [default = false]
	 * <p>If an instance is given, then clone it into a new one with the same properties.</p>
	 * @param bool $readonly [default = false]
	 * <p>Evaluate into a read-only instance.<br>
	 * If an instance is given and is not read-only, 
	 * then a new one is created with the same properties and as read-only.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsStructure(string $class, bool $clone = false, bool $readonly = false) : Property
	{
		$class = UType::coerceClass($class, Structure::class);
		$this->clearEvaluators()->addEvaluator(
			function (&$value) use ($class, $clone, $readonly) : bool {
				return $class::evaluate($value, $clone, $readonly);
			}
		);
		return $this;
	}
	
	/**
	 * Set accessor functions.
	 * 
	 * By setting a getter and a setter function, the value will always be got and set using those functions.<br>
	 * <br>
	 * This method may only be called before initialization.
	 * 
	 * @since 1.0.0
	 * @param callable $getter
	 * <p>The getter function to set.<br>
	 * It is expected to be compatible with the following signature:<br><br>
	 * <code>function ()</code><br>
	 * <br>
	 * Return: <code><b>mixed</b></code><br>
	 * The value.</p>
	 * @param callable $setter
	 * <p>The setter function to set.<br>
	 * It is expected to be compatible with the following signature:<br><br>
	 * <code>function ($value) : void</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>mixed $value</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The value to set.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAccessors(callable $getter, callable $setter) : Property
	{
		//guard
		UCall::guard(!$this->initialized, [
			'hint_message' => "This method may only be called before initialization, " . 
				"in property {{property.getName()}} in manager with owner {{property.getManager().getOwner()}}.",
			'parameters' => ['property' => $this]
		]);
		
		//set
		UCall::assert('getter', $getter, function () {});
		UCall::assert('setter', $setter, function ($value) : void {});
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
	 * @param string|null $class [default = null]
	 * <p>The class scope to use.<br>
	 * If not set, then the manager owner object of this instance is used.</p>
	 * @param string|null $name [default = null]
	 * <p>The name to bind to.<br>
	 * If not set, then the name set in this instance is used.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function bind(?string $class = null, ?string $name = null) : Property
	{
		//guard
		UCall::guard(!$this->initialized, [
			'hint_message' => "This method may only be called before initialization, " . 
				"in property {{property.getName()}} in manager with owner {{property.getManager().getOwner()}}.",
			'parameters' => ['property' => $this]
		]);
		
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
