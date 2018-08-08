<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Managers;

use Feralygon\Kit\{
	Component,
	Enumeration,
	Structure,
	Options
};
use Feralygon\Kit\Primitives\{
	Dictionary,
	Vector
};
use Feralygon\Kit\Utilities\{
	Byte as UByte,
	Call as UCall,
	Data as UData,
	Hash as UHash,
	Time as UTime,
	Type as UType
};

/**
 * This manager handles the evaluator functions of an object, and adds some common ones.
 * 
 * @since 1.0.0
 */
class Evaluators
{
	//Private properties
	/** @var object */
	private $owner;
	
	/** @var \Closure[] */
	private $evaluators = [];
	
	/** @var \Closure[] */
	private $addition_callbacks = [];
	
	/** @var bool */
	private $locked = false;
	
	/** @var \Feralygon\Kit\Utilities\Call\Options\Guard|array|callable|null */
	private $locked_guard_options = null;
	
	
	
	//Final public magic methods
	/**
	 * Instantiate class.
	 * 
	 * @since 1.0.0
	 * @param object $owner
	 * <p>The owner object.</p>
	 */
	final public function __construct(object $owner)
	{
		$this->owner = $owner;
		$this->locked_guard_options = [
			'error_message' => "This method has been locked in manager with owner {{owner}}.",
			'parameters' => ['owner' => $this->owner]
		];
	}
	
	
	
	//Final public methods
	/**
	 * Get owner object.
	 * 
	 * @since 1.0.0
	 * @return object
	 * <p>The owner object.</p>
	 */
	final public function getOwner(): object
	{
		return $this->owner;
	}
	
	/**
	 * Add evaluator function.
	 * 
	 * @since 1.0.0
	 * @param callable $evaluator
	 * <p>The evaluator function to add.<br>
	 * It is expected to be compatible with the following signature:<br><br>
	 * <code>function (&$value): bool</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>mixed $value</b> [reference]</code><br>
	 * &nbsp; &nbsp; &nbsp; The value to evaluate (validate and sanitize).<br>
	 * <br>
	 * Return: <code><b>bool</b></code><br>
	 * Boolean <code>true</code> if the given value was successfully evaluated.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function add(callable $evaluator): Evaluators
	{
		//initialize
		UCall::guard(!$this->locked, $this->locked_guard_options);
		UCall::assert('evaluator', $evaluator, function (&$value): bool {});
		
		//add
		$evaluator = \Closure::fromCallable($evaluator);
		$this->evaluators[] = $evaluator;
		
		//callbacks
		foreach ($this->addition_callbacks as $callback) {
			$callback($evaluator);
		}
		
		//return
		return $this;
	}
	
	/**
	 * Set evaluator function.
	 * 
	 * @since 1.0.0
	 * @param callable $evaluator
	 * <p>The evaluator function to set.<br>
	 * It is expected to be compatible with the following signature:<br><br>
	 * <code>function (&$value): bool</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>mixed $value</b> [reference]</code><br>
	 * &nbsp; &nbsp; &nbsp; The value to evaluate (validate and sanitize).<br>
	 * <br>
	 * Return: <code><b>bool</b></code><br>
	 * Boolean <code>true</code> if the given value was successfully evaluated.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function set(callable $evaluator): Evaluators
	{
		$this->clear()->add($evaluator);
		return $this;
	}
	
	/**
	 * Add evaluator addition callback function.
	 * 
	 * The given callback function is called whenever a new evaluator function is added.
	 * 
	 * @since 1.0.0
	 * @param callable $callback
	 * <p>The callback function to add.<br>
	 * It is expected to be compatible with the following signature:<br><br>
	 * <code>function (callable $evaluator): void</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>callable $evaluator</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The added evaluator function, with the following characteristics:<br>
	 * &nbsp; &nbsp; &#8594; signature: <code>function (&$value): bool</code><br>
	 * &nbsp; &nbsp; &#8594; parameters:<br>
	 * &nbsp; &nbsp; &nbsp; &nbsp; &#9656; <code>mixed $value [reference]</code><br>
	 * &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; The value to evaluate (validate and sanitize).<br>
	 * &nbsp; &nbsp; &#8594; return: <code>bool</code><br>
	 * &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; Boolean <code>true</code> if the given value was successfully evaluated.<br>
	 * <br>
	 * Return: <code><b>void</b></code></p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function addAdditionCallback(callable $callback): Evaluators
	{
		UCall::guard(!$this->locked, $this->locked_guard_options);
		UCall::assert('callback', $callback, function (callable $evaluator): void {});
		$this->addition_callbacks[] = \Closure::fromCallable($callback);
		return $this;
	}
	
	/**
	 * Get all evaluator functions.
	 * 
	 * @since 1.0.0
	 * @return \Closure[]
	 * <p>All the evaluator functions.</p>
	 */
	final public function getAll(): array
	{
		return $this->evaluators;
	}
	
