<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Traits;

use Dracodeum\Kit\Managers\Evaluators as Manager;
use Dracodeum\Kit\Primitives\{
	Dictionary,
	Vector
};
use Dracodeum\Kit\Traits\DebugInfo\Info as DebugInfo;

/** This trait enables the support for evaluators in a class, and adds some common ones. */
trait Evaluators
{
	//Private properties
	/** @var \Dracodeum\Kit\Managers\Evaluators|null */
	private $evaluators_manager = null;
	
	
	
	//Final public methods
	/**
	 * Add evaluator function.
	 * 
	 * @param callable $evaluator
	 * <p>The evaluator function to add.<br>
	 * It is expected to be compatible with the following signature:<br>
	 * <br>
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
	final public function addEvaluator(callable $evaluator): object
	{
		$this->getEvaluatorsManager()->add($evaluator);
		return $this;
	}
	
	/**
	 * Set evaluator function.
	 * 
	 * @param callable $evaluator
	 * <p>The evaluator function to set.<br>
	 * It is expected to be compatible with the following signature:<br>
	 * <br>
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
	final public function setEvaluator(callable $evaluator): object
	{
		$this->getEvaluatorsManager()->set($evaluator);
		return $this;
	}
	
	/**
	 * Get evaluator functions.
	 * 
	 * @return \Closure[]
	 * <p>The evaluator functions.</p>
	 */
	final public function getEvaluators(): array
	{
		return $this->getEvaluatorsManager()->getAll();
	}
	
