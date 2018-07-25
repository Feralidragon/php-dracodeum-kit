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
use Feralygon\Kit\Primitives\Vector\Exceptions;
use Feralygon\Kit\Traits;
use Feralygon\Kit\Options\Text as TextOptions;
use Feralygon\Kit\Utilities\{
	Call as UCall,
	Data as UData,
	Text as UText
};

/**
 * This primitive represents a vector.
 * 
 * A vector is a simple object which represents and stores a contiguous array.<br>
 * <br>
 * It may also be set as read-only during instantiation to prevent any further changes.
 * 
 * @since 1.0.0
 * @see https://en.wikipedia.org/wiki/Array_data_structure
 * @see https://en.wikipedia.org/wiki/Sequence_container_(C%2B%2B)#Vector
 */
final class Vector
implements \ArrayAccess, \Countable, \JsonSerializable, IArrayable, IArrayInstantiable, IStringifiable
{
	//Traits
	use Traits\Readonly;
	use Traits\Stringifiable;
	use Traits\Evaluators;
	
	
	
	//Private properties
	/** @var array */
	private $array = [];
	
	/** @var int|null */
	private $min_index = null;
	
	/** @var int|null */
	private $max_index = null;
	
	
	
	//Final public magic methods
	/**
	 * Instantiate class.
	 * 
	 * @since 1.0.0
	 * @param array $array [default = []]
	 * <p>The array.</p>
	 * @param bool $readonly [default = false]
	 * <p>Set as read-only.</p>
	 */
	final public function __construct(array $array = [], bool $readonly = false)
	{
		//initialize read-only
		$this->initializeReadonly();
		$this->addReadonlyCallback(function (): void {
			$this->lockEvaluators();
		});
		
		//evaluator callback
		$this->addEvaluatorAdditionCallback(function (callable $evaluator): void {
			//array
			$array = $this->array;
			foreach ($array as $index => &$value) {
				if (!$evaluator($value)) {
					unset($array[$index]);
				}
			}
			unset($value);
			
			//reset
			if ($array !== $this->array) {
				$this->array = array_values($array);
				$this->reset();
			}
		});
		
		//array
		if (!empty($array)) {
			$this->setAll($array);
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
		return count($this->array);
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
	 * The returning cloned instance is a new instance with the same array.
	 * 
	 * @since 1.0.0
	 * @param bool $readonly [default = false]
	 * <p>Set the new cloned instance as read-only.</p>
	 * @return static
	 * <p>The new cloned instance from this one.</p>
	 */
	final public function clone(bool $readonly = false): Vector
	{
		return new static($this->getAll(), $readonly);
	}
	
	/**
	 * Check if has value at a given index.
	 * 
	 * @since 1.0.0
	 * @param int $index
	 * <p>The index to check.<br>
	 * It must be greater than or equal to <code>0</code>.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if has value at the given index.</p>
	 */
	final public function has(int $index): bool
	{
		UCall::guardParameter('index', $index, $index >= 0, [
			'hint_message' => "Only a value greater than or equal to 0 is allowed."
		]);
		return isset($this->min_index) && array_key_exists($this->min_index + $index, $this->array);
	}
	
	/**
	 * Get value from a given index.
	 * 
	 * @since 1.0.0
	 * @param int $index
	 * <p>The index to get from.<br>
	 * It must be greater than or equal to <code>0</code>.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Feralygon\Kit\Primitives\Vector\Exceptions\ValueNotSet
	 * @return mixed
	 * <p>The value from the given index.<br>
	 * If <var>$no_throw</var> is set to <code>true</code>, 
	 * then <code>null</code> may also be returned if none is set.</p>
	 */
	final public function get(int $index, bool $no_throw = false)
	{
		//guard
		UCall::guardParameter('index', $index, $index >= 0, [
			'hint_message' => "Only a value greater than or equal to 0 is allowed."
		]);
		
		//get
		if (!isset($this->min_index) || !array_key_exists($this->min_index + $index, $this->array)) {
			if ($no_throw) {
				return null;
			}
			throw new Exceptions\ValueNotSet(['vector' => $this, 'index' => $index]);
		}
		return $this->array[$this->min_index + $index];
	}
	
	/**
	 * Check if value is set at a given index.
	 * 
	 * @since 1.0.0
	 * @param int $index
	 * <p>The index to check.<br>
	 * It must be greater than or equal to <code>0</code>.</p>
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
	 * @since 1.0.0
	 * @param int $index
	 * <p>The index to set at.<br>
	 * It must be greater than or equal to <code>0</code>.</p>
	 * @param mixed $value
	 * <p>The value to set.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Feralygon\Kit\Primitives\Vector\Exceptions\InvalidIndex
	 * @throws \Feralygon\Kit\Primitives\Vector\Exceptions\InvalidValue
	 * @return $this|bool
	 * <p>This instance, for chaining purposes.<br>
	 * If <var>$no_throw</var> is set to <code>true</code>, 
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
			throw new Exceptions\InvalidIndex(['vector' => $this, 'index' => $index, 'max_index' => $max_index]);
		}
		
		//value
		if (!$this->evaluateValue($value)) {
			if ($no_throw) {
				return false;
			}
			throw new Exceptions\InvalidValue(['vector' => $this, 'value' => $value, 'index' => $index]);
		}
		
		//set
		$this->array[$this->min_index + $index] = $value;
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
	 * Unset value from a given index.
	 * 
	 * @since 1.0.0
	 * @param int $index
	 * <p>The index to unset from.<br>
	 * It must be greater than or equal to <code>0</code>.</p>
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
				unset($this->array[$this->min_index]);
				if (empty($this->array)) {
					$this->reset();
				} else {
					$this->min_index++;
				}
			} elseif ($index === $max_index) {
				unset($this->array[$this->max_index]);
				if (empty($this->array)) {
					$this->reset();
				} else {
					$this->max_index--;
				}
			} elseif ($index < $max_index) {
				unset($this->array[$this->min_index + $index]);
				$this->array = array_values($this->array);
				$this->reset();
			}
		}
		
		//return
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
		if (isset($this->min_index) && $this->min_index > 0) {
			$this->reset();
		}
		return $this->array;
	}
	
	/**
	 * Set all values.
	 * 
	 * @since 1.0.0
	 * @param array $values
	 * <p>The values to set.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Feralygon\Kit\Primitives\Vector\Exceptions\InvalidValues
	 * @throws \Feralygon\Kit\Primitives\Vector\Exceptions\InvalidValue
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
		
		//check
		if (UData::isAssociative($values)) {
			if ($no_throw) {
				return false;
			}
			throw new Exceptions\InvalidValues(['vector' => $this, 'values' => $values]);
		}
		
		//evaluate
		foreach ($values as $index => &$value) {
			if (!$this->evaluateValue($value)) {
				if ($no_throw) {
					return false;
				}
				throw new Exceptions\InvalidValue(['vector' => $this, 'value' => $value, 'index' => $index]);
			}
		}
		unset($value);
		
		//set
		$this->array = $values;
		$this->reset();
		
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
	final public function clear(): Vector
	{
		$this->guardNonReadonlyCall();
		$this->array = [];
		$this->reset();
		return $this;
	}
	
	
	
	//Final public static methods
	/**
	 * Build instance.
	 * 
	 * @since 1.0.0
	 * @param array $array [default = []]
	 * <p>The array to build with.</p>
	 * @param bool $readonly [default = false]
	 * <p>Set the built instance as read-only.</p>
	 * @return static
	 * <p>The built instance.</p>
	 */
	final public static function build(array $array = [], bool $readonly = false): Vector
	{
		return new static($array, $readonly);
	}
	
	
	
	//Final protected methods
	/**
	 * Reset.
	 * 
	 * @since 1.0.0
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final protected function reset(): Vector
	{
		if (empty($this->array)) {
			$this->min_index = $this->max_index = null;
		} else {
			if (!array_key_exists(0, $this->array)) {
				$this->array = array_values($this->array);
			}
			$this->min_index = 0;
			$this->max_index = count($this->array) - 1;
		}
		return $this;
	}
}
