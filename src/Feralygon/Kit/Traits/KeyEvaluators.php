<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Traits;

use Feralygon\Kit\Managers\Evaluators as Manager;
use Feralygon\Kit\Primitives\{
	Dictionary,
	Vector
};
use Feralygon\Kit\Traits\DebugInfo\Info as DebugInfo;

/** This trait enables the support for key evaluators in a class, and adds some common ones. */
trait KeyEvaluators
{
	//Private properties
	/** @var \Feralygon\Kit\Managers\Evaluators|null */
	private $key_evaluators_manager = null;
	
	
	
	//Final public methods
	/**
	 * Add key evaluator function.
	 * 
	 * @param callable $evaluator
	 * <p>The key evaluator function to add.<br>
	 * It is expected to be compatible with the following signature:<br>
	 * <br>
	 * <code>function (&$key): bool</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>mixed $key</b> [reference]</code><br>
	 * &nbsp; &nbsp; &nbsp; The key to evaluate (validate and sanitize).<br>
	 * <br>
	 * Return: <code><b>bool</b></code><br>
	 * Boolean <code>true</code> if the given key is successfully evaluated.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function addKeyEvaluator(callable $evaluator): object
	{
		$this->getKeyEvaluatorsManager()->add($evaluator);
		return $this;
	}
	
	/**
	 * Set key evaluator function.
	 * 
	 * @param callable $evaluator
	 * <p>The key evaluator function to set.<br>
	 * It is expected to be compatible with the following signature:<br>
	 * <br>
	 * <code>function (&$key): bool</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>mixed $key</b> [reference]</code><br>
	 * &nbsp; &nbsp; &nbsp; The key to evaluate (validate and sanitize).<br>
	 * <br>
	 * Return: <code><b>bool</b></code><br>
	 * Boolean <code>true</code> if the given key is successfully evaluated.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setKeyEvaluator(callable $evaluator): object
	{
		$this->getKeyEvaluatorsManager()->set($evaluator);
		return $this;
	}
	
	/**
	 * Get key evaluator functions.
	 * 
	 * @return \Closure[]
	 * <p>The key evaluator functions.</p>
	 */
	final public function getKeyEvaluators(): array
	{
		return $this->getKeyEvaluatorsManager()->getAll();
	}
	
