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
use Dracodeum\Kit\Primitives\Vector\Exceptions;
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
 * This primitive represents a vector.
 * 
 * A vector is a simple object which represents and stores a contiguous array.<br>
 * <br>
 * It may also be restricted to specific value types through evaluator functions, 
 * and set as read-only to prevent any further changes.
 * 
 * @see https://en.wikipedia.org/wiki/Array_data_structure
 * @see https://en.wikipedia.org/wiki/Sequence_container_(C%2B%2B)#Vector
 */
final class Vector extends Primitive
implements IDebugInfo, IDebugInfoProcessor, \ArrayAccess, \Countable, \Iterator, \JsonSerializable, IReadonlyable,
IArrayable, IArrayInstantiable, IKeyable, IStringifiable, ICloneable
{
	//Traits
	use Traits\DebugInfo;
	use Traits\Readonly;
	use Traits\Stringifiable;
	use Traits\Evaluators;
	use Traits\CloneableOnly;
	
	
	
	//Private properties
	/** @var array */
	private $values = [];
	
	/** @var int|null */
	private $min_index = null;
	
	/** @var int|null */
	private $max_index = null;
	
	/** @var int|null */
	private $cursor = null;
	
	
	
	//Final public magic methods
	/**
	 * Instantiate class.
	 * 
	 * @param array $values [default = []]
	 * <p>The values to instantiate with.</p>
	 */
	final public function __construct(array $values = [])
	{
		//read-only
		$this->addReadonlyCallback(function (bool $recursive): void {
			//evaluators
			$this->lockEvaluators();
			
			//recursive
			if ($recursive) {
				UType::setValueAsReadonly($this->values, $recursive);
			}
		});
		
		//evaluator callback
		$this->getEvaluatorsManager()->addAdditionCallback(function (callable $evaluator): void {
			//values
			$values = $this->values;
			foreach ($values as $index => &$value) {
				if (!$evaluator($value)) {
					unset($values[$index]);
				}
			}
			unset($value);
			
			//reset
			if ($values !== $this->values) {
				$this->values = array_values($values);
				$this->reset();
			}
		});
		
		//values
		if (!empty($values)) {
			$this->setAll($values);
		}
	}
	
	
	
	//Implemented final public methods (Dracodeum\Kit\Traits\DebugInfo\Interfaces\DebugInfoProcessor)
	/** {@inheritdoc} */
	final public function processDebugInfo(DebugInfo $info): void
	{
		$this->processReadonlyDebugInfo($info)->processEvaluatorsDebugInfo($info);
		$info->set('@values', $this->getAll());
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
		if (isset($offset)) {
			$this->set($offset, $value);
		} else {
			$this->append($value);
		}
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
		return isset($this->cursor) ? $this->values[$this->cursor] ?? null : null;
	}
	
	/** {@inheritdoc} */
	final public function key()
	{
		return isset($this->cursor) && isset($this->min_index) ? $this->cursor - $this->min_index : null;
	}
	
	/** {@inheritdoc} */
	final public function next(): void
	{
		$this->cursor++;
	}
	
	/** {@inheritdoc} */
	final public function rewind(): void
	{
		$this->cursor = $this->min_index;
	}
	
	/** {@inheritdoc} */
	final public function valid(): bool
	{
		return isset($this->cursor) && isset($this->max_index) && $this->cursor <= $this->max_index;
	}
	
	
	
	//Implemented final public methods (JsonSerializable)
	/** {@inheritdoc} */
	final public function jsonSerialize()
	{
		return $this->getAll();
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
		return static::class . '@values:' . UType::keyValue($this->values, $recursive, false, $safe);
	}
	
	
	
	//Implemented final public methods (Dracodeum\Kit\Interfaces\Stringifiable)
	/** {@inheritdoc} */
	final public function toString(?TextOptions $text_options = null): string
	{
		return UText::stringify($this->getAll(), $text_options);
	}
	
	
	
	//Implemented final public methods (Dracodeum\Kit\Interfaces\Cloneable)
	/** {@inheritdoc} */
	final public function clone(bool $recursive = false): object
	{
		//clone
		$clone = new static();
		
		//evaluators
		foreach ($this->getEvaluators() as $evaluator) {
			$clone->addEvaluator($evaluator);
		}
		
		//properties
		$clone->values = $this->values;
		$clone->min_index = $this->min_index;
		$clone->max_index = $this->max_index;
		
		//recursive
		if ($recursive) {
			$clone->values = UType::cloneValue($clone->values, $recursive);
		}
		
		//return
		return $clone;
	}
	
	
	
	//Final public methods
	/**
	 * Check if has value at a given index.
	 * 
	 * @param int $index
	 * <p>The index to check.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if has value at the given index.</p>
	 */
	final public function has(int $index): bool
	{
		UCall::guardParameter('index', $index, $index >= 0, [
			'hint_message' => "Only a value greater than or equal to 0 is allowed."
		]);
		return isset($this->min_index) && array_key_exists($this->min_index + $index, $this->values);
	}
	
	/**
	 * Get value from a given index.
	 * 
	 * @param int $index
	 * <p>The index to get from.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Primitives\Vector\Exceptions\ValueNotSet
	 * @return mixed
	 * <p>The value from the given index.<br>
	 * If <var>$no_throw</var> is set to boolean <code>true</code>, 
	 * then <code>null</code> may also be returned if none is set.</p>
	 */
	final public function get(int $index, bool $no_throw = false)
	{
		//guard
		UCall::guardParameter('index', $index, $index >= 0, [
			'hint_message' => "Only a value greater than or equal to 0 is allowed."
		]);
		
		//get
		if (!isset($this->min_index) || !array_key_exists($this->min_index + $index, $this->values)) {
			if ($no_throw) {
				return null;
			}
			throw new Exceptions\ValueNotSet([$this, $index]);
		}
		return $this->values[$this->min_index + $index];
	}
	
	/**
	 * Check if value is set at a given index.
	 * 
	 * @param int $index
	 * <p>The index to check.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if value is set at the given index.</p>
	 */
	final public function isset(int $index): bool
	{
		return $this->get($index, true) !== null;
	}
	
	/**
	 * Set value at a given index.
	 * 
	 * @param int $index
	 * <p>The index to set at.</p>
	 * @param mixed $value
	 * <p>The value to set.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Primitives\Vector\Exceptions\InvalidIndex
	 * @throws \Dracodeum\Kit\Primitives\Vector\Exceptions\InvalidValue
	 * @return $this|bool
	 * <p>This instance, for chaining purposes.<br>
	 * If <var>$no_throw</var> is set to boolean <code>true</code>, 
	 * then boolean <code>true</code> is returned if the value was successfully set, 
	 * or boolean <code>false</code> if otherwise.</p>
	 */
	final public function set(int $index, $value, bool $no_throw = false)
	{
		//guard
		$this->guardNonReadonlyCall();
		UCall::guardParameter('index', $index, $index >= 0, [
			'hint_message' => "Only a value greater than or equal to 0 is allowed."
		]);
		
		//index
		$max_index = isset($this->min_index) ? $this->max_index - $this->min_index + 1 : 0;
		if ($index > $max_index) {
			if ($no_throw) {
				return false;
			}
			throw new Exceptions\InvalidIndex([$this, $index, 'max_index' => $max_index]);
		}
		
		//value
		if (!$this->getEvaluatorsManager()->evaluate($value)) {
			if ($no_throw) {
				return false;
			}
			throw new Exceptions\InvalidValue([$this, $value, 'index' => $index]);
		}
		
		//set
		$this->values[$this->min_index + $index] = $value;
		if ($index === $max_index) {
			$this->max_index++;
			if ($this->max_index === PHP_INT_MAX) {
				$this->reset();
			}
		}
		
		//return
		return $no_throw ? true : $this;
	}
	
	/**
	 * Prepend value.
	 * 
	 * @param mixed $value
	 * <p>The value to prepend.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Primitives\Vector\Exceptions\InvalidValue
	 * @return $this|bool
	 * <p>This instance, for chaining purposes.<br>
	 * If <var>$no_throw</var> is set to boolean <code>true</code>, 
	 * then boolean <code>true</code> is returned if the value was successfully prepended, 
	 * or boolean <code>false</code> if otherwise.</p>
	 */
	final public function prepend($value, bool $no_throw = false)
	{
		//guard
		$this->guardNonReadonlyCall();
		
		//value
		if (!$this->getEvaluatorsManager()->evaluate($value)) {
			if ($no_throw) {
				return false;
			}
			throw new Exceptions\InvalidValue([$this, $value]);
		}
		
		//prepend
		if ($this->min_index === 0) {
			array_unshift($this->values, $value);
			$this->reset();
		} elseif (isset($this->min_index)) {
			$this->values = [--$this->min_index => $value] + $this->values;
		} else {
			$this->values = [$value];
			$this->reset();
		}
		
		//return
		return $no_throw ? true : $this;
	}
	
	/**
	 * Append value.
	 * 
	 * @param mixed $value
	 * <p>The value to append.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Primitives\Vector\Exceptions\InvalidValue
	 * @return $this|bool
	 * <p>This instance, for chaining purposes.<br>
	 * If <var>$no_throw</var> is set to boolean <code>true</code>, 
	 * then boolean <code>true</code> is returned if the value was successfully appended, 
	 * or boolean <code>false</code> if otherwise.</p>
	 */
	final public function append($value, bool $no_throw = false)
	{
		//guard
		$this->guardNonReadonlyCall();
		
		//value
		if (!$this->getEvaluatorsManager()->evaluate($value)) {
			if ($no_throw) {
				return false;
			}
			throw new Exceptions\InvalidValue([$this, $value]);
		}
		
		//append
		if (isset($this->max_index)) {
			$this->values[++$this->max_index] = $value;
			if ($this->max_index === PHP_INT_MAX) {
				$this->reset();
			}
		} else {
			$this->values = [$value];
			$this->reset();
		}
		
		//return
		return $no_throw ? true : $this;
	}
	
	/**
	 * Unset value from a given index.
	 * 
	 * @param int $index
	 * <p>The index to unset from.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function unset(int $index): Vector
	{
		//guard
		$this->guardNonReadonlyCall();
		UCall::guardParameter('index', $index, $index >= 0, [
			'hint_message' => "Only a value greater than or equal to 0 is allowed."
		]);
		
		//unset
		if (isset($this->min_index)) {
			$max_index = $this->max_index - $this->min_index;
			if ($index === 0) {
				unset($this->values[$this->min_index]);
				if (empty($this->values)) {
					$this->reset();
				} else {
					$this->min_index++;
				}
			} elseif ($index === $max_index) {
				unset($this->values[$this->max_index]);
				if (empty($this->values)) {
					$this->reset();
				} else {
					$this->max_index--;
				}
			} elseif ($index < $max_index) {
				unset($this->values[$this->min_index + $index]);
				$this->values = array_values($this->values);
				$this->reset();
			}
		}
		
		//return
		return $this;
	}
	
	/**
	 * Shift value.
	 * 
	 * The process of shifting a value consists in removing it from the start of a vector.
	 * 
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Primitives\Vector\Exceptions\ValuesNotSet
	 * @return mixed
	 * <p>The shifted value.<br>
	 * If <var>$no_throw</var> is set to boolean <code>true</code>, 
	 * then <code>null</code> may also be returned if no values are set.</p>
	 */
	final public function shift(bool $no_throw = false)
	{
		$this->guardNonReadonlyCall();
		if (isset($this->min_index)) {
			$value = $this->values[$this->min_index];
			unset($this->values[$this->min_index]);
			if (empty($this->values)) {
				$this->reset();
			} else {
				$this->min_index++;
			}
			return $value;
		} elseif ($no_throw) {
			return null;
		}
		throw new Exceptions\ValuesNotSet([$this]);
	}
	
	/**
	 * Pop value.
	 * 
	 * The process of popping a value consists in removing it from the end of a vector.
	 * 
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Primitives\Vector\Exceptions\ValuesNotSet
	 * @return mixed
	 * <p>The popped value.<br>
	 * If <var>$no_throw</var> is set to boolean <code>true</code>, 
	 * then <code>null</code> may also be returned if no values are set.</p>
	 */
	final public function pop(bool $no_throw = false)
	{
		$this->guardNonReadonlyCall();
		if (isset($this->max_index)) {
			$value = $this->values[$this->max_index];
			unset($this->values[$this->max_index]);
			if (empty($this->values)) {
				$this->reset();
			} else {
				$this->max_index--;
			}
			return $value;
		} elseif ($no_throw) {
			return null;
		}
		throw new Exceptions\ValuesNotSet([$this]);
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
	 * Get all values.
	 * 
	 * @return array
	 * <p>All the values.</p>
	 */
	final public function getAll(): array
	{
		if (isset($this->min_index) && $this->min_index > 0) {
			$this->reset();
		}
		return $this->values;
	}
	
	/**
	 * Set all values.
	 * 
	 * @param array $values
	 * <p>The values to set.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Primitives\Vector\Exceptions\InvalidValues
	 * @throws \Dracodeum\Kit\Primitives\Vector\Exceptions\InvalidValue
	 * @return $this|bool
	 * <p>This instance, for chaining purposes.<br>
	 * If <var>$no_throw</var> is set to boolean <code>true</code>, 
	 * then boolean <code>true</code> is returned if the values were successfully set, 
	 * or boolean <code>false</code> if otherwise.</p>
	 */
	final public function setAll(array $values, bool $no_throw = false)
	{
		//guard
		$this->guardNonReadonlyCall();
		
		//check
		if (UData::associative($values)) {
			if ($no_throw) {
				return false;
			}
			throw new Exceptions\InvalidValues([$this, $values]);
		}
		
		//evaluate
		$manager = $this->getEvaluatorsManager();
		foreach ($values as $index => &$value) {
			if (!$manager->evaluate($value)) {
				if ($no_throw) {
					return false;
				}
				throw new Exceptions\InvalidValue([$this, $value, 'index' => $index]);
			}
		}
		unset($value);
		
		//set
		$this->values = $values;
		$this->reset();
		
		//return
		return $no_throw ? true : $this;
	}
	
	/**
	 * Clear values.
	 * 
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function clear(): Vector
	{
		$this->guardNonReadonlyCall();
		$this->values = [];
		$this->reset();
		return $this;
	}
	
	/**
	 * Truncate values to a given length.
	 * 
	 * @param int $length
	 * <p>The length to truncate to.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function truncate(int $length): Vector
	{
		//guard
		$this->guardNonReadonlyCall();
		UCall::guardParameter('length', $length, $length >= 0, [
			'hint_message' => "Only a value greater than or equal to 0 is allowed."
		]);
		
		//truncate
		$this->values = array_slice($this->values, 0, $length);
		$this->reset();
		
		//return
		return $this;
	}
	
	/**
	 * Slice values from a given index.
	 * 
	 * @param int $index
	 * <p>The index to slice from.</p>
	 * @param int|null $length [default = null]
	 * <p>The length to slice.<br>
	 * If not set, then the current vector length is used.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function slice(int $index, ?int $length = null): Vector
	{
		//guard
		$this->guardNonReadonlyCall();
		UCall::guardParameter('index', $index, $index >= 0, [
			'hint_message' => "Only a value greater than or equal to 0 is allowed."
		]);
		UCall::guardParameter('length', $length, !isset($length) || $length >= 0, [
			'hint_message' => "Only null or a value greater than or equal to 0 is allowed."
		]);
		
		//slice
		$this->values = array_slice($this->values, $index, $length ?? count($this->values));
		$this->reset();
		
		//return
		return $this;
	}
	
	/**
	 * Remove duplicated values.
	 * 
	 * The removal is performed in such a way that only strictly unique values are left within this vector, 
	 * as not only the values are considered, but also their types as well.
	 * 
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function unique(): Vector
	{
		$this->guardNonReadonlyCall();
		$this->values = UData::unique($this->values, 0, UData::UNIQUE_ARRAYS_AS_VALUES);
		$this->reset();
		return $this;
	}
	
	
	
	//Final public static methods
	/**
	 * Build instance.
	 * 
	 * @param array $values [default = []]
	 * <p>The values to build with.</p>
	 * @return static
	 * <p>The built instance.</p>
	 */
	final public static function build(array $values = []): Vector
	{
		return new static($values);
	}
	
	/**
	 * Evaluate a given value as an instance.
	 * 
	 * Only the following types and formats can be evaluated into an instance:<br>
	 * &nbsp; &#8226; &nbsp; an instance;<br>
	 * &nbsp; &#8226; &nbsp; a non-associative array;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Arrayable</code> interface.
	 * 
	 * @see \Dracodeum\Kit\Interfaces\Arrayable
	 * @param mixed $value [reference]
	 * <p>The value to evaluate (validate and sanitize).</p>
	 * @param \Dracodeum\Kit\Primitives\Vector|null $template [default = null]
	 * <p>The template instance to clone from and evaluate into.</p>
	 * @param bool|null $clone_recursive [default = null]
	 * <p>Clone the given value recursively.<br>
	 * If set to boolean <code>false</code> and an instance is given, then clone it into a new one with the same values 
	 * and evaluator functions, but not recursively.<br>
	 * If not set, then the given value is not cloned.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to evaluate as <code>null</code>.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value was successfully evaluated into an instance.</p>
	 */
	final public static function evaluate(
		&$value, ?Vector $template = null, ?bool $clone_recursive = null, bool $nullable = false
	): bool
	{
		return self::processCoercion($value, $template, $clone_recursive, $nullable, true);
	}
	
	/**
	 * Coerce a given value into an instance.
	 * 
	 * Only the following types and formats can be coerced into an instance:<br>
	 * &nbsp; &#8226; &nbsp; an instance;<br>
	 * &nbsp; &#8226; &nbsp; a non-associative array;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Arrayable</code> interface.
	 * 
	 * @see \Dracodeum\Kit\Interfaces\Arrayable
	 * @param mixed $value
	 * <p>The value to coerce (validate and sanitize).</p>
	 * @param \Dracodeum\Kit\Primitives\Vector|null $template [default = null]
	 * <p>The template instance to clone from and coerce into.</p>
	 * @param bool|null $clone_recursive [default = null]
	 * <p>Clone the given value recursively.<br>
	 * If set to boolean <code>false</code> and an instance is given, then clone it into a new one with the same values 
	 * and evaluator functions, but not recursively.<br>
	 * If not set, then the given value is not cloned.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @throws \Dracodeum\Kit\Primitives\Vector\Exceptions\CoercionFailed
	 * @return static|null
	 * <p>The given value coerced into an instance.<br>
	 * If nullable, then <code>null</code> may also be returned.</p>
	 */
	final public static function coerce(
		$value, ?Vector $template = null, ?bool $clone_recursive = null, bool $nullable = false
	): ?Vector
	{
		self::processCoercion($value, $template, $clone_recursive, $nullable);
		return $value;
	}
	
	/**
	 * Process the coercion of a given value into an instance.
	 * 
	 * Only the following types and formats can be coerced into an instance:<br>
	 * &nbsp; &#8226; &nbsp; an instance;<br>
	 * &nbsp; &#8226; &nbsp; a non-associative array;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Arrayable</code> interface.
	 * 
	 * @see \Dracodeum\Kit\Interfaces\Arrayable
	 * @param mixed $value [reference]
	 * <p>The value to process (validate and sanitize).</p>
	 * @param \Dracodeum\Kit\Primitives\Vector|null $template [default = null]
	 * <p>The template instance to clone from and coerce into.</p>
	 * @param bool|null $clone_recursive [default = null]
	 * <p>Clone the given value recursively.<br>
	 * If set to boolean <code>false</code> and an instance is given, then clone it into a new one with the same values 
	 * and evaluator functions, but not recursively.<br>
	 * If not set, then the given value is not cloned.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Primitives\Vector\Exceptions\CoercionFailed
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value was successfully coerced into an instance.</p>
	 */
	final public static function processCoercion(
		&$value, ?Vector $template = null, ?bool $clone_recursive = null, bool $nullable = false, bool $no_throw = false
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
				'vector' => static::class,
				'error_code' => Exceptions\CoercionFailed::ERROR_CODE_NULL,
				'error_message' => "A null value is not allowed."
			]);
		}
		
		//coerce
		try {
			//object
			if (!isset($template) && is_object($value) && $value instanceof Vector) {
				if ($clone_recursive !== null) {
					$value = $value->clone($clone_recursive);
				}
				return true;
			}
			
			//array
			$array = $value;
			if (UData::evaluate($array, null, true)) {
				if ($clone_recursive === true) {
					$array = UType::cloneValue($array, true);
				}
				$value = isset($template) ? $template->clone()->setAll($array) : static::build($array);
				return true;
			}
			
		} catch (\Exception $exception) {
			if ($no_throw) {
				return false;
			}
			throw new Exceptions\CoercionFailed([
				'value' => $value,
				'vector' => static::class,
				'error_code' => Exceptions\CoercionFailed::ERROR_CODE_BUILD_EXCEPTION,
				'error_message' => $exception->getMessage()
			]);
		}
		
		//finalize
		if ($no_throw) {
			return false;
		}
		throw new Exceptions\CoercionFailed([
			'value' => $value,
			'vector' => static::class,
			'error_code' => Exceptions\CoercionFailed::ERROR_CODE_INVALID_TYPE,
			'error_message' => "Only the following types and formats can be coerced into an instance:\n" . 
				" - an instance;\n" . 
				" - a non-associative array;\n" . 
				" - an object implementing the \"Dracodeum\\Kit\\Interfaces\\Arrayable\" interface."
		]);
	}
	
	
	
	//Final protected methods
	/**
	 * Reset.
	 * 
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final protected function reset(): Vector
	{
		if (empty($this->values)) {
			$this->min_index = $this->max_index = $this->cursor = null;
		} else {
			if (!array_key_exists(0, $this->values)) {
				$this->values = array_values($this->values);
			}
			if (isset($this->cursor) && isset($this->min_index)) {
				$this->cursor -= $this->min_index;
			}
			$this->min_index = 0;
			$this->max_index = count($this->values) - 1;
		}
		return $this;
	}
}
