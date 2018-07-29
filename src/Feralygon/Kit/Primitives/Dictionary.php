<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Primitives;

use Feralygon\Kit\Interfaces\{
	Arrayable as IArrayable,
	ArrayInstantiable as IArrayInstantiable,
	Stringifiable as IStringifiable
};
use Feralygon\Kit\Primitives\Dictionary\Exceptions;
use Feralygon\Kit\Traits;
use Feralygon\Kit\Options\Text as TextOptions;
use Feralygon\Kit\Utilities\{
	Data as UData,
	Text as UText
};

/**
 * This primitive represents a dictionary.
 * 
 * A dictionary is a simple object which represents and stores an associative array.<br>
 * <br>
 * Unlike PHP associative arrays, this dictionary supports any type of key, and keys are strictly mapped by type.<br>
 * In other words, keys may also be arrays, objects and even resources themselves, and they retain their type and 
 * are not coerced into strings nor integers.<br>
 * <br>
 * It may also be restricted to specific key and value types through evaluator functions, 
 * and set as read-only to prevent any further changes.
 * 
 * @since 1.0.0
 * @see https://en.wikipedia.org/wiki/Associative_array
 */
final class Dictionary
implements \ArrayAccess, \Countable, \Iterator, \JsonSerializable, IArrayable, IArrayInstantiable, IStringifiable
{
	//Traits
	use Traits\Readonly;
	use Traits\Stringifiable;
	use Traits\Evaluators;
	use Traits\KeyEvaluators;
	
	
	
	//Private properties
	/** @var array */
	private $keys = [];
	
	/** @var array */
	private $values = [];
	
	/** @var string[] */
	private $cursor_map = [];
	
	
	
	//Final public magic methods
	/**
	 * Instantiate class.
	 * 
	 * @since 1.0.0
	 * @param array $values [default = []]
	 * <p>The values, as <samp>key => value</samp> pairs.</p>
	 * @param bool $readonly [default = false]
	 * <p>Set as read-only.</p>
	 */
	final public function __construct(array $values = [], bool $readonly = false)
	{
		//initialize read-only
		$this->initializeReadonly();
		$this->addReadonlyCallback(function (): void {
			$this->lockKeyEvaluators()->lockEvaluators();
		});
		
		//key evaluator callback
		$this->getKeyEvaluatorsManager()->addAdditionCallback(function (callable $evaluator): void {
			foreach ($this->keys as $index => &$key) {
				$previous_key = $key;
				if (!$evaluator($key)) {
					$this->unsetIndex($index);
				} elseif ($key !== $previous_key) {
					$this->setIndex($this->getKeyIndex($key), $this->values[$index]);
					$this->unsetIndex($index);
				}
				unset($previous_key);
			}
			unset($key);
		});
		
		//evaluator callback
		$this->getEvaluatorsManager()->addAdditionCallback(function (callable $evaluator): void {
			foreach ($this->values as $index => &$value) {
				if (!$evaluator($value)) {
					$this->unsetIndex($index);
				}
			}
			unset($value);
		});
		
		//values
		if (!empty($values)) {
			$this->setAll($values);
		}
		
		//read-only
		if ($readonly) {
			$this->setAsReadonly();
		}
	}
	
	
	
	//Implemented final public methods (ArrayAccess)
	/** {@inheritdoc} */
	final public function offsetExists($offset): bool
	{
		return $this->has($offset);
	}
	
	/** {@inheritdoc} */
	final public function offsetGet($offset)
	{
		return $this->get($offset);
	}
	
	/** {@inheritdoc} */
	final public function offsetSet($offset, $value): void
	{
		$this->set($offset, $value);
	}
	
	/** {@inheritdoc} */
	final public function offsetUnset($offset): void
	{
		$this->unset($offset);
	}
	
	
	
	//Implemented final public methods (Countable)
	/** {@inheritdoc} */
	final public function count(): int
	{
		return count($this->values);
	}
	
	
	
	//Implemented final public methods (Iterator)
	/** {@inheritdoc} */
	final public function current()
	{
		$index = current($this->cursor_map);
		return $index !== false ? $this->values[$index] : null;
	}
	
	/** {@inheritdoc} */
	final public function key()
	{
		$index = current($this->cursor_map);
		return $index !== false ? $this->keys[$index] : null;
	}
	
	/** {@inheritdoc} */
	final public function next(): void
	{
		if (current($this->cursor_map) === false) {
			reset($this->cursor_map);
		} else {
			next($this->cursor_map);
		}
	}
	
	/** {@inheritdoc} */
	final public function rewind(): void
	{
		reset($this->cursor_map);
	}
	
	/** {@inheritdoc} */
	final public function valid(): bool
	{
		return current($this->cursor_map) !== false;
	}
	
	
	
	//Implemented final public methods (JsonSerializable)
	/** {@inheritdoc} */
	final public function jsonSerialize()
	{
		return $this->getAll();
	}
	
	
	
	//Implemented final public methods (Feralygon\Kit\Interfaces\Arrayable)
	/** {@inheritdoc} */
	final public function toArray(): array
	{
		return $this->getAll();
	}
	
	
	
	//Implemented final public static methods (Feralygon\Kit\Interfaces\ArrayInstantiable)
	/** {@inheritdoc} */
	final public static function fromArray(array $array): object
	{
		return new static($array);
	}
	
	
	
	//Implemented final public methods (Feralygon\Kit\Interfaces\Stringifiable)
	/** {@inheritdoc} */
	final public function toString(?TextOptions $text_options = null): string
	{
		return UText::stringify($this->getAll(), $text_options);
	}
	
	
	
	//Final public methods
	/**
	 * Clone into a new instance.
	 * 
	 * The returning cloned instance is a new instance with the same keys and values and evaluator functions.
	 * 
	 * @since 1.0.0
	 * @param bool $readonly [default = false]
	 * <p>Set the new cloned instance as read-only.</p>
	 * @return static
	 * <p>The new cloned instance from this one.</p>
	 */
	final public function clone(bool $readonly = false): Dictionary
	{
		//new
		$instance = new static([], $readonly);
		
		//evaluators
		foreach ($this->getKeyEvaluators() as $evaluator) {
			$instance->addKeyEvaluator($evaluator);
		}
		foreach ($this->getEvaluators() as $evaluator) {
			$instance->addEvaluator($evaluator);
		}
		
		//clone
		$instance->keys = $this->keys;
		$instance->values = $this->values;
		$instance->cursor_map = $this->cursor_map;
		reset($instance->cursor_map);
		
		//return
		return $instance;
	}
	
	/**
	 * Check if has value at a given key.
	 * 
	 * @since 1.0.0
	 * @param mixed $key
	 * <p>The key to check.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if has value at the given key.</p>
	 */
	final public function has($key): bool
	{
		return array_key_exists($this->getKeyIndex($key), $this->values);
	}
	
	/**
	 * Get value from a given key.
	 * 
	 * @since 1.0.0
	 * @param mixed $key
	 * <p>The key to get from.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Feralygon\Kit\Primitives\Dictionary\Exceptions\ValueNotSet
	 * @return mixed
	 * <p>The value from the given key.<br>
	 * If <var>$no_throw</var> is set to <code>true</code>, 
	 * then <code>null</code> may also be returned if none is set.</p>
	 */
	final public function get($key, bool $no_throw = false)
	{
		$index = $this->getKeyIndex($key);
		if (!array_key_exists($index, $this->values)) {
			if ($no_throw) {
				return null;
			}
			throw new Exceptions\ValueNotSet(['dictionary' => $this, 'key' => $key]);
		}
		return $this->values[$index];
	}
	
	/**
	 * Check if value is set at a given key.
	 * 
	 * @since 1.0.0
	 * @param mixed $key
	 * <p>The key to check.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if value is set at the given key.</p>
	 */
	final public function isset($key): bool
	{
		return $this->get($key, true) !== null;
	}
	
	/**
	 * Set value at a given key.
	 * 
	 * @since 1.0.0
	 * @param mixed $key
	 * <p>The key to set at.</p>
	 * @param mixed $value
	 * <p>The value to set.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Feralygon\Kit\Primitives\Dictionary\Exceptions\InvalidKey
	 * @throws \Feralygon\Kit\Primitives\Dictionary\Exceptions\InvalidValue
	 * @return $this|bool
	 * <p>This instance, for chaining purposes.<br>
	 * If <var>$no_throw</var> is set to <code>true</code>, 
	 * then boolean <code>true</code> is returned if the value was successfully set, 
	 * or boolean <code>false</code> if otherwise.</p>
	 */
	final public function set($key, $value, bool $no_throw = false)
	{
		//guard
		$this->guardNonReadonlyCall();
		
		//key
		if (!$this->getKeyEvaluatorsManager()->evaluate($key)) {
			if ($no_throw) {
				return false;
			}
			throw new Exceptions\InvalidKey(['dictionary' => $this, 'key' => $key]);
		}
		
		//value
		if (!$this->getEvaluatorsManager()->evaluate($value)) {
			if ($no_throw) {
				return false;
			}
			throw new Exceptions\InvalidValue([
				'dictionary' => $this, 'value' => $value, 'has_key' => true, 'key' => $key
			]);
		}
		
		//set
		$this->setIndex($this->getKeyIndex($key), $key, $value);
		
		//return
		return $no_throw ? true : $this;
	}
	
	/**
	 * Unset value from a given key.
	 * 
	 * @since 1.0.0
	 * @param mixed $key
	 * <p>The key to unset from.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function unset($key): Dictionary
	{
		$this->guardNonReadonlyCall();
		$this->unsetIndex($this->getKeyIndex($key));
		return $this;
	}
	
	/**
	 * Get all values.
	 * 
	 * @since 1.0.0
	 * @return array
	 * <p>All the values.</p>
	 */
	final public function getAll(): array
	{
		//TODO
	}
	
	/**
	 * Set all values.
	 * 
	 * @since 1.0.0
	 * @param array $values
	 * <p>The values to set, as <samp>key => value</samp> pairs.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Feralygon\Kit\Primitives\Dictionary\Exceptions\InvalidKey
	 * @throws \Feralygon\Kit\Primitives\Dictionary\Exceptions\InvalidValue
	 * @return $this|bool
	 * <p>This instance, for chaining purposes.<br>
	 * If <var>$no_throw</var> is set to <code>true</code>, 
	 * then boolean <code>true</code> is returned if the values were successfully set, 
	 * or boolean <code>false</code> if otherwise.</p>
	 */
	final public function setAll(array $values, bool $no_throw = false)
	{
		//guard
		$this->guardNonReadonlyCall();
		
		//evaluate
		$keys = $values = [];
		$key_manager = $this->getKeyEvaluatorsManager();
		$value_manager = $this->getEvaluatorsManager();
		foreach ($values as $key => $value) {
			//key
			if (!$key_manager->evaluate($key)) {
				if ($no_throw) {
					return false;
				}
				throw new Exceptions\InvalidKey(['dictionary' => $this, 'key' => $key]);
			}
			
			//value
			if (!$value_manager->evaluate($value)) {
				if ($no_throw) {
					return false;
				}
				throw new Exceptions\InvalidValue([
					'dictionary' => $this, 'value' => $value, 'has_key' => true, 'key' => $key
				]);
			}
			
			//finish
			$index = $this->getKeyIndex($key);
			$keys[$index] = $key;
			$values[$index] = $value;
		}
		
		//set
		$this->keys = $keys;
		$this->values = $values;
		$indexes = array_keys($this->keys);
		$this->cursor_map = array_combine($indexes, $indexes);
		
		//return
		return $no_throw ? true : $this;
	}
	
	/**
	 * Clear values.
	 * 
	 * @since 1.0.0
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function clear(): Dictionary
	{
		$this->guardNonReadonlyCall();
		$this->values = $this->keys = $this->cursor_map = [];
		return $this;
	}
	
	
	
	//Final public static methods
	/**
	 * Build instance.
	 * 
	 * @since 1.0.0
	 * @param array $values [default = []]
	 * <p>The values to build with, as <samp>key => value</samp> pairs.</p>
	 * @param bool $readonly [default = false]
	 * <p>Set the built instance as read-only.</p>
	 * @return static
	 * <p>The built instance.</p>
	 */
	final public static function build(array $values = [], bool $readonly = false): Dictionary
	{
		return new static($values, $readonly);
	}
	
	/**
	 * Evaluate a given value as an instance.
	 * 
	 * Only the following types and formats can be evaluated into an instance:<br>
	 * &nbsp; &#8226; &nbsp; an instance;<br>
	 * &nbsp; &#8226; &nbsp; an associative array;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Feralygon\Kit\Interfaces\Arrayable</code> interface.
	 * 
	 * @since 1.0.0
	 * @param mixed $value [reference]
	 * <p>The value to evaluate (validate and sanitize).</p>
	 * @param \Feralygon\Kit\Primitives\Dictionary|null $template [default = null]
	 * <p>The template instance to clone from and evaluate into.</p>
	 * @param bool $readonly [default = false]
	 * <p>Evaluate into a read-only instance.<br>
	 * If an instance is given and is not read-only, then a new one is created as read-only.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to evaluate as <code>null</code>.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value was successfully evaluated into an instance.</p>
	 */
	final public static function evaluate(
		&$value, ?Dictionary $template = null, bool $readonly = false, bool $nullable = false
	): bool
	{
		try {
			$value = static::coerce($value, $template, $readonly, $nullable);
		} catch (Exceptions\CoercionFailed $exception) {
			return false;
		}
		return true;
	}
	
	/**
	 * Coerce a given value into an instance.
	 * 
	 * Only the following types and formats can be coerced into an instance:<br>
	 * &nbsp; &#8226; &nbsp; an instance;<br>
	 * &nbsp; &#8226; &nbsp; an associative array;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Feralygon\Kit\Interfaces\Arrayable</code> interface.
	 * 
	 * @since 1.0.0
	 * @param mixed $value
	 * <p>The value to coerce (validate and sanitize).</p>
	 * @param \Feralygon\Kit\Primitives\Dictionary|null $template [default = null]
	 * <p>The template instance to clone from and coerce into.</p>
	 * @param bool $readonly [default = false]
	 * <p>Coerce into a read-only instance.<br>
	 * If an instance is given and is not read-only, then a new one is created as read-only.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @throws \Feralygon\Kit\Primitives\Dictionary\Exceptions\CoercionFailed
	 * @return static|null
	 * <p>The given value coerced into an instance.<br>
	 * If nullable, then <code>null</code> may also be returned.</p>
	 */
	final public static function coerce(
		$value, ?Dictionary $template = null, bool $readonly = false, bool $nullable = false
	): ?Dictionary
	{
		//nullable
		if (!isset($value)) {
			if ($nullable) {
				return null;
			}
			throw new Exceptions\CoercionFailed([
				'value' => $value,
				'dictionary' => static::class,
				'error_code' => Exceptions\CoercionFailed::ERROR_CODE_NULL,
				'error_message' => "A null value is not allowed."
			]);
		}
		
		//coerce
		$array = $value;
		if (is_object($value)) {
			if ($value instanceof Dictionary) {
				if (!isset($template) && (!$readonly || $value->isReadonly())) {
					return $value;
				}
				$array = $value->getAll();
			} elseif ($value instanceof IArrayable) {
				$array = $value->toArray();
			}
		}
		
		//build
		if (is_array($array)) {
			try {
				if (isset($template)) {
					$instance = $template->clone()->setAll($array);
					if ($readonly) {
						$instance->setAsReadonly();
					}
					return $instance;
				}
				return static::build($array, $readonly);
			} catch (\Exception $exception) {
				throw new Exceptions\CoercionFailed([
					'value' => $value,
					'dictionary' => static::class,
					'error_code' => Exceptions\CoercionFailed::ERROR_CODE_BUILD_EXCEPTION,
					'error_message' => $exception->getMessage()
				]);
			}
		}
		
		//throw
		throw new Exceptions\CoercionFailed([
			'value' => $value,
			'dictionary' => static::class,
			'error_code' => Exceptions\CoercionFailed::ERROR_CODE_INVALID_TYPE,
			'error_message' => "Only the following types and formats can be coerced into an instance:\n" . 
				" - an instance;\n" . 
				" - an associative array;\n" . 
				" - an object implementing the \"Feralygon\\Kit\\Interfaces\\Arrayable\" interface."
		]);
	}
	
	
	
	//Final private methods
	/**
	 * Get key index.
	 * 
	 * @since 1.0.0
	 * @param mixed $key
	 * <p>The key to get from.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final private function getKeyIndex($key): string
	{
		return UData::keyfy($key);
	}
	
	/**
	 * Set index with a given key and value.
	 * 
	 * @since 1.0.0
	 * @param string $index
	 * <p>The index to set.</p>
	 * @param mixed $key
	 * <p>The key to set with.</p>
	 * @param mixed $value
	 * <p>The value to set with.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final private function setIndex(string $index, $key, $value): Dictionary
	{
		$this->keys[$index] = $key;
		$this->values[$index] = $value;
		$this->cursor_map[$index] = $index;
		return $this;
	}
	
	/**
	 * Unset index.
	 * 
	 * @since 1.0.0
	 * @param string $index
	 * <p>The index to unset.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final private function unsetIndex(string $index): Dictionary
	{
		if (isset($this->cursor_map[$index]) && current($this->cursor_map) === $index) {
			prev($this->cursor_map);
		}
		unset($this->values[$index], $this->keys[$index], $this->cursor_map[$index]);
		return $this;
	}
}