	/**
	 * Clear all key evaluator functions.
	 * 
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function clearKeyEvaluators(): object
	{
		$this->getKeyEvaluatorsManager()->clear();
		return $this;
	}
	
	/**
	 * Lock key evaluators.
	 * 
	 * @param \Feralygon\Kit\Utilities\Call\Options\Guard|array|callable|null $guard_options [default = null]
	 * <p>The guard options to set, as an instance, <samp>name => value</samp> pairs or a function compatible 
	 * with the following signature:<br>
	 * <br>
	 * <code>function ()</code><br>
	 * <br>
	 * Return: <code><b>\Feralygon\Kit\Utilities\Call\Options\Guard|array</b></code><br>
	 * The options, as an instance or <samp>name => value</samp> pairs.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function lockKeyEvaluators($guard_options = null): object
	{
		$this->getKeyEvaluatorsManager()->lock();
		return $this;
	}
	
	/**
	 * Set to only allow a key evaluated as a boolean.
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
	 * @param bool $nullable [default = false]
	 * <p>Allow a key to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setKeyAsBoolean(bool $nullable = false): object
	{
		$this->getKeyEvaluatorsManager()->setAsBoolean($nullable);
		return $this;
	}
	
	/**
	 * Set to only allow a key strictly evaluated as a boolean.
	 * 
	 * @param bool $nullable [default = false]
	 * <p>Allow a key to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setKeyAsStrictBoolean(bool $nullable = false): object
	{
		$this->getKeyEvaluatorsManager()->setAsStrictBoolean($nullable);
		return $this;
	}
	
	/**
	 * Set to only allow a key evaluated as a number.
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
	 * <p>Allow a key to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setKeyAsNumber(bool $nullable = false): object
	{
		$this->getKeyEvaluatorsManager()->setAsNumber($nullable);
		return $this;
	}
	
	/**
	 * Set to only allow a key strictly evaluated as a number.
	 * 
	 * @param bool $nullable [default = false]
	 * <p>Allow a key to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setKeyAsStrictNumber(bool $nullable = false): object
	{
		$this->getKeyEvaluatorsManager()->setAsStrictNumber($nullable);
		return $this;
	}
	
	/**
	 * Set to only allow a key evaluated as an integer.
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
	 * <p>Allow a key to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setKeyAsInteger(bool $unsigned = false, ?int $bits = null, bool $nullable = false): object
	{
		$this->getKeyEvaluatorsManager()->setAsInteger($unsigned, $bits, $nullable);
		return $this;
	}
	
	/**
	 * Set to only allow a key strictly evaluated as an integer.
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
	 * <p>Allow a key to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setKeyAsStrictInteger(
		bool $unsigned = false, ?int $bits = null, bool $nullable = false
	): object
	{
		$this->getKeyEvaluatorsManager()->setAsStrictInteger($unsigned, $bits, $nullable);
		return $this;
	}
	
	/**
	 * Set to only allow a key evaluated as a float.
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
	 * <p>Allow a key to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setKeyAsFloat(bool $nullable = false): object
	{
		$this->getKeyEvaluatorsManager()->setAsFloat($nullable);
		return $this;
	}
	
	/**
	 * Set to only allow a key strictly evaluated as a float.
	 * 
	 * @param bool $nullable [default = false]
	 * <p>Allow a key to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setKeyAsStrictFloat(bool $nullable = false): object
	{
		$this->getKeyEvaluatorsManager()->setAsStrictFloat($nullable);
		return $this;
	}
	
	/**
	 * Set to only allow a key evaluated as a size in bytes.
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
	 * <p>Allow a key to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setKeyAsSize(bool $nullable = false): object
	{
		$this->getKeyEvaluatorsManager()->setAsSize($nullable);
		return $this;
	}
	
	/**
	 * Set to only allow a key evaluated as a string.
	 * 
	 * Only the following types and formats can be evaluated into a string:<br>
	 * &nbsp; &#8226; &nbsp; a string, integer or float;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>__toString</code> method;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Feralygon\Kit\Interfaces\Stringifiable</code> interface.
	 * 
	 * @see https://php.net/manual/en/language.oop5.magic.php#object.tostring
	 * @see \Feralygon\Kit\Interfaces\Stringifiable
	 * @param bool $non_empty [default = false]
	 * <p>Do not allow an empty string key.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a key to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setKeyAsString(bool $non_empty = false, bool $nullable = false): object
	{
		$this->getKeyEvaluatorsManager()->setAsString($non_empty, $nullable);
		return $this;
	}
	
	/**
	 * Set to only allow a key strictly evaluated as a string.
	 * 
	 * @param bool $non_empty [default = false]
	 * <p>Do not allow an empty string key.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a key to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setKeyAsStrictString(bool $non_empty = false, bool $nullable = false): object
	{
		$this->getKeyEvaluatorsManager()->setAsStrictString($non_empty, $nullable);
		return $this;
	}
	
	/**
	 * Set to only allow a key evaluated as a class.
	 * 
	 * Only a class string or object can be evaluated into a class.
	 * 
	 * @param object|string|null $object_class_interface [default = null]
	 * <p>The object or class which a key must be or extend from or the interface which a key must implement.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a key to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setKeyAsClass($object_class_interface = null, bool $nullable = false): object
	{
		$this->getKeyEvaluatorsManager()->setAsClass($object_class_interface, $nullable);
		return $this;
	}
	
	/**
	 * Set to only allow a key strictly evaluated as a class.
	 * 
	 * @param object|string|null $object_class_interface [default = null]
	 * <p>The object or class which a key must be or extend from or the interface which a key must implement.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a key to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setKeyAsStrictClass($object_class_interface = null, bool $nullable = false): object
	{
		$this->getKeyEvaluatorsManager()->setAsStrictClass($object_class_interface, $nullable);
		return $this;
	}
	
	/**
	 * Set to only allow a key evaluated as an object.
	 * 
	 * Only the following types and formats can be evaluated into an object:<br>
	 * &nbsp; &#8226; &nbsp; a class string or object;<br>
	 * &nbsp; &#8226; &nbsp; an array with an <var>$object_class_interface</var> implementing 
	 * the <code>Feralygon\Kit\Interfaces\ArrayInstantiable</code> interface;<br>
	 * &nbsp; &#8226; &nbsp; a string with an <var>$object_class_interface</var> implementing 
	 * the <code>Feralygon\Kit\Interfaces\StringInstantiable</code> interface.
	 * 
	 * @see \Feralygon\Kit\Interfaces\ArrayInstantiable
	 * @see \Feralygon\Kit\Interfaces\StringInstantiable
	 * @param object|string|null $object_class_interface [default = null]
	 * <p>The object or class which a key must be or extend from or the interface which a key must implement.</p>
	 * @param array $arguments [default = []]
	 * <p>The class constructor arguments to instantiate with.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a key to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setKeyAsObject(
		$object_class_interface = null, array $arguments = [], bool $nullable = false
	): object
	{
		$this->getKeyEvaluatorsManager()->setAsObject($object_class_interface, $arguments, $nullable);
		return $this;
	}
	
	/**
	 * Set to only allow a key strictly evaluated as an object.
	 * 
	 * @param object|string|null $object_class_interface [default = null]
	 * <p>The object or class which a key must be or extend from or the interface which a key must implement.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a key to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setKeyAsStrictObject($object_class_interface = null, bool $nullable = false): object
	{
		$this->getKeyEvaluatorsManager()->setAsStrictObject($object_class_interface, $nullable);
		return $this;
	}
	
	/**
	 * Set to only allow a key evaluated as a class or object.
	 * 
	 * Only the following types and formats can be evaluated into an object or class:<br>
	 * &nbsp; &#8226; &nbsp; a class string or object;<br>
	 * &nbsp; &#8226; &nbsp; an array with an <var>$object_class_interface</var> implementing 
	 * the <code>Feralygon\Kit\Interfaces\ArrayInstantiable</code> interface;<br>
	 * &nbsp; &#8226; &nbsp; a string with an <var>$object_class_interface</var> implementing 
	 * the <code>Feralygon\Kit\Interfaces\StringInstantiable</code> interface.
	 * 
	 * @see \Feralygon\Kit\Interfaces\ArrayInstantiable
	 * @see \Feralygon\Kit\Interfaces\StringInstantiable
	 * @param object|string|null $object_class_interface [default = null]
	 * <p>The object or class which a key must be or extend from or the interface which a key must implement.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a key to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setKeyAsObjectClass($object_class_interface = null, bool $nullable = false): object
	{
		$this->getKeyEvaluatorsManager()->setAsObjectClass($object_class_interface, $nullable);
		return $this;
	}
	
	/**
	 * Set to only allow a key evaluated as a callable.
	 * 
	 * @param callable|null $template [default = null]
	 * <p>The template callable declaration to validate the compatibility against.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a key to evaluate as <code>null</code>.</p>
	 * @param bool $assertive [default = false]
	 * <p>Evaluate in an assertive manner, in other words, perform the heavier validations, 
	 * such as the template compatibility one, only when in a debug environment.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setKeyAsCallable(
		?callable $template = null, bool $nullable = false, bool $assertive = false
	): object
	{
		$this->getKeyEvaluatorsManager()->setAsCallable($template, $nullable, $assertive);
		return $this;
	}
	
	/**
	 * Set to only allow a key evaluated as a closure.
	 * 
	 * @param callable|null $template [default = null]
	 * <p>The template callable declaration to validate the compatibility against.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a key to evaluate as <code>null</code>.</p>
	 * @param bool $assertive [default = false]
	 * <p>Evaluate in an assertive manner, in other words, perform the heavier validations, 
	 * such as the template compatibility one, only when in a debug environment.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setKeyAsClosure(
		?callable $template = null, bool $nullable = false, bool $assertive = false
	): object
	{
		$this->getKeyEvaluatorsManager()->setAsClosure($template, $nullable, $assertive);
		return $this;
	}
	
	/**
	 * Set to only allow a key evaluated as an array.
	 * 
	 * Only the following types and formats can be evaluated into an array:<br>
	 * &nbsp; &#8226; &nbsp; an array;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Feralygon\Kit\Interfaces\Arrayable</code> interface.
	 * 
	 * @see \Feralygon\Kit\Interfaces\Arrayable
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
	 * <p>Allow a key to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setKeyAsArray(
		?callable $evaluator = null, bool $non_associative = false, bool $non_empty = false, bool $recursive = false,
		bool $nullable = false
	): object
	{
		$this->getKeyEvaluatorsManager()->setAsArray($evaluator, $non_associative, $non_empty, $recursive, $nullable);
		return $this;
	}
	
	/**
	 * Set to only allow a key strictly evaluated as an array.
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
	 * <p>Allow a key to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setKeyAsStrictArray(
		?callable $evaluator = null, bool $non_associative = false, bool $non_empty = false, bool $nullable = false
	): object
	{
		$this->getKeyEvaluatorsManager()->setAsStrictArray($evaluator, $non_associative, $non_empty, $nullable);
		return $this;
	}
	
	/**
	 * Set to only allow a key evaluated as an enumeration value.
	 * 
	 * Only an enumeration element given as an integer, float or string can be evaluated into an enumeration value.
	 * 
	 * @param string $enumeration
	 * <p>The enumeration class to use.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a key to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setKeyAsEnumerationValue(string $enumeration, bool $nullable = false): object
	{
		$this->getKeyEvaluatorsManager()->setAsEnumerationValue($enumeration, $nullable);
		return $this;
	}
	
	/**
	 * Set to only allow a key strictly evaluated as an enumeration value.
	 * 
	 * @param string $enumeration
	 * <p>The enumeration class to use.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a key to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setKeyAsStrictEnumerationValue(string $enumeration, bool $nullable = false): object
	{
		$this->getKeyEvaluatorsManager()->setAsStrictEnumerationValue($enumeration, $nullable);
		return $this;
	}
	
	/**
	 * Set to only allow a key evaluated as an enumeration name.
	 * 
	 * Only an enumeration element given as an integer, float or string can be evaluated into an enumeration name.
	 * 
	 * @param string $enumeration
	 * <p>The enumeration class to use.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a key to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setKeyAsEnumerationName(string $enumeration, bool $nullable = false): object
	{
		$this->getKeyEvaluatorsManager()->setAsEnumerationName($enumeration, $nullable);
		return $this;
	}
	
	/**
	 * Set to only allow a key strictly evaluated as an enumeration name.
	 * 
	 * @param string $enumeration
	 * <p>The enumeration class to use.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a key to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setKeyAsStrictEnumerationName(string $enumeration, bool $nullable = false): object
	{
		$this->getKeyEvaluatorsManager()->setAsStrictEnumerationName($enumeration, $nullable);
		return $this;
	}
	
	/**
	 * Set to only allow a key evaluated as a hash.
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
	 * <p>Allow a key to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setKeyAsHash(?int $bits = null, bool $nullable = false): object
	{
		$this->getKeyEvaluatorsManager()->setAsHash($bits, $nullable);
		return $this;
	}
	
	/**
	 * Set to only allow a key evaluated as a date and time.
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
	 * If not set, then the given key is evaluated into an integer as an Unix timestamp.</p>
	 * @param string|null $timezone [default = null]
	 * <p>The timezone to evaluate into, as supported by the PHP <code>date_default_timezone_set</code> function.<br>
	 * If not set, then the currently set default timezone is used.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a key to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setKeyAsDateTime(
		?string $format = null, ?string $timezone = null, bool $nullable = false
	): object
	{
		$this->getKeyEvaluatorsManager()->setAsDateTime($format, $timezone, $nullable);
		return $this;
	}
	
	/**
	 * Set to only allow a key evaluated as a date.
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
	 * If not set, then the given key is evaluated into an integer as an Unix timestamp.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a key to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setKeyAsDate(?string $format = null, bool $nullable = false): object
	{
		$this->getKeyEvaluatorsManager()->setAsDate($format, $nullable);
		return $this;
	}
	
	/**
	 * Set to only allow a key evaluated as a time.
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
	 * If not set, then the given key is evaluated into an integer as an Unix timestamp.</p>
	 * @param string|null $timezone [default = null]
	 * <p>The timezone to evaluate into, as supported by the PHP <code>date_default_timezone_set</code> function.<br>
	 * If not set, then the currently set default timezone is used.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a key to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setKeyAsTime(?string $format = null, ?string $timezone = null, bool $nullable = false): object
	{
		$this->getKeyEvaluatorsManager()->setAsTime($format, $timezone, $nullable);
		return $this;
	}
	
	/**
	 * Set to only allow a key evaluated as a component instance.
	 * 
	 * Only a component instance or name, or a prototype instance, class or name, can be evaluated into an instance.
	 * 
	 * @param string $class
	 * <p>The class to use.</p>
	 * @param array $properties [default = []]
	 * <p>The properties to evaluate with, as <samp>name => value</samp> pairs.<br>
	 * Required properties may also be given as an array of values (<samp>[value1, value2, ...]</samp>), 
	 * in the same order as how these properties were first declared.<br>
	 * <br>
	 * If a component instance is given, then the given properties are ignored.</p>
	 * @param callable|null $builder [default = null]
	 * <p>The function to use to build an instance.<br>
	 * It is expected to be compatible with the following signature:<br>
	 * <br>
	 * <code>function ($prototype, array $properties): Feralygon\Kit\Component</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>Feralygon\Kit\Prototype|string|null $prototype</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The prototype instance, class or name to build with.<br>
	 * &nbsp; &nbsp; &nbsp; If not set, then the default prototype instance or the base prototype class is used.<br>
	 * &nbsp; &#8226; &nbsp; <code><b>array $properties</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The properties to build with, as <samp>name => value</samp> pairs.<br>
	 * &nbsp; &nbsp; &nbsp; Required properties may also be given as an array of values 
	 * (<samp>[value1, value2, ...]</samp>), in the same order as how these properties were first declared.<br>
	 * <br>
	 * Return: <code><b>Feralygon\Kit\Component</b></code><br>
	 * The built instance.</p>
	 * @param callable|null $named_builder [default = null]
	 * <p>The function to use to build an instance for a given name.<br>
	 * It is expected to be compatible with the following signature:<br>
	 * <br>
	 * <code>function (string $name, array $properties): ?Feralygon\Kit\Component</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>string $name</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The name to build for.<br>
	 * &nbsp; &#8226; &nbsp; <code><b>array $properties</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The properties to build with, as <samp>name => value</samp> pairs.<br>
	 * &nbsp; &nbsp; &nbsp; Required properties may also be given as an array of values 
	 * (<samp>[value1, value2, ...]</samp>), in the same order as how these properties were first declared.<br>
	 * <br>
	 * Return: <code><b>Feralygon\Kit\Component|null</b></code><br>
	 * The built instance for the given name or <code>null</code> if none was built.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a key to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setKeyAsComponent(
		string $class, array $properties = [], ?callable $builder = null, ?callable $named_builder = null,
		bool $nullable = false
	): object
	{
		$this->getKeyEvaluatorsManager()->setAsComponent($class, $properties, $builder, $named_builder, $nullable);
		return $this;
	}
	
	/**
	 * Set to only allow a key evaluated as an options instance.
	 * 
	 * Only the following types and formats can be evaluated into an options instance:<br>
	 * &nbsp; &#8226; &nbsp; <code>null</code>, a string or an instance;<br>
	 * &nbsp; &#8226; &nbsp; an array of properties, given as <samp>name => value</samp> pairs;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Feralygon\Kit\Interfaces\Arrayable</code> interface.
	 * 
	 * @see \Feralygon\Kit\Interfaces\Arrayable
	 * @param string $class
	 * <p>The class to use.</p>
	 * @param bool $clone [default = false]
	 * <p>If an instance is given, then clone it into a new one with the same properties.</p>
	 * @param callable|null $builder [default = null]
	 * <p>The function to use to build an instance.<br>
	 * It is expected to be compatible with the following signature:<br>
	 * <br>
	 * <code>function (array $properties): Feralygon\Kit\Options</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>array $properties</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The properties to build with, as <samp>name => value</samp> pairs.<br>
	 * <br>
	 * Return: <code><b>Feralygon\Kit\Options</b></code><br>
	 * The built instance.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a key to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setKeyAsOptions(
		string $class, bool $clone = false, ?callable $builder = null, bool $nullable = false
	): object
	{
		$this->getKeyEvaluatorsManager()->setAsOptions($class, $clone, $builder, $nullable);
		return $this;
	}
	
	/**
	 * Set to only allow a key evaluated as a structure instance.
	 * 
	 * Only the following types and formats can be evaluated into a structure instance:<br>
	 * &nbsp; &#8226; &nbsp; <code>null</code>, a string or an instance;<br>
	 * &nbsp; &#8226; &nbsp; an array of properties, given as <samp>name => value</samp> pairs;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Feralygon\Kit\Interfaces\Arrayable</code> interface.
	 * 
	 * @see \Feralygon\Kit\Interfaces\Arrayable
	 * @param string $class
	 * <p>The class to use.</p>
	 * @param bool $clone [default = false]
	 * <p>If an instance is given, then clone it into a new one with the same properties.</p>
	 * @param callable|null $builder [default = null]
	 * <p>The function to use to build an instance.<br>
	 * It is expected to be compatible with the following signature:<br>
	 * <br>
	 * <code>function (array $properties): Feralygon\Kit\Structure</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>array $properties</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The properties to build with, as <samp>name => value</samp> pairs.<br>
	 * &nbsp; &nbsp; &nbsp; Required properties may also be given as an array of values 
	 * (<samp>[value1, value2, ...]</samp>), in the same order as how these properties were first declared.<br>
	 * <br>
	 * Return: <code><b>Feralygon\Kit\Structure</b></code><br>
	 * The built instance.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a key to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setKeyAsStructure(
		string $class, bool $clone = false, ?callable $builder = null, bool $nullable = false
	): object
	{
		$this->getKeyEvaluatorsManager()->setAsStructure($class, $clone, $builder, $nullable);
		return $this;
	}
	
	/**
	 * Set to only allow a key evaluated as a dictionary instance.
	 * 
	 * Only the following types and formats can be evaluated into a dictionary instance:<br>
	 * &nbsp; &#8226; &nbsp; an instance;<br>
	 * &nbsp; &#8226; &nbsp; an associative array;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Feralygon\Kit\Interfaces\Arrayable</code> interface.
	 * 
	 * @see \Feralygon\Kit\Interfaces\Arrayable
	 * @param \Feralygon\Kit\Primitives\Dictionary|null $template [default = null]
	 * <p>The template instance to clone from and evaluate into.</p>
	 * @param bool $clone [default = false]
	 * <p>If an instance is given, then clone it into a new one with the same pairs and evaluator functions.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a key to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setKeyAsDictionary(
		?Dictionary $template = null, bool $clone = false, bool $nullable = false
	): object
	{
		$this->getKeyEvaluatorsManager()->setAsDictionary($template, $clone, $nullable);
		return $this;
	}
	
	/**
	 * Set to only allow a key evaluated as a vector instance.
	 * 
	 * Only the following types and formats can be evaluated into a vector instance:<br>
	 * &nbsp; &#8226; &nbsp; an instance;<br>
	 * &nbsp; &#8226; &nbsp; a non-associative array;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Feralygon\Kit\Interfaces\Arrayable</code> interface.
	 * 
	 * @see \Feralygon\Kit\Interfaces\Arrayable
	 * @param \Feralygon\Kit\Primitives\Vector|null $template [default = null]
	 * <p>The template instance to clone from and evaluate into.</p>
	 * @param bool $clone [default = false]
	 * <p>If an instance is given, then clone it into a new one with the same values and evaluator functions.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow a key to evaluate as <code>null</code>.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setKeyAsVector(?Vector $template = null, bool $clone = false, bool $nullable = false): object
	{
		$this->getKeyEvaluatorsManager()->setAsVector($template, $clone, $nullable);
		return $this;
	}
	
	
	
	//Final protected methods
	/**
	 * Get key evaluators manager instance.
	 * 
	 * @return \Feralygon\Kit\Managers\Evaluators
	 * <p>The key evaluators manager instance.</p>
	 */
	final protected function getKeyEvaluatorsManager(): Manager
	{
		if (!isset($this->key_evaluators_manager)) {
			$this->key_evaluators_manager = new Manager($this);
		}
		return $this->key_evaluators_manager;
	}
	
	/**
	 * Process a given key evaluators debug info instance.
	 * 
	 * @param \Feralygon\Kit\Traits\DebugInfo\Info $info
	 * <p>The debug info instance to process.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final protected function processKeyEvaluatorsDebugInfo(DebugInfo $info): object
	{
		$info->hideObjectProperty('key_evaluators_manager', self::class);
		return $this;
	}
	
	/**
	 * Process key evaluators cloning.
	 * 
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final protected function processKeyEvaluatorsCloning(): object
	{
		if (isset($this->key_evaluators_manager)) {
			$this->key_evaluators_manager = $this->key_evaluators_manager->cloneForOwner($this);
		}
		return $this;
	}
}