	/**
	 * Clear all evaluator functions.
	 * 
	 * @since 1.0.0
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function clear(): Evaluators
	{
		UCall::guard(!$this->locked, $this->locked_guard_options);
		$this->evaluators = [];
		return $this;
	}
	
	/**
	 * Check if is locked.
	 * 
	 * @since 1.0.0
	 * @return bool
	 * <p>Boolean <code>true</code> if is locked.</p>
	 */
	final public function isLocked(): bool
	{
		return $this->locked;
	}
	
	/**
	 * Lock.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Utilities\Call\Options\Guard|array|callable|null $guard_options [default = null]
	 * <p>The guard options to set, as an instance, <samp>name => value</samp> pairs or a function compatible 
	 * with the following signature:<br><br>
	 * <code>function ()</code><br>
	 * <br>
	 * Return: <code><b>\Feralygon\Kit\Utilities\Call\Options\Guard|array</b></code><br>
	 * The options, as an instance or <samp>name => value</samp> pairs.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function lock($guard_options = null): Evaluators
	{
		$this->locked = true;
		if (isset($guard_options)) {
			$this->locked_guard_options = $guard_options;
		}
		return $this;
	}
	
	/**
	 * Evaluate a given value.
	 * 
	 * @since 1.0.0
	 * @param mixed $value [reference]
	 * <p>The value to evaluate (validate and sanitize).</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value was successfully evaluated.</p>
	 */
	final public function evaluate(&$value): bool
	{
		$v = $value;
		foreach ($this->evaluators as $evaluator) {
			if (!$evaluator($v)) {
				return false;
			}
		}
		$value = $v;
		return true;
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
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsBoolean(bool $nullable = false): Evaluators
	{
		$this->set(
			function (&$value) use ($nullable): bool {
				return UType::evaluateBoolean($value, $nullable);
			}
		);
		return $this;
	}
	
	/**
	 * Set to only allow a value strictly evaluated as a boolean.
	 * 
	 * @since 1.0.0
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsStrictBoolean(bool $nullable = false): Evaluators
	{
		$this->set(
			function (&$value) use ($nullable): bool {
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
	 * such as: <code>"123k"</code> or <code>"123 thousand"</code> for <code>123000</code>.
	 * 
	 * @since 1.0.0
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsNumber(bool $nullable = false): Evaluators
	{
		$this->set(
			function (&$value) use ($nullable): bool {
				return UType::evaluateNumber($value, $nullable);
			}
		);
		return $this;
	}
	
	/**
	 * Set to only allow a value strictly evaluated as a number.
	 * 
	 * @since 1.0.0
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsStrictNumber(bool $nullable = false): Evaluators
	{
		$this->set(
			function (&$value) use ($nullable): bool {
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
	 * such as: <code>"123k"</code> or <code>"123 thousand"</code> for <code>123000</code>.
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
	final public function setAsInteger(bool $unsigned = false, ?int $bits = null, bool $nullable = false): Evaluators
	{
		$this->set(
			function (&$value) use ($unsigned, $bits, $nullable): bool {
				return UType::evaluateInteger($value, $unsigned, $bits, $nullable);
			}
		);
		return $this;
	}
	
	/**
	 * Set to only allow a value strictly evaluated as an integer.
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
	): Evaluators
	{
		$this->set(
			function (&$value) use ($unsigned, $bits, $nullable): bool {
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
	 * such as: <code>"123.45k"</code> or <code>"123.45 thousand"</code> for <code>123450.0</code>.
	 * 
	 * @since 1.0.0
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsFloat(bool $nullable = false): Evaluators
	{
		$this->set(
			function (&$value) use ($nullable): bool {
				return UType::evaluateFloat($value, $nullable);
			}
		);
		return $this;
	}
	
	/**
	 * Set to only allow a value strictly evaluated as a float.
	 * 
	 * @since 1.0.0
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsStrictFloat(bool $nullable = false): Evaluators
	{
		$this->set(
			function (&$value) use ($nullable): bool {
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
	 * such as: <code>"123kB"</code> or <code>"123 kilobytes"</code> for <code>123000</code>.
	 * 
	 * @since 1.0.0
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsSize(bool $nullable = false): Evaluators
	{
		$this->set(
			function (&$value) use ($nullable): bool {
				return UByte::evaluateSize($value, $nullable);
			}
		);
		return $this;
	}
	
	/**
	 * Set to only allow a value evaluated as a string.
	 * 
	 * Only the following types and formats can be evaluated into a string:<br>
	 * &nbsp; &#8226; &nbsp; a string, integer or float;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>__toString</code> method;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Feralygon\Kit\Interfaces\Stringifiable</code> interface.
	 * 
	 * @since 1.0.0
	 * @param bool $non_empty [default = false]
	 * <p>Do not allow an empty string value.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsString(bool $non_empty = false, bool $nullable = false): Evaluators
	{
		$this->set(
			function (&$value) use ($non_empty, $nullable): bool {
				return UType::evaluateString($value, $non_empty, $nullable);
			}
		);
		return $this;
	}
	
	/**
	 * Set to only allow a value strictly evaluated as a string.
	 * 
	 * @since 1.0.0
	 * @param bool $non_empty [default = false]
	 * <p>Do not allow an empty string value.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsStrictString(bool $non_empty = false, bool $nullable = false): Evaluators
	{
		$this->set(
			function (&$value) use ($non_empty, $nullable): bool {
				return isset($value) ? is_string($value) && (!$non_empty || $value !== '') : $nullable;
			}
		);
		return $this;
	}
	
	/**
	 * Set to only allow a value evaluated as a class.
	 * 
	 * Only a class string or object can be evaluated into a class.
	 * 
	 * @since 1.0.0
	 * @param object|string|null $object_class_interface [default = null]
	 * <p>The object or class which a value must be or extend from or the interface which a value must implement.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsClass($object_class_interface = null, bool $nullable = false): Evaluators
	{
		$this->set(
			function (&$value) use ($object_class_interface, $nullable): bool {
				return UType::evaluateClass($value, $object_class_interface, $nullable);
			}
		);
		return $this;
	}
	
	/**
	 * Set to only allow a value strictly evaluated as a class.
	 * 
	 * @since 1.0.0
	 * @param object|string|null $object_class_interface [default = null]
	 * <p>The object or class which a value must be or extend from or the interface which a value must implement.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsStrictClass($object_class_interface = null, bool $nullable = false): Evaluators
	{
		$this->set(
			function (&$value) use ($object_class_interface, $nullable): bool {
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
	 * the <code>Feralygon\Kit\Interfaces\ArrayInstantiable</code> interface.
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
	): Evaluators
	{
		$this->set(
			function (&$value) use ($object_class_interface, $arguments, $nullable): bool {
				return UType::evaluateObject($value, $object_class_interface, $arguments, $nullable);
			}
		);
		return $this;
	}
	
	/**
	 * Set to only allow a value strictly evaluated as an object.
	 * 
	 * @since 1.0.0
	 * @param object|string|null $object_class_interface [default = null]
	 * <p>The object or class which a value must be or extend from or the interface which a value must implement.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsStrictObject($object_class_interface = null, bool $nullable = false): Evaluators
	{
		$this->set(
			function (&$value) use ($object_class_interface, $nullable): bool {
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
	 * the <code>Feralygon\Kit\Interfaces\ArrayInstantiable</code> interface.
	 * 
	 * @since 1.0.0
	 * @param object|string|null $object_class_interface [default = null]
	 * <p>The object or class which a value must be or extend from or the interface which a value must implement.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsObjectClass($object_class_interface = null, bool $nullable = false): Evaluators
	{
		$this->set(
			function (&$value) use ($object_class_interface, $nullable): bool {
				return UType::evaluateObjectClass($value, $object_class_interface, $nullable);
			}
		);
		return $this;
	}
	
	/**
	 * Set to only allow a value evaluated as a callable.
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
	): Evaluators
	{
		$this->set(
			function (&$value) use ($template, $nullable, $assertive): bool {
				return UCall::evaluate($value, $template, $nullable, $assertive);
			}
		);
		return $this;
	}
	
	/**
	 * Set to only allow a value evaluated as a closure.
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
	): Evaluators
	{
		$this->set(
			function (&$value) use ($template, $nullable, $assertive): bool {
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
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Feralygon\Kit\Interfaces\Arrayable</code> interface.
	 * 
	 * @since 1.0.0
	 * @param callable|null $evaluator [default = null]
	 * <p>The evaluator function to use for each element in the resulting array.<br>
	 * It is expected to be compatible with the following signature:<br><br>
	 * <code>function (&$key, &$value): bool</code><br>
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
	 * <p>Do not allow an associative array.</p>
	 * @param bool $non_empty [default = false]
	 * <p>Do not allow an empty array.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsArray(
		?callable $evaluator = null, bool $non_associative = false, bool $non_empty = false, bool $nullable = false
	): Evaluators
	{
		$this->set(
			function (&$value) use ($evaluator, $non_associative, $non_empty, $nullable): bool {
				return UData::evaluate($value, $evaluator, $non_associative, $non_empty, $nullable);
			}
		);
		return $this;
	}
	
	/**
	 * Set to only allow a value strictly evaluated as an array.
	 * 
	 * @since 1.0.0
	 * @param callable|null $evaluator [default = null]
	 * <p>The evaluator function to use for each element in the resulting array.<br>
	 * It is expected to be compatible with the following signature:<br><br>
	 * <code>function (&$key, &$value): bool</code><br>
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
	 * <p>Do not allow an associative array.</p>
	 * @param bool $non_empty [default = false]
	 * <p>Do not allow an empty array.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsStrictArray(
		?callable $evaluator = null, bool $non_associative = false, bool $non_empty = false, bool $nullable = false
	): Evaluators
	{
		$this->set(
			function (&$value) use ($evaluator, $non_associative, $non_empty, $nullable): bool {
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
	 * Only an enumeration element given as an integer, float or string can be evaluated into an enumeration value.
	 * 
	 * @since 1.0.0
	 * @param string $enumeration
	 * <p>The enumeration class to use.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsEnumerationValue(string $enumeration, bool $nullable = false): Evaluators
	{
		$enumeration = UType::coerceClass($enumeration, Enumeration::class);
		$this->set(
			function (&$value) use ($enumeration, $nullable): bool {
				return $enumeration::evaluateValue($value, $nullable);
			}
		);
		return $this;
	}
	
	/**
	 * Set to only allow a value strictly evaluated as an enumeration value.
	 * 
	 * @since 1.0.0
	 * @param string $enumeration
	 * <p>The enumeration class to use.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsStrictEnumerationValue(string $enumeration, bool $nullable = false): Evaluators
	{
		$enumeration = UType::coerceClass($enumeration, Enumeration::class);
		$this->set(
			function (&$value) use ($enumeration, $nullable): bool {
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
	 * Only an enumeration element given as an integer, float or string can be evaluated into an enumeration name.
	 * 
	 * @since 1.0.0
	 * @param string $enumeration
	 * <p>The enumeration class to use.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsEnumerationName(string $enumeration, bool $nullable = false): Evaluators
	{
		$enumeration = UType::coerceClass($enumeration, Enumeration::class);
		$this->set(
			function (&$value) use ($enumeration, $nullable): bool {
				return $enumeration::evaluateName($value, $nullable);
			}
		);
		return $this;
	}
	
	/**
	 * Set to only allow a value strictly evaluated as an enumeration name.
	 * 
	 * @since 1.0.0
	 * @param string $enumeration
	 * <p>The enumeration class to use.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsStrictEnumerationName(string $enumeration, bool $nullable = false): Evaluators
	{
		$enumeration = UType::coerceClass($enumeration, Enumeration::class);
		$this->set(
			function (&$value) use ($enumeration, $nullable): bool {
				return is_string($value) && $enumeration::hasName($value);
			}
		);
		return $this;
	}
	
	/**
	 * Set to only allow a value evaluated as a hash.
	 * 
	 * Only the following types and formats can be evaluated into a hash:<br>
	 * &nbsp; &#8226; &nbsp; a hexadecimal notation string;<br>
	 * &nbsp; &#8226; &nbsp; a Base64 or an URL-safe Base64 encoded string;<br>
	 * &nbsp; &#8226; &nbsp; a raw binary string.
	 * 
	 * @since 1.0.0
	 * @param int $bits
	 * <p>The number of bits to evaluate with.<br>
	 * It must be a multiple of <code>8</code> and be greater than <code>0</code>.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsHash(int $bits, bool $nullable = false): Evaluators
	{
		$this->set(
			function (&$value) use ($bits, $nullable): bool {
				return UHash::evaluate($value, $bits, $nullable);
			}
		);
		return $this;
	}
	
	/**
	 * Set to only allow a value evaluated as a date and time.
	 * 
	 * Only the following types and formats can be evaluated into a date and time:<br>
	 * &nbsp; &#8226; &nbsp; an integer or float as the number of seconds since 1970-01-01 00:00:00 UTC, 
	 * such as: <code>1483268400</code> for <samp>2017-01-01 12:00:00</samp>;<br>
	 * &nbsp; &#8226; &nbsp; a string as supported by the PHP <code>strtotime</code> function, 
	 * such as: <samp>2017-Jan-01 12:00:00</samp> for <samp>2017-01-01 12:00:00</samp>;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>DateTimeInterface</code> interface.
	 * 
	 * @since 1.0.0
	 * @param string|null $format [default = null]
	 * <p>The format to evaluate into, as supported by the PHP <code>date</code> function, 
	 * or as a <code>DateTime</code> or <code>DateTimeImmutable</code> class to instantiate.<br>
	 * If not set, then the given value is evaluated into an integer as an Unix timestamp.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsDateTime(?string $format = null, bool $nullable = false): Evaluators
	{
		$this->set(
			function (&$value) use ($format, $nullable): bool {
				return UTime::evaluateDateTime($value, $format, $nullable);
			}
		);
		return $this;
	}
	
	/**
	 * Set to only allow a value evaluated as a date.
	 * 
	 * Only the following types and formats can be evaluated into a date:<br>
	 * &nbsp; &#8226; &nbsp; an integer or float as the number of seconds since 1970-01-01, 
	 * such as: <code>1483228800</code> for <samp>2017-01-01</samp>;<br>
	 * &nbsp; &#8226; &nbsp; a string as supported by the PHP <code>strtotime</code> function, 
	 * such as: <samp>2017-Jan-01</samp> for <samp>2017-01-01</samp>;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>DateTimeInterface</code> interface.
	 * 
	 * @since 1.0.0
	 * @param string|null $format [default = null]
	 * <p>The format to evaluate into, as supported by the PHP <code>date</code> function, 
	 * or as a <code>DateTime</code> or <code>DateTimeImmutable</code> class to instantiate.<br>
	 * If not set, then the given value is evaluated into an integer as an Unix timestamp.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsDate(?string $format = null, bool $nullable = false): Evaluators
	{
		$this->set(
			function (&$value) use ($format, $nullable): bool {
				return UTime::evaluateDate($value, $format, $nullable);
			}
		);
		return $this;
	}
	
	/**
	 * Set to only allow a value evaluated as a time.
	 * 
	 * Only the following types and formats can be evaluated into a time:<br>
	 * &nbsp; &#8226; &nbsp; an integer or float as the number of seconds, 
	 * such as: <code>50700</code> for <samp>14:05:00</samp>;<br>
	 * &nbsp; &#8226; &nbsp; a string as supported by the PHP <code>strtotime</code> function, 
	 * such as: <samp>2:05PM</samp> for <samp>14:05:00</samp>;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>DateTimeInterface</code> interface.
	 * 
	 * @since 1.0.0
	 * @param string|null $format [default = null]
	 * <p>The format to evaluate into, as supported by the PHP <code>date</code> function, 
	 * or as a <code>DateTime</code> or <code>DateTimeImmutable</code> class to instantiate.<br>
	 * If not set, then the given value is evaluated into an integer as an Unix timestamp.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsTime(?string $format = null, bool $nullable = false): Evaluators
	{
		$this->set(
			function (&$value) use ($format, $nullable): bool {
				return UTime::evaluateTime($value, $format, $nullable);
			}
		);
		return $this;
	}
	
	/**
	 * Set to only allow a value evaluated as a component instance.
	 * 
	 * Only a component instance or name, or a prototype instance, class or name, can be evaluated into an instance.
	 * 
	 * @since 1.0.0
	 * @param string $class
	 * <p>The class to use.</p>
	 * @param array $properties [default = []]
	 * <p>The properties to evaluate with, as <samp>name => value</samp> pairs.<br>
	 * If a component or prototype instance is given, then the given properties are ignored.</p>
	 * @param callable|null $builder [default = null]
	 * <p>The function to use to build an instance.<br>
	 * It is expected to be compatible with the following signature:<br><br>
	 * <code>function ($prototype, array $properties): Feralygon\Kit\Component</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>Feralygon\Kit\Prototype|string $prototype</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The prototype instance, class or name to build with.<br>
	 * &nbsp; &#8226; &nbsp; <code><b>array $properties</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The properties to build with, as <samp>name => value</samp> pairs.<br>
	 * <br>
	 * Return: <code><b>Feralygon\Kit\Component</b></code><br>
	 * The built instance.</p>
	 * @param callable|null $named_builder [default = null]
	 * <p>The function to use to build an instance for a given name.<br>
	 * It is expected to be compatible with the following signature:<br><br>
	 * <code>function (string $name, array $properties): ?Feralygon\Kit\Component</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>string $name</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The name to build for.<br>
	 * &nbsp; &#8226; &nbsp; <code><b>array $properties</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The properties to build with, as <samp>name => value</samp> pairs.<br>
	 * <br>
	 * Return: <code><b>Feralygon\Kit\Component|null</b></code><br>
	 * The built instance for the given name or <code>null</code> if none was built.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsComponent(
		string $class, array $properties = [], ?callable $builder = null, ?callable $named_builder = null,
		bool $nullable = false
	): Evaluators
	{
		$class = UType::coerceClass($class, Component::class);
		$this->set(
			function (&$value) use ($class, $properties, $builder, $named_builder, $nullable): bool {
				return $class::evaluate($value, $properties, $builder, $named_builder, $nullable);
			}
		);
		return $this;
	}
	
	/**
	 * Set to only allow a value evaluated as an options instance.
	 * 
	 * Only <code>null</code>, an instance or array of properties, given as <samp>name => value</samp> pairs, 
	 * can be evaluated into an options instance.
	 * 
	 * @since 1.0.0
	 * @param string $class
	 * <p>The class to use.</p>
	 * @param bool $clone [default = false]
	 * <p>If an instance is given, then clone it into a new one with the same properties.</p>
	 * @param bool|null $readonly [default = null]
	 * <p>Evaluate into either a non-read-only or read-only instance.<br>
	 * If set and if an instance is given and its read-only state does not match, 
	 * then a new one is created with the same properties and read-only state.</p>
	 * @param callable|null $builder [default = null]
	 * <p>The function to use to build an instance.<br>
	 * It is expected to be compatible with the following signature:<br><br>
	 * <code>function (array $properties, bool $readonly): Feralygon\Kit\Options</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>array $properties</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The properties to build with, as <samp>name => value</samp> pairs.<br>
	 * &nbsp; &#8226; &nbsp; <code><b>bool $readonly</b></code><br>
	 * &nbsp; &nbsp; &nbsp; Set the built instance as read-only.<br>
	 * <br>
	 * Return: <code><b>Feralygon\Kit\Options</b></code><br>
	 * The built instance.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsOptions(
		string $class, bool $clone = false, ?bool $readonly = null, ?callable $builder = null, bool $nullable = false
	): Evaluators
	{
		$class = UType::coerceClass($class, Options::class);
		$this->set(
			function (&$value) use ($class, $clone, $readonly, $builder, $nullable): bool {
				return $class::evaluate($value, $clone, $readonly, $builder, $nullable);
			}
		);
		return $this;
	}
	
	/**
	 * Set to only allow a value evaluated as a structure instance.
	 * 
	 * Only <code>null</code>, an instance or array of properties, given as <samp>name => value</samp> pairs, 
	 * can be evaluated into a structure instance.
	 * 
	 * @since 1.0.0
	 * @param string $class
	 * <p>The class to use.</p>
	 * @param bool $clone [default = false]
	 * <p>If an instance is given, then clone it into a new one with the same properties.</p>
	 * @param bool|null $readonly [default = null]
	 * <p>Evaluate into either a non-read-only or read-only instance.<br>
	 * If set and if an instance is given and its read-only state does not match, 
	 * then a new one is created with the same properties and read-only state.</p>
	 * @param callable|null $builder [default = null]
	 * <p>The function to use to build an instance.<br>
	 * It is expected to be compatible with the following signature:<br><br>
	 * <code>function (array $properties, bool $readonly): Feralygon\Kit\Structure</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>array $properties</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The properties to build with, as <samp>name => value</samp> pairs.<br>
	 * &nbsp; &#8226; &nbsp; <code><b>bool $readonly</b></code><br>
	 * &nbsp; &nbsp; &nbsp; Set the built instance as read-only.<br>
	 * <br>
	 * Return: <code><b>Feralygon\Kit\Structure</b></code><br>
	 * The built instance.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsStructure(
		string $class, bool $clone = false, ?bool $readonly = null, ?callable $builder = null, bool $nullable = false
	): Evaluators
	{
		$class = UType::coerceClass($class, Structure::class);
		$this->set(
			function (&$value) use ($class, $clone, $readonly, $builder, $nullable): bool {
				return $class::evaluate($value, $clone, $readonly, $builder, $nullable);
			}
		);
		return $this;
	}
	
	/**
	 * Set to only allow a value evaluated as a dictionary instance.
	 * 
	 * Only the following types and formats can be evaluated into a dictionary instance:<br>
	 * &nbsp; &#8226; &nbsp; an instance;<br>
	 * &nbsp; &#8226; &nbsp; an associative array;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Feralygon\Kit\Interfaces\Arrayable</code> interface.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Primitives\Dictionary|null $template [default = null]
	 * <p>The template instance to clone from and evaluate into.</p>
	 * @param bool $clone [default = false]
	 * <p>If an instance is given, then clone it into a new one with the same pairs and evaluator functions.</p>
	 * @param bool|null $readonly [default = null]
	 * <p>Evaluate into either a non-read-only or read-only instance.<br>
	 * If set and if an instance is given and its read-only state does not match, 
	 * then a new one is created with the same pairs and read-only state.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsDictionary(
		?Dictionary $template = null, bool $clone = false, ?bool $readonly = null, bool $nullable = false
	): Evaluators
	{
		$this->set(
			function (&$value) use ($template, $clone, $readonly, $nullable): bool {
				return Dictionary::evaluate($value, $template, $clone, $readonly, $nullable);
			}
		);
		return $this;
	}
	
	/**
	 * Set to only allow a value evaluated as a vector instance.
	 * 
	 * Only the following types and formats can be evaluated into a vector instance:<br>
	 * &nbsp; &#8226; &nbsp; an instance;<br>
	 * &nbsp; &#8226; &nbsp; a non-associative array;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Feralygon\Kit\Interfaces\Arrayable</code> interface.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Primitives\Vector|null $template [default = null]
	 * <p>The template instance to clone from and evaluate into.</p>
	 * @param bool $clone [default = false]
	 * <p>If an instance is given, then clone it into a new one with the same values and evaluator functions.</p>
	 * @param bool|null $readonly [default = null]
	 * <p>Evaluate into either a non-read-only or read-only instance.<br>
	 * If set and if an instance is given and its read-only state does not match, 
	 * then a new one is created with the same values and read-only state.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsVector(
		?Vector $template = null, bool $clone = false, ?bool $readonly = null, bool $nullable = false
	): Evaluators
	{
		$this->set(
			function (&$value) use ($template, $clone, $readonly, $nullable): bool {
				return Vector::evaluate($value, $template, $clone, $readonly, $nullable);
			}
		);
		return $this;
	}
}
