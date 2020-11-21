<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit;

use Dracodeum\Kit\Interfaces\{
	DebugInfo as IDebugInfo,
	Properties as IProperties,
	Arrayable as IArrayable,
	Keyable as IKeyable,
	Readonlyable as IReadonlyable,
	Stringable as IStringable,
	IntegerInstantiable as IIntegerInstantiable,
	FloatInstantiable as IFloatInstantiable,
	StringInstantiable as IStringInstantiable,
	CallableInstantiable as ICallableInstantiable,
	ArrayInstantiable as IArrayInstantiable,
	Cloneable as ICloneable
};
use Dracodeum\Kit\Traits\DebugInfo\Interfaces\DebugInfoProcessor as IDebugInfoProcessor;
use Dracodeum\Kit\Structure\{
	Traits,
	Exceptions
};
use Dracodeum\Kit\Traits as KitTraits;
use Dracodeum\Kit\Options\Text as TextOptions;
use Dracodeum\Kit\Utilities\{
	Call as UCall,
	Data as UData,
	Text as UText,
	Type as UType
};

/**
 * This class is the base to be extended from when creating a structure.
 * 
 * A structure is a simple object which represents and stores multiple properties of multiple types.<br>
 * Each and every single one of its properties is validated and sanitized, guaranteeing its type and integrity, 
 * and may be retrieved and set directly just like any public object property.<br>
 * <br>
 * It may also be set as read-only to prevent any further changes.
 * 
 * @see https://en.wikipedia.org/wiki/Struct_(C_programming_language)
 * @see \Dracodeum\Kit\Structure\Traits\DefaultBuilder
 * @see \Dracodeum\Kit\Structure\Traits\PropertiesInitializer
 * @see \Dracodeum\Kit\Structure\Traits\IntegerPropertiesExtractor
 * @see \Dracodeum\Kit\Structure\Traits\FloatPropertiesExtractor
 * @see \Dracodeum\Kit\Structure\Traits\StringPropertiesExtractor
 * @see \Dracodeum\Kit\Structure\Traits\CallablePropertiesExtractor
 */
