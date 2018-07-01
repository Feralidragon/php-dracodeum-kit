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
use Feralygon\Kit\Primitives\Vector\{
	Traits,
	Exceptions
};
use Feralygon\Kit\Traits as KitTraits;
use Feralygon\Kit\Options\Text as TextOptions;
use Feralygon\Kit\Utilities\{
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
 * It may also be set to read-only during instantiation to prevent any further changes.
 * 
 * @since 1.0.0
 * @see https://en.wikipedia.org/wiki/Array_data_structure
 * @see https://en.wikipedia.org/wiki/Sequence_container_(C%2B%2B)#Vector
 * @see \Feralygon\Kit\Primitives\Vector\Traits\DefaultBuilder
 */
class Vector implements \ArrayAccess, \Countable, \JsonSerializable, IArrayable, IArrayInstantiable, IStringifiable
{
	//Traits
	use KitTraits\Readonly;
	use KitTraits\Stringifiable;
	use Traits\DefaultBuilder;
	
	
	
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
		//TODO
		
		//read-only
		$this->initializeReadonly($readonly);
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
	
	
	
	//Implemented public methods (Feralygon\Kit\Interfaces\Stringifiable)
	/** {@inheritdoc} */
	public function toString(?TextOptions $text_options = null): string
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
		return !empty($this->array) && array_key_exists($this->min_index + $index, $this->array);
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
		if (empty($this->array) || !array_key_exists($this->min_index + $index, $this->array)) {
			if ($no_throw) {
				return null;
			}
			
			//TODO
			
		}
		return $this->array($this->min_index + $index);
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
	 * @return $this
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
		
		//set
		$max_index = empty($this->array) ? 0 : $this->max_index - $this->min_index + 1;
		if ($index > $max_index) {
			if ($no_throw) {
				return false;
			}
			
			//TODO
			
		}
		$this->array[$this->min_index + $index] = $value;
		$this->max_index++;
		
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
		if (!empty($this->array)) {
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
	 * <p>All values.</p>
	 */
	final public function getAll(): array
	{
		if (!empty($this->array) && $this->min_index !== 0) {
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
	 * @return $this
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
			
			//TODO
			
		}
		
		//set
		$this->array = $values;
		if (empty($this->array)) {
			$this->min_index = $this->max_index = null;
		} else {
			$this->min_index = 0;
			$this->max_index = count($this->array) - 1;
		}
		
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
	 * Evaluate a given value as an instance.
	 * 
	 * Only <code>null</code>, an instance or array of properties, given as <samp>name => value</samp> pairs, 
	 * can be evaluated into an instance.
	 * 
	 * @since 1.0.0
	 * @param mixed $value [reference]
	 * <p>The value to evaluate (validate and sanitize).</p>
	 * @param bool $clone [default = false]
	 * <p>If an instance is given, then clone it into a new one with the same properties.</p>
	 * @param bool $readonly [default = false]
	 * <p>Evaluate into a read-only instance.<br>
	 * If an instance is given and is not read-only, 
	 * then a new one is created with the same properties and as read-only.</p>
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
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value was successfully evaluated into an instance.</p>
	 */
	final public static function evaluate(
		&$value, bool $clone = false, bool $readonly = false, ?callable $builder = null
	): bool
	{
		try {
			$value = static::coerce($value, $clone, $readonly, $builder);
		} catch (Exceptions\CoercionFailed $exception) {
			return false;
		}
		return true;
	}
	
	/**
	 * Coerce a given value into an instance.
	 * 
	 * Only <code>null</code>, an instance or array of properties, given as <samp>name => value</samp> pairs, 
	 * can be coerced into an instance.
	 * 
	 * @since 1.0.0
	 * @param mixed $value
	 * <p>The value to coerce (validate and sanitize).</p>
	 * @param bool $clone [default = false]
	 * <p>If an instance is given, then clone it into a new one with the same properties.</p>
	 * @param bool $readonly [default = false]
	 * <p>Coerce into a read-only instance.<br>
	 * If an instance is given and is not read-only, 
	 * then a new one is created with the same properties and as read-only.</p>
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
	 * @throws \Feralygon\Kit\Structure\Exceptions\CoercionFailed
	 * @return static
	 * <p>The given value coerced into an instance.</p>
	 */
	final public static function coerce(
		$value, bool $clone = false, bool $readonly = false, ?callable $builder = null
	): Structure
	{
		//builder
		if (!isset($builder)) {
			$builder = static::getDefaultBuilder();
		}
		if (isset($builder)) {
			UCall::assert('builder', $builder, function (array $properties, bool $readonly): Structure {});
		}
		
		//coerce
		try {
			if (!isset($value) || is_array($value)) {
				return isset($builder)
					? UType::coerceObject($builder($value ?? [], $readonly), static::class)
					: new static($value ?? [], $readonly);
			} elseif (is_object($value) && $value instanceof Vector) {
				if ($clone || ($readonly && !$value->isReadonly())) {
					return new static($value->getAll(), $readonly);
				} elseif (!UType::isA($value, static::class)) {
					return isset($builder)
						? UType::coerceObject($builder($value->getAll(), $readonly), static::class)
						: new static($value->getAll(), $readonly);
				}
				return $value;
			}
		} catch (\Exception $exception) {
			throw new Exceptions\CoercionFailed([
				'value' => $value,
				'structure' => static::class,
				'error_code' => Exceptions\CoercionFailed::ERROR_CODE_BUILD_EXCEPTION,
				'error_message' => $exception->getMessage()
			]);
		}
		
		//throw
		throw new Exceptions\CoercionFailed([
			'value' => $value,
			'structure' => static::class,
			'error_code' => Exceptions\CoercionFailed::ERROR_CODE_INVALID_TYPE,
			'error_message' => "Only null, an instance or array of properties, " . 
				"given as \"name => value\" pairs, can be coerced into an instance."
		]);
	}
	
	
	
	//Final protected methods
	/**
	 * Reset.
	 * 
	 * @since 1.0.0
	 * @return void
	 */
	final protected function reset(): void
	{
		if (empty($this->array)) {
			$this->min_index = $this->max_index = null;
		} else {
			$this->array = array_values($this->array);
			$this->min_index = 0;
			$this->max_index = count($this->array) - 1;
		}
	}
}
