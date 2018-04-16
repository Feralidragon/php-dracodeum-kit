<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit;

use Feralygon\Kit\Interfaces\{
	Arrayable as IArrayable,
	Stringifiable as IStringifiable
};
use Feralygon\Kit\Structure\Exceptions;
use Feralygon\Kit\Options\Text as TextOptions;
use Feralygon\Kit\Utilities\{
	Text as UText,
	Type as UType
};

/**
 * This class is the base to be extended from when creating a structure.
 * 
 * A structure is a simple object which represents and stores multiple properties of multiple types.<br>
 * Each and every single one of its properties is validated and sanitized, guaranteeing its type and integrity, 
 * and may be retrieved and modified directly just like any public object property.<br>
 * <br>
 * It may also be set to read-only during instantiation to prevent any further changes.
 * 
 * @since 1.0.0
 * @see https://en.wikipedia.org/wiki/Struct_(C_programming_language)
 */
abstract class Structure implements \ArrayAccess, \JsonSerializable, IArrayable, IStringifiable
{
	//Traits
	use Traits\Properties\ArrayableAccess;
	use Traits\Readonly;
	use Traits\Stringifiable;
	
	
	
	//Final public magic methods
	/**
	 * Instantiate class.
	 * 
	 * @since 1.0.0
	 * @param array $properties [default = []]
	 * <p>The properties, as <samp>name => value</samp> pairs.</p>
	 * @param bool $readonly [default = false]
	 * <p>Set as read-only.</p>
	 */
	final public function __construct(array $properties = [], bool $readonly = false)
	{
		//properties
		$mode = $readonly ? 'r+' : 'rw';
		$this->initializeProperties(\Closure::fromCallable([$this, 'loadProperties']), $properties, $mode);
		
		//read-only
		$this->initializeReadonly(
			$readonly,
			$readonly ? [] : [\Closure::fromCallable([$this, 'setPropertiesAsReadonly'])]
		);
	}
	
	
	
	//Abstract protected methods
	/**
	 * Load properties.
	 * 
	 * @since 1.0.0
	 * @return void
	 */
	abstract protected function loadProperties() : void;
	
	
	
	//Implemented final public methods (JsonSerializable)
	/** {@inheritdoc} */
	final public function jsonSerialize()
	{
		return $this->getAll();
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Interfaces\Stringifiable)
	/** {@inheritdoc} */
	public function toString(?TextOptions $text_options = null) : string
	{
		return UText::stringify($this->getAll(), $text_options);
	}
	
	
	
	//Final public methods
	/**
	 * Clone into a new instance.
	 * 
	 * The returning cloned instance is just a new instance with the same properties.
	 * 
	 * @since 1.0.0
	 * @param bool $readonly [default = false]
	 * <p>Set the new cloned instance as read-only.</p>
	 * @return static
	 * <p>The new cloned instance from this one.</p>
	 */
	final public function clone(bool $readonly = false) : Structure
	{
		return new static($this->getAll(), $readonly);
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
	 * <p>If an instance is given, clone it into a new one with the same properties.</p>
	 * @param bool $readonly [default = false]
	 * <p>Evaluate into a read-only instance.<br>
	 * If an instance is given and is not read-only, a new one is created with the same properties and as read-only.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value was successfully evaluated into an instance.</p>
	 */
	final public static function evaluate(&$value, bool $clone = false, bool $readonly = false) : bool
	{
		try {
			$value = static::coerce($value, $clone, $readonly);
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
	 * <p>If an instance is given, clone it into a new one with the same properties.</p>
	 * @param bool $readonly [default = false]
	 * <p>Coerce into a read-only instance.<br>
	 * If an instance is given and is not read-only, a new one is created with the same properties and as read-only.</p>
	 * @throws \Feralygon\Kit\Structure\Exceptions\CoercionFailed
	 * @return static
	 * <p>The given value coerced into an instance.</p>
	 */
	final public static function coerce($value, bool $clone = false, bool $readonly = false) : Structure
	{
		//coerce
		try {
			if (!isset($value)) {
				return new static([], $readonly);
			} elseif (is_array($value)) {
				return new static($value, $readonly);
			} elseif (is_object($value) && $value instanceof Structure) {
				return $clone || ($readonly && !$value->isReadonly()) || !UType::isA($value, static::class)
					? new static($value->getAll(), $readonly)
					: $value;
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
}
