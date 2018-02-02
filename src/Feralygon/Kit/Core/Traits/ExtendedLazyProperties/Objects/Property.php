<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Traits\ExtendedLazyProperties\Objects;

use Feralygon\Kit\Core\Traits\ExtendedLazyProperties\Objects\Property\Exceptions;
use Feralygon\Kit\Core\Utilities\{
	Call as UCall,
	Data as UData,
	Type as UType
};

/**
 * Core extended lazy properties trait property object class.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Core\Traits\ExtendedLazyProperties
 */
final class Property
{	
	//Private constants
	/** Allowed modes. */
	private const MODES = ['rw', 'r', 'w', 'w-'];
	
	
	
	//Private properties
	/** @var object */
	private $owner;
	
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
	
	
	
	//Final public magic methods
	/**
	 * Instantiate class.
	 * 
	 * @since 1.0.0
	 * @param object $owner <p>The owner object.</p>
	 * @throws \Feralygon\Kit\Core\Traits\ExtendedLazyProperties\Objects\Property\Exceptions\InvalidOwner
	 */
	final public function __construct($owner)
	{
		if (!is_object($owner)) {
			throw new Exceptions\InvalidOwner(['property' => $this, 'owner' => $owner]);
		}
		$this->owner = $owner;
	}
	
	
	
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
	 * Boolean <code>true</code> if the given value is successfully evaluated.
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
	 * <code>"on"</code> or <code>"yes"</code> for boolean <code>true</code>.
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
	 * such as: <code>"123kB"</code> or <code>"123 kilobytes"</code> for <code>123000</code>.
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
	 * such as: <code>"123kB"</code> or <code>"123 kilobytes"</code> for <code>123000</code>.
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
	 * such as: <code>"123.45kB"</code> or <code>"123.45 kilobytes"</code> for <code>123450.0</code>.
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
	 * Only a string, integer or float can be evaluated into a string.
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
	 * Only a class string or object can be evaluated into a class.
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
	 * Only a class string or object can be evaluated into an object.
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
	 * Only a class string or object can be evaluated into an object or class.
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
	 * @since 1.0.0
	 * @param callable|null $template [default = null] <p>The template callable declaration 
	 * to validate the signature against.</p>
	 * @param bool $nullable [default = false] <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @param bool $assertive [default = false] <p>Evaluate in an assertive manner, in other words, 
	 * perform the heavier validations, such as the template one, only when in a debug environment.</p>
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
	 * @since 1.0.0
	 * @param callable|null $template [default = null] <p>The template callable declaration 
	 * to validate the signature against.</p>
	 * @param bool $nullable [default = false] <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @param bool $assertive [default = false] <p>Evaluate in an assertive manner, in other words, 
	 * perform the heavier validations, such as the template one, only when in a debug environment.</p>
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
	 * @since 1.0.0
	 * @param callable|null $evaluator [default = null] <p>The evaluator function to use for each element 
	 * in the resulting array value.<br>
	 * The expected function signature is represented as:<br><br>
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
	 * @since 1.0.0
	 * @param callable|null $evaluator [default = null] <p>The evaluator function to use for each element 
	 * in the resulting array value.<br>
	 * The expected function signature is represented as:<br><br>
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
		return $this;
	}
	
	/**
	 * Bind to a given property name using a given class scope.
	 * 
	 * By binding to a property, getter and setter functions are automatically set for that property, 
	 * using the given class scope, so it can be accessed and modified directly from outside.<br>
	 * All restrictions set in this property still apply however, therefore attempts at accessing and modifying it 
	 * may still fail accordingly.
	 * 
	 * @since 1.0.0
	 * @param string $name <p>The property name to bind to.</p>
	 * @param string $class_scope <p>The class scope to use.</p>
	 * @return $this <p>This instance, for chaining purposes.</p>
	 */
	final public function bind(string $name, string $class_scope) : Property
	{
		//getter
		$this->setGetter(\Closure::bind(function () use ($name) {
			return $this->$name;
		}, $this->owner, $class_scope));
		
		//setter
		$this->setSetter(\Closure::bind(function ($value) use ($name) : void {
			$this->$name = $value;
		}, $this->owner, $class_scope));
		
		//return
		return $this;
	}
}