	/**
	 * Clear all evaluator functions.
	 * 
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function clearEvaluators(): object
	{
		$this->getEvaluatorsManager()->clear();
		return $this;
	}
	
	/**
	 * Lock evaluators.
	 * 
	 * @param \Dracodeum\Kit\Utilities\Call\Options\Halt|array|callable|null $halt_options [default = null]
	 * <p>The halt options to set, as an instance, <samp>name => value</samp> pairs or a function compatible 
	 * with the following signature:<br>
	 * <br>
	 * <code>function ()</code><br>
	 * <br>
	 * Return: <code><b>\Dracodeum\Kit\Utilities\Call\Options\Halt|array</b></code><br>
	 * The options, as an instance or <samp>name => value</samp> pairs.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function lockEvaluators($halt_options = null): object
	{
		$this->getEvaluatorsManager()->lock($halt_options);
		return $this;
	}
	
	/**
	 * Set to only allow a value evaluated as a boolean.
	 * 
	 * Only the following types and formats can be evaluated into a boolean:<br>
	 * &nbsp; &#8226; &nbsp; a boolean;<br>
	 * &nbsp; &#8226; &nbsp; an integer, as: <code>0</code> for boolean <code>false</code>, 
	 * and <code>1</code> for boolean <code>true</code>;<br>
	 * &nbsp; &#8226; &nbsp; a float, as: <code>0.0</code> for boolean <code>false</code>, 
	 * and <code>1.0</code> for boolean <code>true</code>;<br>
	 * &nbsp; &#8226; &nbsp; a string, as: <code>"0"</code>, <code>"f"</code>, <code>"false"</code>, 
	 * <code>"off"</code> or <code>"no"</code> for boolean <code>false</code>, 
	 * and <code>"1"</code>, <code>"t"</code>, <code>"true"</code>, 
	 * <code>"on"</code> or <code>"yes"</code> for boolean <code>true</code>.
	 * 
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsBoolean(bool $nullable = false): object
	{
		$this->getEvaluatorsManager()->setAsBoolean($nullable);
		return $this;
	}
	
	/**
	 * Set to only allow a value strictly evaluated as a boolean.
	 * 
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsStrictBoolean(bool $nullable = false): object
	{
		$this->getEvaluatorsManager()->setAsStrictBoolean($nullable);
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
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsNumber(bool $nullable = false): object
	{
		$this->getEvaluatorsManager()->setAsNumber($nullable);
		return $this;
	}
	
	/**
	 * Set to only allow a value strictly evaluated as a number.
	 * 
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsStrictNumber(bool $nullable = false): object
	{
		$this->getEvaluatorsManager()->setAsStrictNumber($nullable);
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
	final public function setAsInteger(bool $unsigned = false, ?int $bits = null, bool $nullable = false): object
	{
		$this->getEvaluatorsManager()->setAsInteger($unsigned, $bits, $nullable);
		return $this;
	}
	
	/**
	 * Set to only allow a value strictly evaluated as an integer.
	 * 
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
	): object
	{
		$this->getEvaluatorsManager()->setAsStrictInteger($unsigned, $bits, $nullable);
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
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsFloat(bool $nullable = false): object
	{
		$this->getEvaluatorsManager()->setAsFloat($nullable);
		return $this;
	}
	
	/**
	 * Set to only allow a value strictly evaluated as a float.
	 * 
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsStrictFloat(bool $nullable = false): object
	{
		$this->getEvaluatorsManager()->setAsStrictFloat($nullable);
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
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsSize(bool $nullable = false): object
	{
		$this->getEvaluatorsManager()->setAsSize($nullable);
		return $this;
	}
	
	/**
	 * Set to only allow a value evaluated as a string.
	 * 
	 * Only the following types and formats can be evaluated into a string:<br>
	 * &nbsp; &#8226; &nbsp; a string, integer or float;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>__toString</code> method;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Stringifiable</code> interface.
	 * 
	 * @see https://php.net/manual/en/language.oop5.magic.php#object.tostring
	 * @see \Dracodeum\Kit\Interfaces\Stringifiable
	 * @param bool $non_empty [default = false]
	 * <p>Do not allow an empty string value.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsString(bool $non_empty = false, bool $nullable = false): object
	{
		$this->getEvaluatorsManager()->setAsString($non_empty, $nullable);
		return $this;
	}
	
	/**
	 * Set to only allow a value strictly evaluated as a string.
	 * 
	 * @param bool $non_empty [default = false]
	 * <p>Do not allow an empty string value.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsStrictString(bool $non_empty = false, bool $nullable = false): object
	{
		$this->getEvaluatorsManager()->setAsStrictString($non_empty, $nullable);
		return $this;
	}
	
	/**
	 * Set to only allow a value evaluated as a class.
	 * 
	 * Only a class string or object can be evaluated into a class.
	 * 
	 * @param object|string|null $object_class_interface [default = null]
	 * <p>The object or class which a value must be or extend from or the interface which a value must implement.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsClass($object_class_interface = null, bool $nullable = false): object
	{
		$this->getEvaluatorsManager()->setAsClass($object_class_interface, $nullable);
		return $this;
	}
	
	/**
	 * Set to only allow a value strictly evaluated as a class.
	 * 
	 * @param object|string|null $object_class_interface [default = null]
	 * <p>The object or class which a value must be or extend from or the interface which a value must implement.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsStrictClass($object_class_interface = null, bool $nullable = false): object
	{
		$this->getEvaluatorsManager()->setAsStrictClass($object_class_interface, $nullable);
		return $this;
	}
	
	/**
	 * Set to only allow a value evaluated as an object.
	 * 
	 * Only the following types and formats can be evaluated into an object:<br>
	 * &nbsp; &#8226; &nbsp; a class string or object;<br>
	 * &nbsp; &#8226; &nbsp; an array with an <var>$object_class_interface</var> implementing 
	 * the <code>Dracodeum\Kit\Interfaces\ArrayInstantiable</code> interface;<br>
	 * &nbsp; &#8226; &nbsp; a string with an <var>$object_class_interface</var> implementing 
	 * the <code>Dracodeum\Kit\Interfaces\StringInstantiable</code> interface.
	 * 
	 * @see \Dracodeum\Kit\Interfaces\ArrayInstantiable
	 * @see \Dracodeum\Kit\Interfaces\StringInstantiable
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
	): object
	{
		$this->getEvaluatorsManager()->setAsObject($object_class_interface, $arguments, $nullable);
		return $this;
	}
	
	/**
	 * Set to only allow a value strictly evaluated as an object.
	 * 
	 * @param object|string|null $object_class_interface [default = null]
	 * <p>The object or class which a value must be or extend from or the interface which a value must implement.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsStrictObject($object_class_interface = null, bool $nullable = false): object
	{
		$this->getEvaluatorsManager()->setAsStrictObject($object_class_interface, $nullable);
		return $this;
	}
	
	/**
	 * Set to only allow a value evaluated as a class or object.
	 * 
	 * Only the following types and formats can be evaluated into an object or class:<br>
	 * &nbsp; &#8226; &nbsp; a class string or object;<br>
	 * &nbsp; &#8226; &nbsp; an array with an <var>$object_class_interface</var> implementing 
	 * the <code>Dracodeum\Kit\Interfaces\ArrayInstantiable</code> interface;<br>
	 * &nbsp; &#8226; &nbsp; a string with an <var>$object_class_interface</var> implementing 
	 * the <code>Dracodeum\Kit\Interfaces\StringInstantiable</code> interface.
	 * 
	 * @see \Dracodeum\Kit\Interfaces\ArrayInstantiable
	 * @see \Dracodeum\Kit\Interfaces\StringInstantiable
	 * @param object|string|null $object_class_interface [default = null]
	 * <p>The object or class which a value must be or extend from or the interface which a value must implement.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsObjectClass($object_class_interface = null, bool $nullable = false): object
	{
		$this->getEvaluatorsManager()->setAsObjectClass($object_class_interface, $nullable);
		return $this;
	}
	
	/**
	 * Set to only allow a value evaluated as a callable.
	 * 
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
	): object
	{
		$this->getEvaluatorsManager()->setAsCallable($template, $nullable, $assertive);
		return $this;
	}
	
	/**
	 * Set to only allow a value evaluated as a closure.
	 * 
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
	): object
	{
		$this->getEvaluatorsManager()->setAsClosure($template, $nullable, $assertive);
		return $this;
	}
	
	/**
	 * Set to only allow a value evaluated as an array.
	 * 
	 * Only the following types and formats can be evaluated into an array:<br>
	 * &nbsp; &#8226; &nbsp; an array;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Arrayable</code> interface.
	 * 
	 * @see \Dracodeum\Kit\Interfaces\Arrayable
	 * @param callable|null $evaluator [default = null]
	 * <p>The evaluator function to use for each element in the resulting array.<br>
	 * It is expected to be compatible with the following signature:<br>
	 * <br>
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
	 * @param bool $recursive [default = false]
	 * <p>Evaluate all possible referenced subobjects into arrays recursively.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsArray(
		?callable $evaluator = null, bool $non_associative = false, bool $non_empty = false, bool $recursive = false,
		bool $nullable = false
	): object
	{
		$this->getEvaluatorsManager()->setAsArray($evaluator, $non_associative, $non_empty, $recursive, $nullable);
		return $this;
	}
	
	/**
	 * Set to only allow a value strictly evaluated as an array.
	 * 
	 * @param callable|null $evaluator [default = null]
	 * <p>The evaluator function to use for each element in the resulting array.<br>
	 * It is expected to be compatible with the following signature:<br>
	 * <br>
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
	): object
	{
		$this->getEvaluatorsManager()->setAsStrictArray($evaluator, $non_associative, $non_empty, $nullable);
		return $this;
	}
	
	/**
	 * Set to only allow a value evaluated as an enumeration value.
	 * 
	 * Only an enumeration element given as an integer, float or string can be evaluated into an enumeration value.
	 * 
	 * @param string $enumeration
	 * <p>The enumeration class to use.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsEnumerationValue(string $enumeration, bool $nullable = false): object
	{
		$this->getEvaluatorsManager()->setAsEnumerationValue($enumeration, $nullable);
		return $this;
	}
	
	/**
	 * Set to only allow a value strictly evaluated as an enumeration value.
	 * 
	 * @param string $enumeration
	 * <p>The enumeration class to use.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsStrictEnumerationValue(string $enumeration, bool $nullable = false): object
	{
		$this->getEvaluatorsManager()->setAsStrictEnumerationValue($enumeration, $nullable);
		return $this;
	}
	
	/**
	 * Set to only allow a value evaluated as an enumeration name.
	 * 
	 * Only an enumeration element given as an integer, float or string can be evaluated into an enumeration name.
	 * 
	 * @param string $enumeration
	 * <p>The enumeration class to use.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsEnumerationName(string $enumeration, bool $nullable = false): object
	{
		$this->getEvaluatorsManager()->setAsEnumerationName($enumeration, $nullable);
		return $this;
	}
	
	/**
	 * Set to only allow a value strictly evaluated as an enumeration name.
	 * 
	 * @param string $enumeration
	 * <p>The enumeration class to use.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsStrictEnumerationName(string $enumeration, bool $nullable = false): object
	{
		$this->getEvaluatorsManager()->setAsStrictEnumerationName($enumeration, $nullable);
		return $this;
	}
	
	/**
	 * Set to only allow a value evaluated as a hash.
	 * 
	 * Only the following types and formats can be evaluated into a hash:<br>
	 * &nbsp; &#8226; &nbsp; a hexadecimal notation string;<br>
	 * &nbsp; &#8226; &nbsp; a colon-hexadecimal notation string, as octets or hextets;<br>
	 * &nbsp; &#8226; &nbsp; a Base64 or an URL-safe Base64 encoded string;<br>
	 * &nbsp; &#8226; &nbsp; a raw binary string.
	 * 
	 * @param int|null $bits [default = null]
	 * <p>The number of bits to evaluate with.<br>
	 * If set, then it must be a multiple of <code>8</code> and be greater than <code>0</code>.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsHash(?int $bits = null, bool $nullable = false): object
	{
		$this->getEvaluatorsManager()->setAsHash($bits, $nullable);
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
	 * @see https://php.net/manual/en/function.strtotime.php
	 * @see https://php.net/manual/en/function.date.php
	 * @see https://php.net/manual/en/function.date-default-timezone-set.php
	 * @see https://php.net/manual/en/class.datetime.php
	 * @see https://php.net/manual/en/class.datetimeimmutable.php
	 * @see https://php.net/manual/en/class.datetimeinterface.php
	 * @param string|null $format [default = null]
	 * <p>The format to evaluate into, as supported by the PHP <code>date</code> function, 
	 * or as a <code>DateTime</code> or <code>DateTimeImmutable</code> class to instantiate.<br>
	 * If not set, then the given value is evaluated into an integer or float as an Unix timestamp.</p>
	 * @param bool $microseconds [default = false]
	 * <p>Evaluate the given value with microseconds.</p>
	 * @param string|null $timezone [default = null]
	 * <p>The timezone to evaluate into, as supported by the PHP <code>date_default_timezone_set</code> function.<br>
	 * If not set, then the currently set default timezone is used.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsDateTime(
		?string $format = null, bool $microseconds = false, ?string $timezone = null, bool $nullable = false
	): object
	{
		$this->getEvaluatorsManager()->setAsDateTime($format, $microseconds, $timezone, $nullable);
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
	 * @see https://php.net/manual/en/function.strtotime.php
	 * @see https://php.net/manual/en/function.date.php
	 * @see https://php.net/manual/en/class.datetime.php
	 * @see https://php.net/manual/en/class.datetimeimmutable.php
	 * @see https://php.net/manual/en/class.datetimeinterface.php
	 * @param string|null $format [default = null]
	 * <p>The format to evaluate into, as supported by the PHP <code>date</code> function, 
	 * or as a <code>DateTime</code> or <code>DateTimeImmutable</code> class to instantiate.<br>
	 * If not set, then the given value is evaluated into an integer as an Unix timestamp.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsDate(?string $format = null, bool $nullable = false): object
	{
		$this->getEvaluatorsManager()->setAsDate($format, $nullable);
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
	 * @see https://php.net/manual/en/function.strtotime.php
	 * @see https://php.net/manual/en/function.date.php
	 * @see https://php.net/manual/en/function.date-default-timezone-set.php
	 * @see https://php.net/manual/en/class.datetime.php
	 * @see https://php.net/manual/en/class.datetimeimmutable.php
	 * @see https://php.net/manual/en/class.datetimeinterface.php
	 * @param string|null $format [default = null]
	 * <p>The format to evaluate into, as supported by the PHP <code>date</code> function, 
	 * or as a <code>DateTime</code> or <code>DateTimeImmutable</code> class to instantiate.<br>
	 * If not set, then the given value is evaluated into an integer or float as an Unix timestamp.</p>
	 * @param bool $microseconds [default = false]
	 * <p>Evaluate the given value with microseconds.</p>
	 * @param string|null $timezone [default = null]
	 * <p>The timezone to evaluate into, as supported by the PHP <code>date_default_timezone_set</code> function.<br>
	 * If not set, then the currently set default timezone is used.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsTime(
		?string $format = null, bool $microseconds = false, ?string $timezone = null, bool $nullable = false
	): object
	{
		$this->getEvaluatorsManager()->setAsTime($format, $microseconds, $timezone, $nullable);
		return $this;
	}
	
	/**
	 * Set to only allow a value evaluated as a component instance.
	 * 
	 * Only a component instance or name, or a prototype instance, class or name, can be evaluated into an instance.
	 * 
	 * @param string $class
	 * <p>The class to use.</p>
	 * @param array $properties [default = []]
	 * <p>The properties to evaluate with, as <samp>name => value</samp> pairs, 
	 * if a component name, or a prototype class or name, or <code>null</code>, is given.<br>
	 * Required properties may also be given as an array of values (<samp>[value1, value2, ...]</samp>), 
	 * in the same order as how these properties were first declared.</p>
	 * @param callable|null $builder [default = null]
	 * <p>The function to use to build an instance with a given prototype and set of properties.<br>
	 * It is expected to be compatible with the following signature:<br>
	 * <br>
	 * <code>function ($prototype, array $properties): Dracodeum\Kit\Component</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>Dracodeum\Kit\Prototype|string|null $prototype</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The prototype instance, class or name to build with.<br>
	 * &nbsp; &nbsp; &nbsp; If not set, then the default prototype instance or the base prototype class is used.<br>
	 * &nbsp; &#8226; &nbsp; <code><b>array $properties</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The properties to build with, as <samp>name => value</samp> pairs, 
	 * if a prototype class or name, or <code>null</code>, is given.<br>
	 * &nbsp; &nbsp; &nbsp; Required properties may also be given as an array of values 
	 * (<samp>[value1, value2, ...]</samp>), in the same order as how these properties were first declared.<br>
	 * <br>
	 * Return: <code><b>Dracodeum\Kit\Component</b></code><br>
	 * The built instance with the given prototype and set of properties.</p>
	 * @param callable|null $named_builder [default = null]
	 * <p>The function to use to build an instance for a given name with a given set of properties.<br>
	 * It is expected to be compatible with the following signature:<br>
	 * <br>
	 * <code>function (string $name, array $properties): ?Dracodeum\Kit\Component</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>string $name</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The name to build for.<br>
	 * &nbsp; &#8226; &nbsp; <code><b>array $properties</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The properties to build with, as <samp>name => value</samp> pairs.<br>
	 * &nbsp; &nbsp; &nbsp; Required properties may also be given as an array of values 
	 * (<samp>[value1, value2, ...]</samp>), in the same order as how these properties were first declared.<br>
	 * <br>
	 * Return: <code><b>Dracodeum\Kit\Component|null</b></code><br>
	 * The built instance for the given name with the given set of properties 
	 * or <code>null</code> if none was built.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsComponent(
		string $class, array $properties = [], ?callable $builder = null, ?callable $named_builder = null,
		bool $nullable = false
	): object
	{
		$this->getEvaluatorsManager()->setAsComponent($class, $properties, $builder, $named_builder, $nullable);
		return $this;
	}
	
	/**
	 * Set to only allow a value evaluated as an entity instance.
	 * 
	 * Only the following types and formats can be evaluated into an entity instance:<br>
	 * &nbsp; &#8226; &nbsp; <code>null</code> or an instance;<br>
	 * &nbsp; &#8226; &nbsp; an integer, float or string, given as an ID to be loaded from;<br>
	 * &nbsp; &#8226; &nbsp; an array of properties, given as <samp>name => value</samp> pairs;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Arrayable</code> interface;<br>
	 * &nbsp; &#8226; &nbsp; an instance of <code>Dracodeum\Kit\Components\Store\Structures\Uid</code>, 
	 * to be loaded from;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>__toString</code> method, 
	 * given as an ID to be loaded from;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Stringifiable</code> interface, 
	 * given as an ID to be loaded from.
	 * 
	 * @see \Dracodeum\Kit\Interfaces\Arrayable
	 * @see \Dracodeum\Kit\Components\Store\Structures\Uid
	 * @param string $class
	 * <p>The class to use.</p>
	 * @param bool $persisted [default = false]
	 * <p>Set as having already been persisted at least once, if an array of properties, an object implementing the 
	 * <code>Dracodeum\Kit\Interfaces\Arrayable</code> interface or <code>null</code> is given.</p>
	 * @param callable|null $builder [default = null]
	 * <p>The function to use to build an instance with a given set of properties.<br>
	 * It is expected to be compatible with the following signature:<br>
	 * <br>
	 * <code>function (array $properties, bool $persisted): Dracodeum\Kit\Entity</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>array $properties</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The properties to build with, as <samp>name => value</samp> pairs.<br>
	 * &nbsp; &nbsp; &nbsp; Required properties may also be given as an array of values 
	 * (<samp>[value1, value2, ...]</samp>), in the same order as how these properties were first declared.<br>
	 * &nbsp; &#8226; &nbsp; <code><b>bool $persisted</b></code><br>
	 * &nbsp; &nbsp; &nbsp; Set as having already been persisted at least once.<br>
	 * <br>
	 * Return: <code><b>Dracodeum\Kit\Entity</b></code><br>
	 * The built instance with the given set of properties.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsEntity(
		string $class, bool $persisted = false, ?callable $builder = null, bool $nullable = false
	): object
	{
		$this->getEvaluatorsManager()->setAsEntity($class, $persisted, $builder, $nullable);
		return $this;
	}
	
	/**
	 * Set to only allow a value evaluated as an options instance.
	 * 
	 * Only the following types and formats can be evaluated into an options instance:<br>
	 * &nbsp; &#8226; &nbsp; <code>null</code>, an integer, a float, a string or an instance;<br>
	 * &nbsp; &#8226; &nbsp; an array of properties, given as <samp>name => value</samp> pairs;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Arrayable</code> interface.
	 * 
	 * @see \Dracodeum\Kit\Interfaces\Arrayable
	 * @param string $class
	 * <p>The class to use.</p>
	 * @param bool|null $clone_recursive [default = null]
	 * <p>Clone the given value recursively.<br>
	 * If set to boolean <code>false</code> and an instance is given, then clone it into a new one with the same 
	 * properties, but not recursively.<br>
	 * If not set, then the given value is not cloned.</p>
	 * @param callable|null $builder [default = null]
	 * <p>The function to use to build an instance with a given set of properties.<br>
	 * It is expected to be compatible with the following signature:<br>
	 * <br>
	 * <code>function (array $properties): Dracodeum\Kit\Options</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>array $properties</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The properties to build with, as <samp>name => value</samp> pairs.<br>
	 * <br>
	 * Return: <code><b>Dracodeum\Kit\Options</b></code><br>
	 * The built instance with the given set of properties.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsOptions(
		string $class, ?bool $clone_recursive = null, ?callable $builder = null, bool $nullable = false
	): object
	{
		$this->getEvaluatorsManager()->setAsOptions($class, $clone_recursive, $builder, $nullable);
		return $this;
	}
	
	/**
	 * Set to only allow a value evaluated as a structure instance.
	 * 
	 * Only the following types and formats can be evaluated into a structure instance:<br>
	 * &nbsp; &#8226; &nbsp; <code>null</code>, an integer, a float, a string or an instance;<br>
	 * &nbsp; &#8226; &nbsp; an array of properties, given as <samp>name => value</samp> pairs;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Arrayable</code> interface.
	 * 
	 * @see \Dracodeum\Kit\Interfaces\Arrayable
	 * @param string $class
	 * <p>The class to use.</p>
	 * @param bool|null $clone_recursive [default = null]
	 * <p>Clone the given value recursively.<br>
	 * If set to boolean <code>false</code> and an instance is given, then clone it into a new one with the same 
	 * properties, but not recursively.<br>
	 * If not set, then the given value is not cloned.</p>
	 * @param callable|null $builder [default = null]
	 * <p>The function to use to build an instance with a given set of properties.<br>
	 * It is expected to be compatible with the following signature:<br>
	 * <br>
	 * <code>function (array $properties): Dracodeum\Kit\Structure</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>array $properties</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The properties to build with, as <samp>name => value</samp> pairs.<br>
	 * &nbsp; &nbsp; &nbsp; Required properties may also be given as an array of values 
	 * (<samp>[value1, value2, ...]</samp>), in the same order as how these properties were first declared.<br>
	 * <br>
	 * Return: <code><b>Dracodeum\Kit\Structure</b></code><br>
	 * The built instance with the given set of properties.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsStructure(
		string $class, ?bool $clone_recursive = null, ?callable $builder = null, bool $nullable = false
	): object
	{
		$this->getEvaluatorsManager()->setAsStructure($class, $clone_recursive, $builder, $nullable);
		return $this;
	}
	
	/**
	 * Set to only allow a value evaluated as a dictionary instance.
	 * 
	 * Only the following types and formats can be evaluated into a dictionary instance:<br>
	 * &nbsp; &#8226; &nbsp; an instance;<br>
	 * &nbsp; &#8226; &nbsp; an associative array;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Arrayable</code> interface.
	 * 
	 * @see \Dracodeum\Kit\Interfaces\Arrayable
	 * @param \Dracodeum\Kit\Primitives\Dictionary|null $template [default = null]
	 * <p>The template instance to clone from and evaluate into.</p>
	 * @param bool|null $clone_recursive [default = null]
	 * <p>Clone the given value recursively.<br>
	 * If set to boolean <code>false</code> and an instance is given, then clone it into a new one with the same pairs 
	 * and evaluator functions, but not recursively.<br>
	 * If not set, then the given value is not cloned.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsDictionary(
		?Dictionary $template = null, ?bool $clone_recursive = null, bool $nullable = false
	): object
	{
		$this->getEvaluatorsManager()->setAsDictionary($template, $clone_recursive, $nullable);
		return $this;
	}
	
	/**
	 * Set to only allow a value evaluated as a vector instance.
	 * 
	 * Only the following types and formats can be evaluated into a vector instance:<br>
	 * &nbsp; &#8226; &nbsp; an instance;<br>
	 * &nbsp; &#8226; &nbsp; a non-associative array;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Arrayable</code> interface.
	 * 
	 * @see \Dracodeum\Kit\Interfaces\Arrayable
	 * @param \Dracodeum\Kit\Primitives\Vector|null $template [default = null]
	 * <p>The template instance to clone from and evaluate into.</p>
	 * @param bool|null $clone_recursive [default = null]
	 * <p>Clone the given value recursively.<br>
	 * If set to boolean <code>false</code> and an instance is given, then clone it into a new one with the same values 
	 * and evaluator functions, but not recursively.<br>
	 * If not set, then the given value is not cloned.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a value to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsVector(
		?Vector $template = null, ?bool $clone_recursive = null, bool $nullable = false
	): object
	{
		$this->getEvaluatorsManager()->setAsVector($template, $clone_recursive, $nullable);
		return $this;
	}
	
	
	
	//Final protected methods
	/**
	 * Get evaluators manager instance.
	 * 
	 * @return \Dracodeum\Kit\Managers\Evaluators
	 * <p>The evaluators manager instance.</p>
	 */
	final protected function getEvaluatorsManager(): Manager
	{
		if (!isset($this->evaluators_manager)) {
			$this->evaluators_manager = new Manager($this);
		}
		return $this->evaluators_manager;
	}
	
	/**
	 * Process a given evaluators debug info instance.
	 * 
	 * @param \Dracodeum\Kit\Traits\DebugInfo\Info $info
	 * <p>The debug info instance to process.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final protected function processEvaluatorsDebugInfo(DebugInfo $info): object
	{
		$info->hideObjectProperty('evaluators_manager', self::class);
		return $this;
	}
	
	/**
	 * Process evaluators cloning.
	 * 
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final protected function processEvaluatorsCloning(): object
	{
		if (isset($this->evaluators_manager)) {
			$this->evaluators_manager = $this->evaluators_manager->cloneForOwner($this);
		}
		return $this;
	}
}