abstract class Structure
implements IDebugInfo, IDebugInfoProcessor, IProperties, \ArrayAccess, IArrayable, IKeyable, \JsonSerializable,
IReadonlyable, IStringable, IIntegerInstantiable, IFloatInstantiable, IStringInstantiable, ICallableInstantiable,
IArrayInstantiable, ICloneable
{
	//Traits
	use KitTraits\DebugInfo;
	use KitTraits\DebugInfo\ReadonlyPropertiesDumpProcessor;
	use KitTraits\Properties;
	use KitTraits\Properties\Arrayable;
	use KitTraits\Properties\ArrayAccess;
	use KitTraits\Properties\Keyable;
	use KitTraits\Readonly;
	use KitTraits\Stringable;
	use KitTraits\CloneableOnly;
	use Traits\DefaultBuilder;
	use Traits\PropertiesInitializer;
	use Traits\IntegerPropertiesExtractor;
	use Traits\FloatPropertiesExtractor;
	use Traits\StringPropertiesExtractor;
	use Traits\CallablePropertiesExtractor;
	
	
	
	//Final public magic methods
	/**
	 * Instantiate class.
	 * 
	 * @param array $properties [default = []]
	 * <p>The properties to instantiate with, as a set of <samp>name => value</samp> pairs.<br>
	 * Required properties may also be given as an array of values (<samp>[value1, value2, ...]</samp>), 
	 * in the same order as how these properties were first declared.</p>
	 */
	final public function __construct(array $properties = [])
	{
		//properties
		$this->initializePropertiesManager(
			\Closure::fromCallable([$this, 'loadProperties']), $properties, 'rw',
			\Closure::fromCallable([$this, 'initializeProperties'])
		);
		
		//read-only
		$this->addReadonlyCallback(function (bool $recursive): void {
			//properties
			$this->setPropertiesAsReadonly();
			
			//recursive
			if ($recursive) {
				UType::setValueAsReadonly($this->getAll(true), $recursive);
			}
		});
	}
	
	
	
	//Abstract protected methods
	/**
	 * Load properties.
	 * 
	 * @return void
	 */
	abstract protected function loadProperties(): void;
	
	
	
	//Implemented final public methods (JsonSerializable)
	/** {@inheritdoc} */
	final public function jsonSerialize()
	{
		return $this->getAll();
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Interfaces\Stringable)
	/** {@inheritdoc} */
	public function toString(?TextOptions $text_options = null): string
	{
		return UText::stringify($this->getAll(), $text_options);
	}
	
	
	
	//Implemented final public static methods (Dracodeum\Kit\Interfaces\IntegerInstantiable)
	/** {@inheritdoc} */
	final public static function fromInteger(int $integer): object
	{
		return static::build(static::getIntegerProperties($integer));
	}
	
	
	
	//Implemented final public static methods (Dracodeum\Kit\Interfaces\FloatInstantiable)
	/** {@inheritdoc} */
	final public static function fromFloat(float $float): object
	{
		return static::build(static::getFloatProperties($float));
	}
	
	
	
	//Implemented final public static methods (Dracodeum\Kit\Interfaces\StringInstantiable)
	/** {@inheritdoc} */
	final public static function fromString(string $string): object
	{
		return static::build(static::getStringProperties($string));
	}
	
	
	
	//Implemented final public static methods (Dracodeum\Kit\Interfaces\CallableInstantiable)
	/** {@inheritdoc} */
	final public static function fromCallable(callable $callable): object
	{
		return static::build(static::getCallableProperties($callable));
	}
	
	
	
	//Implemented final public static methods (Dracodeum\Kit\Interfaces\ArrayInstantiable)
	/** {@inheritdoc} */
	final public static function fromArray(array $array): object
	{
		return static::build($array);
	}
	
	
	
	//Implemented final public methods (Dracodeum\Kit\Interfaces\Cloneable)
	/** {@inheritdoc} */
	final public function clone(bool $recursive = false): object
	{
		$properties = $this->getAllInitializeable(true);
		return new static($recursive ? UType::cloneValue($properties, $recursive) : $properties);
	}
	
	
	
	//Final public static methods
	/**
	 * Build instance.
	 * 
	 * @param array $properties [default = []]
	 * <p>The properties to build with, as a set of <samp>name => value</samp> pairs.<br>
	 * Required properties may also be given as an array of values (<samp>[value1, value2, ...]</samp>), 
	 * in the same order as how these properties were first declared.</p>
	 * @return static
	 * <p>The built instance.</p>
	 */
	final public static function build(array $properties = []): Structure
	{
		$builder = static::getDefaultBuilder();
		if (isset($builder)) {
			UCall::assert('builder', $builder, function (array $properties): Structure {});
			return $builder($properties);
		}
		return new static($properties);
	}
	
	/**
	 * Get properties from a given integer.
	 * 
	 * @param int $integer
	 * <p>The integer to get from.</p>
	 * @return array
	 * <p>The properties from the given integer, as a set of <samp>name => value</samp> pairs.</p>
	 */
	final public static function getIntegerProperties(int $integer): array
	{
		$properties = static::extractIntegerProperties($integer);
		UCall::guardParameter('integer', $integer, $properties !== null, [
			'error_message' => "No properties could be extracted from the given integer."
		]);
		return $properties;
	}
	
	/**
	 * Get properties from a given float.
	 * 
	 * @param float $float
	 * <p>The float to get from.</p>
	 * @return array
	 * <p>The properties from the given float, as a set of <samp>name => value</samp> pairs.</p>
	 */
	final public static function getFloatProperties(float $float): array
	{
		$properties = static::extractFloatProperties($float);
		UCall::guardParameter('float', $float, $properties !== null, [
			'error_message' => "No properties could be extracted from the given float."
		]);
		return $properties;
	}
	
	/**
	 * Get properties from a given string.
	 * 
	 * @param string $string
	 * <p>The string to get from.</p>
	 * @return array
	 * <p>The properties from the given string, as a set of <samp>name => value</samp> pairs.</p>
	 */
	final public static function getStringProperties(string $string): array
	{
		$properties = static::extractStringProperties($string);
		UCall::guardParameter('string', $string, isset($properties), [
			'error_message' => "No properties could be extracted from the given string."
		]);
		return $properties;
	}
	
	/**
	 * Get properties from a given callable.
	 * 
	 * @param callable $callable
	 * <p>The callable to get from.</p>
	 * @return array
	 * <p>The properties from the given callable, as a set of <samp>name => value</samp> pairs.</p>
	 */
	final public static function getCallableProperties(callable $callable): array
	{
		$properties = static::extractCallableProperties($callable);
		if ($properties === null) {
			UCall::haltParameter('callable', $callable, [
				'error_message' => "No properties could be extracted from the given callable."
			]);
		}
		return $properties;
	}
	
	/**
	 * Evaluate a given value as an instance.
	 * 
	 * Only the following types and formats can be evaluated into an instance:<br>
	 * &nbsp; &#8226; &nbsp; <code>null</code>, an integer, a float, a string, a callable or an instance;<br>
	 * &nbsp; &#8226; &nbsp; an array of properties, given as a set of <samp>name => value</samp> pairs;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Arrayable</code> interface.
	 * 
	 * @see \Dracodeum\Kit\Interfaces\Arrayable
	 * @param mixed $value [reference]
	 * <p>The value to evaluate (validate and sanitize).</p>
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
	 * &nbsp; &nbsp; &nbsp; The properties to build with, as a set of <samp>name => value</samp> pairs.<br>
	 * &nbsp; &nbsp; &nbsp; Required properties may also be given as an array of values 
	 * (<samp>[value1, value2, ...]</samp>), in the same order as how these properties were first declared.<br>
	 * <br>
	 * Return: <code><b>Dracodeum\Kit\Structure</b></code><br>
	 * The built instance with the given set of properties.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to evaluate as <code>null</code>.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value was successfully evaluated into an instance.</p>
	 */
	final public static function evaluate(
		&$value, ?bool $clone_recursive = null, ?callable $builder = null, bool $nullable = false
	): bool
	{
		return self::processCoercion($value, $clone_recursive, $builder, $nullable, true);
	}
	
	/**
	 * Coerce a given value into an instance.
	 * 
	 * Only the following types and formats can be coerced into an instance:<br>
	 * &nbsp; &#8226; &nbsp; <code>null</code>, an integer, a float, a string, a callable or an instance;<br>
	 * &nbsp; &#8226; &nbsp; an array of properties, given as a set of <samp>name => value</samp> pairs;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Arrayable</code> interface.
	 * 
	 * @see \Dracodeum\Kit\Interfaces\Arrayable
	 * @param mixed $value
	 * <p>The value to coerce (validate and sanitize).</p>
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
	 * &nbsp; &nbsp; &nbsp; The properties to build with, as a set of <samp>name => value</samp> pairs.<br>
	 * &nbsp; &nbsp; &nbsp; Required properties may also be given as an array of values 
	 * (<samp>[value1, value2, ...]</samp>), in the same order as how these properties were first declared.<br>
	 * <br>
	 * Return: <code><b>Dracodeum\Kit\Structure</b></code><br>
	 * The built instance with the given set of properties.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @throws \Dracodeum\Kit\Structure\Exceptions\CoercionFailed
	 * @return static|null
	 * <p>The given value coerced into an instance.<br>
	 * If nullable, then <code>null</code> may also be returned.</p>
	 */
	final public static function coerce(
		$value, ?bool $clone_recursive = null, ?callable $builder = null, bool $nullable = false
	): ?Structure
	{
		self::processCoercion($value, $clone_recursive, $builder, $nullable);
		return $value;
	}
	
	/**
	 * Process the coercion of a given value into an instance.
	 * 
	 * Only the following types and formats can be coerced into an instance:<br>
	 * &nbsp; &#8226; &nbsp; <code>null</code>, an integer, a float, a string, a callable or an instance;<br>
	 * &nbsp; &#8226; &nbsp; an array of properties, given as a set of <samp>name => value</samp> pairs;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Arrayable</code> interface.
	 * 
	 * @see \Dracodeum\Kit\Interfaces\Arrayable
	 * @param mixed $value [reference]
	 * <p>The value to process (validate and sanitize).</p>
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
	 * &nbsp; &nbsp; &nbsp; The properties to build with, as a set of <samp>name => value</samp> pairs.<br>
	 * &nbsp; &nbsp; &nbsp; Required properties may also be given as an array of values 
	 * (<samp>[value1, value2, ...]</samp>), in the same order as how these properties were first declared.<br>
	 * <br>
	 * Return: <code><b>Dracodeum\Kit\Structure</b></code><br>
	 * The built instance with the given set of properties.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Structure\Exceptions\CoercionFailed
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value was successfully coerced into an instance.</p>
	 */
	final public static function processCoercion(
		&$value, ?bool $clone_recursive = null, ?callable $builder = null, bool $nullable = false,
		bool $no_throw = false
	): bool
	{
		//builder
		if ($builder !== null) {
			UCall::assert('builder', $builder, function (array $properties): Structure {});
		} else {
			$builder = [static::class, 'build'];
		}
		
		//coerce
		try {
			if ($value === null && $nullable) {
				return true;
			} elseif (
				$value === null || is_int($value) || is_float($value) || is_string($value) || is_array($value) || 
				is_callable($value)
			) {
				$properties = [];
				if (is_int($value)) {
					$properties = static::getIntegerProperties($value);
				} elseif (is_float($value)) {
					$properties = static::getFloatProperties($value);
				} elseif (is_string($value)) {
					$properties = static::getStringProperties($value);
				} elseif (is_array($value)) {
					$properties = $value;
					if ($clone_recursive === true) {
						$properties = UType::cloneValue($properties, true);
					}
				} elseif (is_callable($value)) {
					$properties = static::getCallableProperties($value);
				}
				$value = UType::coerceObject($builder($properties), static::class);
				return true;
			} elseif (is_object($value)) {
				$instance = $value;
				if ($instance instanceof Structure) {
					if (!UType::isA($instance, static::class)) {
						$properties = $instance->getAll(true);
						if ($clone_recursive === true) {
							$properties = UType::cloneValue($properties, true);
						}
						$value = UType::coerceObject($builder($properties), static::class);
					} elseif ($clone_recursive !== null) {
						$value = $value->clone($clone_recursive);
					}
					return true;
				} elseif (UData::evaluate($instance)) {
					$properties = $instance;
					if ($clone_recursive === true) {
						$properties = UType::cloneValue($properties, true);
					}
					$value = UType::coerceObject($builder($properties), static::class);
					return true;
				}
			}
		} catch (\Exception $exception) {
			if ($no_throw) {
				return false;
			}
			throw new Exceptions\CoercionFailed([
				'value' => $value,
				'structure' => static::class,
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
			'structure' => static::class,
			'error_code' => Exceptions\CoercionFailed::ERROR_CODE_INVALID_TYPE,
			'error_message' => "Only the following types and formats can be coerced into an instance:\n" . 
				" - null, an integer, a float, a string, a callable or an instance;\n" . 
				" - an array of properties, given as a set of \"name => value\" pairs;\n" . 
				" - an object implementing the \"Dracodeum\\Kit\\Interfaces\\Arrayable\" interface."
		]);
	}
}
