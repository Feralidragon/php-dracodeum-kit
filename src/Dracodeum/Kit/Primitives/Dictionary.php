<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Primitives;

use Dracodeum\Kit\Primitive;
use Dracodeum\Kit\Interfaces\{
	DebugInfo as IDebugInfo,
	Readonlyable as IReadonlyable,
	Arrayable as IArrayable,
	ArrayInstantiable as IArrayInstantiable,
	Keyable as IKeyable,
	Stringifiable as IStringifiable,
	Cloneable as ICloneable
};
use Dracodeum\Kit\Traits\DebugInfo\Interfaces\DebugInfoProcessor as IDebugInfoProcessor;
use Dracodeum\Kit\Primitives\Dictionary\Exceptions;
use Dracodeum\Kit\Traits;
use Dracodeum\Kit\Traits\DebugInfo\Info as DebugInfo;
use Dracodeum\Kit\Options\Text as TextOptions;
use Dracodeum\Kit\Utilities\{
	Call as UCall,
	Data as UData,
	Text as UText,
	Type as UType
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
 * @see https://en.wikipedia.org/wiki/Associative_array
 */
final class Dictionary extends Primitive
implements IDebugInfo, IDebugInfoProcessor, \ArrayAccess, \Countable, \Iterator, \JsonSerializable, IReadonlyable,
IArrayable, IArrayInstantiable, IKeyable, IStringifiable, ICloneable
{
	//Traits
	use Traits\DebugInfo;
	use Traits\Readonly;
	use Traits\Stringifiable;
	use Traits\Evaluators;
	use Traits\KeyEvaluators;
	use Traits\CloneableOnly;
	
	
	
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
	 * @param array $pairs [default = []]
	 * <p>The pairs to instantiate with, as <samp>key => value</samp>.</p>
	 */
	final public function __construct(array $pairs = [])
	{
		//read-only
		$this->addReadonlyCallback(function (bool $recursive): void {
			//evaluators
			$this->lockKeyEvaluators()->lockEvaluators();
			
			//recursive
			if ($recursive) {
				UType::setValueAsReadonly($this->keys, $recursive);
				UType::setValueAsReadonly($this->values, $recursive);
			}
		});
		
		//key evaluator callback
		$this->getKeyEvaluatorsManager()->addAdditionCallback(function (callable $evaluator): void {
			foreach ($this->keys as $index => &$key) {
				$previous_key = $key;
				if (!$evaluator($key)) {
					$this->unsetIndex($index);
				} elseif ($key !== $previous_key) {
					$this->setKeyValue($key, $this->values[$index])->unsetIndex($index);
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
		
		//pairs
		if (!empty($pairs)) {
			$this->setAll($pairs);
		}
	}
	
	
	
	//Implemented final public methods (Dracodeum\Kit\Traits\DebugInfo\Interfaces\DebugInfoProcessor)
	/** {@inheritdoc} */
	final public function processDebugInfo(DebugInfo $info): void
	{
		//initialize
		$complex = false;
		foreach ($this->keys as $key) {
			if (!is_int($key) && !is_string($key)) {
				$complex = true;
				break;
			}
		}
		
		//pairs
		$pairs = [];
		if ($complex) {
			foreach ($this->keys as $index => $key) {
				$pairs[] = [
					'@key' => $key,
					'@value' => $this->values[$index]
				];
			}
		} else {
			foreach ($this->keys as $index => $key) {
				$pairs[$key] = $this->values[$index];
			}
		}
		
		//process
		$this->processReadonlyDebugInfo($info)->processEvaluatorsDebugInfo($info)->processKeyEvaluatorsDebugInfo($info);
		
		//set
		$info->set('@pairs', $pairs);
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
		$index = key($this->cursor_map);
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
		return (object)$this->getAll();
	}
	
	
	
	//Implemented final public methods (Dracodeum\Kit\Interfaces\Arrayable)
	/** {@inheritdoc} */
	final public function toArray(bool $recursive = false): array
	{
		$array = $this->getAll();
		if ($recursive) {
			foreach ($array as &$value) {
				if (is_object($value)) {
					UData::evaluate($value, null, false, false, true);
				}
			}
			unset($value);
		}
		return $array;
	}
	
	
	
	//Implemented final public static methods (Dracodeum\Kit\Interfaces\ArrayInstantiable)
	/** {@inheritdoc} */
	final public static function fromArray(array $array): object
	{
		return new static($array);
	}
	
	
	
	//Implemented final public methods (Dracodeum\Kit\Interfaces\Keyable)
	/** {@inheritdoc} */
	final public function toKey(bool $recursive = false, ?bool &$safe = null): string
	{
		//pairs
		$pairs = [];
		$pairs_safe = true;
		foreach ($this->keys as $k => $key) {
			$pairs[UData::keyfy($key, $s)] = $this->values[$k];
			$pairs_safe = $pairs_safe && $s;
		}
		
		//key
		$key = static::class . '@pairs:' . UType::keyValue($pairs, $recursive, false, $safe);
		$safe = $safe && $pairs_safe;
		
		//return
		return $key;
	}
	
	
	
	//Implemented final public methods (Dracodeum\Kit\Interfaces\Stringifiable)
	/** {@inheritdoc} */
	final public function toString(?TextOptions $text_options = null): string
	{
		$pairs = [];
		foreach ($this->keys as $index => $key) {
			$pairs[UText::stringify($key, $text_options)] = $this->values[$index];
		}
		return UText::stringify($pairs, $text_options, ['associative' => true]);
	}
	
	
	
	//Implemented final public methods (Dracodeum\Kit\Interfaces\Cloneable)
	/** {@inheritdoc} */
	final public function clone(bool $recursive = false): object
	{
		//clone
		$clone = new static();
		
		//evaluators
		foreach ($this->getKeyEvaluators() as $evaluator) {
			$clone->addKeyEvaluator($evaluator);
		}
		foreach ($this->getEvaluators() as $evaluator) {
			$clone->addEvaluator($evaluator);
		}
		
		//properties
		$clone->keys = $this->keys;
		$clone->values = $this->values;
		$clone->cursor_map = $this->cursor_map;
		reset($clone->cursor_map);
		
		//recursive
		if ($recursive) {
			$clone->keys = UType::cloneValue($clone->keys, $recursive);
			$clone->values = UType::cloneValue($clone->values, $recursive);
		}
		
		//return
		return $clone;
	}
	
	
	
	//Final public methods
	/**
	 * Check if has value at a given key.
	 * 
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
	 * @param mixed $key
	 * <p>The key to get from.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Primitives\Dictionary\Exceptions\ValueNotSet
	 * @return mixed
	 * <p>The value from the given key.<br>
	 * If <var>$no_throw</var> is set to boolean <code>true</code>, 
	 * then <code>null</code> may also be returned if none is set.</p>
	 */
	final public function get($key, bool $no_throw = false)
	{
		$index = $this->getKeyIndex($key);
		if (!array_key_exists($index, $this->values)) {
			if ($no_throw) {
				return null;
			}
			throw new Exceptions\ValueNotSet([$this, $key]);
		}
		return $this->values[$index];
	}
	
	/**
	 * Check if value is set at a given key.
	 * 
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
	 * @param mixed $key
	 * <p>The key to set at.</p>
	 * @param mixed $value
	 * <p>The value to set.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Primitives\Dictionary\Exceptions\InvalidKey
	 * @throws \Dracodeum\Kit\Primitives\Dictionary\Exceptions\InvalidValue
	 * @return $this|bool
	 * <p>This instance, for chaining purposes.<br>
	 * If <var>$no_throw</var> is set to boolean <code>true</code>, 
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
			throw new Exceptions\InvalidKey([$this, $key]);
		}
		
		//value
		if (!$this->getEvaluatorsManager()->evaluate($value)) {
			if ($no_throw) {
				return false;
			}
			throw new Exceptions\InvalidValue([$this, $value, 'has_key' => true, 'key' => $key]);
		}
		
		//set
		$this->setKeyValue($key, $value);
		
		//return
		return $no_throw ? true : $this;
	}
	
	/**
	 * Unset value from a given key.
	 * 
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
	 * Check if is empty.
	 * 
	 * @return bool
	 * <p>Boolean <code>true</code> if is empty.</p>
	 */
	final public function isEmpty(): bool
	{
		return empty($this->values);
	}
	
	/**
	 * Get all pairs.
	 * 
	 * Only pairs whose keys are integers, floats or strings can be converted to PHP associative array keys.
	 * 
	 * @return array
	 * <p>All the pairs, as <samp>key => value</samp>.</p>
	 */
	final public function getAll(): array
	{
		$pairs = [];
		foreach ($this->keys as $index => $key) {
			UCall::guardInternal(is_int($key) || is_float($key) || is_string($key), [
				'error_message' => "Invalid pair key {{key}}.",
				'hint_message' => "Only a key as an integer, float or string is allowed in a PHP associative array.",
				'parameters' => ['key' => $key]
			]);
			$pairs[(string)$key] = $this->values[$index];
		}
		return $pairs;
	}
	
	/**
	 * Set all pairs.
	 * 
	 * @param array $pairs
	 * <p>The pairs to set, as <samp>key => value</samp>.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Primitives\Dictionary\Exceptions\InvalidKey
	 * @throws \Dracodeum\Kit\Primitives\Dictionary\Exceptions\InvalidValue
	 * @return $this|bool
	 * <p>This instance, for chaining purposes.<br>
	 * If <var>$no_throw</var> is set to boolean <code>true</code>, 
	 * then boolean <code>true</code> is returned if the pairs were successfully set, 
	 * or boolean <code>false</code> if otherwise.</p>
	 */
	final public function setAll(array $pairs, bool $no_throw = false)
	{
		//guard
		$this->guardNonReadonlyCall();
		
		//evaluate
		$keys = $values = [];
		$key_manager = $this->getKeyEvaluatorsManager();
		$value_manager = $this->getEvaluatorsManager();
		foreach ($pairs as $key => $value) {
			//key
			if (!$key_manager->evaluate($key)) {
				if ($no_throw) {
					return false;
				}
				throw new Exceptions\InvalidKey([$this, $key]);
			}
			
			//value
			if (!$value_manager->evaluate($value)) {
				if ($no_throw) {
					return false;
				}
				throw new Exceptions\InvalidValue([$this, $value, 'has_key' => true, 'key' => $key]);
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
	 * Clear pairs.
	 * 
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function clear(): Dictionary
	{
		$this->guardNonReadonlyCall();
		$this->keys = $this->values = $this->cursor_map = [];
		return $this;
	}
	
	/**
	 * Get keys.
	 * 
	 * @return array
	 * <p>The keys.</p>
	 */
	final public function getKeys(): array
	{
		return array_values($this->keys);
	}
	
	/**
	 * Get values.
	 * 
	 * @return array
	 * <p>The values.</p>
	 */
	final public function getValues(): array
	{
		return array_values($this->values);
	}
	
	
	
	//Final public static methods
	/**
	 * Build instance.
	 * 
	 * @param array $pairs [default = []]
	 * <p>The pairs to build with, as <samp>key => value</samp>.</p>
	 * @return static
	 * <p>The built instance.</p>
	 */
	final public static function build(array $pairs = []): Dictionary
	{
		return new static($pairs);
	}
	
	/**
	 * Evaluate a given value as an instance.
	 * 
	 * Only the following types and formats can be evaluated into an instance:<br>
	 * &nbsp; &#8226; &nbsp; an instance;<br>
	 * &nbsp; &#8226; &nbsp; an associative array;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Arrayable</code> interface.
	 * 
	 * @see \Dracodeum\Kit\Interfaces\Arrayable
	 * @param mixed $value [reference]
	 * <p>The value to evaluate (validate and sanitize).</p>
	 * @param \Dracodeum\Kit\Primitives\Dictionary|null $template [default = null]
	 * <p>The template instance to clone from and evaluate into.</p>
	 * @param bool $clone [default = false]
	 * <p>If an instance is given, then clone it into a new one with the same pairs and evaluator functions.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to evaluate as <code>null</code>.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value was successfully evaluated into an instance.</p>
	 */
	final public static function evaluate(
		&$value, ?Dictionary $template = null, bool $clone = false, bool $nullable = false
	): bool
	{
		return self::processCoercion($value, $template, $clone, $nullable, true);
	}
	
	/**
	 * Coerce a given value into an instance.
	 * 
	 * Only the following types and formats can be coerced into an instance:<br>
	 * &nbsp; &#8226; &nbsp; an instance;<br>
	 * &nbsp; &#8226; &nbsp; an associative array;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Arrayable</code> interface.
	 * 
	 * @see \Dracodeum\Kit\Interfaces\Arrayable
	 * @param mixed $value
	 * <p>The value to coerce (validate and sanitize).</p>
	 * @param \Dracodeum\Kit\Primitives\Dictionary|null $template [default = null]
	 * <p>The template instance to clone from and coerce into.</p>
	 * @param bool $clone [default = false]
	 * <p>If an instance is given, then clone it into a new one with the same pairs and evaluator functions.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @throws \Dracodeum\Kit\Primitives\Dictionary\Exceptions\CoercionFailed
	 * @return static|null
	 * <p>The given value coerced into an instance.<br>
	 * If nullable, then <code>null</code> may also be returned.</p>
	 */
	final public static function coerce(
		$value, ?Dictionary $template = null, bool $clone = false, bool $nullable = false
	): ?Dictionary
	{
		self::processCoercion($value, $template, $clone, $nullable);
		return $value;
	}
	
	/**
	 * Process the coercion of a given value into an instance.
	 * 
	 * Only the following types and formats can be coerced into an instance:<br>
	 * &nbsp; &#8226; &nbsp; an instance;<br>
	 * &nbsp; &#8226; &nbsp; an associative array;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Arrayable</code> interface.
	 * 
	 * @see \Dracodeum\Kit\Interfaces\Arrayable
	 * @param mixed $value [reference]
	 * <p>The value to process (validate and sanitize).</p>
	 * @param \Dracodeum\Kit\Primitives\Dictionary|null $template [default = null]
	 * <p>The template instance to clone from and coerce into.</p>
	 * @param bool $clone [default = false]
	 * <p>If an instance is given, then clone it into a new one with the same pairs and evaluator functions.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Primitives\Dictionary\Exceptions\CoercionFailed
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value was successfully coerced into an instance.</p>
	 */
	final public static function processCoercion(
		&$value, ?Dictionary $template = null, bool $clone = false, bool $nullable = false, bool $no_throw = false
	): bool
	{
		//nullable
		if (!isset($value)) {
			if ($nullable) {
				return true;
			} elseif ($no_throw) {
				return false;
			}
			throw new Exceptions\CoercionFailed([
				'value' => $value,
				'dictionary' => static::class,
				'error_code' => Exceptions\CoercionFailed::ERROR_CODE_NULL,
				'error_message' => "A null value is not allowed."
			]);
		}
		
		//coerce
		try {
			//object
			if (is_object($value) && $value instanceof Dictionary) {
				if (isset($template)) {
					$instance = $template->clone()->clear();
					foreach ($value->keys as $index => $key) {
						$instance->set($key, $value->values[$index]);
					}
					$value = $instance;
				} elseif ($clone) {
					$value = $value->clone();
				}
				return true;
			}
			
			//array
			$array = $value;
			if (UData::evaluate($array)) {
				$value = isset($template) ? $template->clone()->setAll($array) : static::build($array);
				return true;
			}
			
		} catch (\Exception $exception) {
			if ($no_throw) {
				return false;
			}
			throw new Exceptions\CoercionFailed([
				'value' => $value,
				'dictionary' => static::class,
				'error_code' => Exceptions\CoercionFailed::ERROR_CODE_BUILD_EXCEPTION,
				'error_message' => $exception->getMessage()
			]);
		}
		
		//finish
		if ($no_throw) {
			return false;
		}
		throw new Exceptions\CoercionFailed([
			'value' => $value,
			'dictionary' => static::class,
			'error_code' => Exceptions\CoercionFailed::ERROR_CODE_INVALID_TYPE,
			'error_message' => "Only the following types and formats can be coerced into an instance:\n" . 
				" - an instance;\n" . 
				" - an associative array;\n" . 
				" - an object implementing the \"Dracodeum\\Kit\\Interfaces\\Arrayable\" interface."
		]);
	}
	
	
	
	//Final private methods
	/**
	 * Get index from a given key.
	 * 
	 * @param mixed $key
	 * <p>The key to get from.</p>
	 * @return string
	 * <p>The index from the given key.</p>
	 */
	final private function getKeyIndex($key): string
	{
		return UData::keyfy($key);
	}
	
	/**
	 * Set index with a given key and value.
	 * 
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
	
	/**
	 * Set key with a given value.
	 * 
	 * @param mixed $key
	 * <p>The key to set.</p>
	 * @param mixed $value
	 * <p>The value to set with.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final private function setKeyValue($key, $value): Dictionary
	{
		$this->setIndex($this->getKeyIndex($key), $key, $value);
		return $this;
	}
}
