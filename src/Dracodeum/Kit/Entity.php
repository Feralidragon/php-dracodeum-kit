<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit;

use Dracodeum\Kit\Interfaces\{
	DebugInfo as IDebugInfo,
	Propertiesable as IPropertiesable,
	Arrayable as IArrayable,
	Readonlyable as IReadonlyable,
	ArrayInstantiable as IArrayInstantiable,
	Stringifiable as IStringifiable,
	StringInstantiable as IStringInstantiable,
	Cloneable as ICloneable
};
use Dracodeum\Kit\Traits\DebugInfo\Interfaces\DebugInfoProcessor as IDebugInfoProcessor;
use Dracodeum\Kit\Entity\{
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
 * This class is the base to be extended from when creating an entity.
 * 
 * An entity represents an object with a name, a unique identifier and multiple properties of multiple types, 
 * on which all persistent CRUD operations may be performed: create (insert), read (check and load), update and 
 * delete.<br>
 * <br>
 * As such, it may be used to verify, expose, modify or remove a database record, a resource from a remote service, 
 * among others, using a common class interface.<br>
 * <br>
 * All persistent operations are optimized to only persist changes, as well prevent changes to protected properties, 
 * with each and every single one of its properties validated and sanitized, guaranteeing its type and integrity, 
 * which may be got and set directly just like any public object property.<br>
 * <br>
 * Additional internal functions may also be called automatically upon the persistence of a property value change.<br>
 * <br>
 * It may also be set as read-only to prevent any further changes.
 * 
 * @see https://en.wikipedia.org/wiki/Entity%E2%80%93relationship_model
 * @see \Dracodeum\Kit\Entity\Traits\DefaultBuilder
 */
abstract class Entity
implements IDebugInfo, IDebugInfoProcessor, IPropertiesable, \ArrayAccess, IArrayable, \JsonSerializable, IReadonlyable,
IArrayInstantiable, IStringifiable, IStringInstantiable, ICloneable
{
	//Traits
	use KitTraits\DebugInfo;
	use KitTraits\DebugInfo\ReadonlyPropertiesDumpProcessor;
	use KitTraits\Properties;
	use KitTraits\Properties\Arrayable;
	use KitTraits\Properties\ArrayAccess;
	use KitTraits\Readonly;
	use KitTraits\Stringifiable;
	use KitTraits\CloneableOnly;
	use Traits\DefaultBuilder;
	
	
	
	//Final public magic methods
	/**
	 * Instantiate class.
	 * 
	 * @param array $properties [default = []]
	 * <p>The properties to instantiate with, as <samp>name => value</samp> pairs.<br>
	 * Required properties may also be given as an array of values (<samp>[value1, value2, ...]</samp>), 
	 * in the same order as how these properties were first declared.</p>
	 * @param bool $persisted [default = false]
	 * <p>Set as having already been persisted at least once.</p>
	 */
	final public function __construct(array $properties = [], bool $persisted = false)
	{
		//properties
		$this->initializeProperties(\Closure::fromCallable([$this, 'loadProperties']), $properties, 'rw', $persisted);
		
		//read-only
		$this->addReadonlyCallback(function (bool $recursive): void {
			//properties
			$this->setPropertiesAsReadonly();
			
			//recursive
			if ($recursive) {
				foreach ($this->getAll() as $value) {
					if (is_object($value) && $value instanceof IReadonlyable) {
						$value->setAsReadonly($recursive);
					}
				}
			}
		});
	}
	
	
	
	//Abstract public static methods
	/**
	 * Get name.
	 * 
	 * @return string
	 * <p>The name.</p>
	 */
	abstract public static function getName(): string;
	
	
	
	//Abstract protected methods
	/**
	 * Load properties.
	 * 
	 * @return void
	 */
	abstract protected function loadProperties(): void;
	
	
	
	//Abstract protected static methods
	/**
	 * Get unique identifier property name.
	 * 
	 * @return string
	 * <p>The unique identifier property name.</p>
	 */
	abstract protected static function getUidPropertyName(): string;
	
	
	
	//Implemented final public methods (JsonSerializable)
	/** {@inheritdoc} */
	final public function jsonSerialize()
	{
		return $this->getAll();
	}
	
	
	
	//Implemented final public static methods (Dracodeum\Kit\Interfaces\ArrayInstantiable)
	/** {@inheritdoc} */
	final public static function fromArray(array $array): object
	{
		return static::build($array);
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Interfaces\Stringifiable)
	/** {@inheritdoc} */
	public function toString(?TextOptions $text_options = null): string
	{
		return UText::stringify($this->getAll(), $text_options);
	}
	
	
	
	//Implemented final public methods (Dracodeum\Kit\Interfaces\Cloneable)
	/** {@inheritdoc} */
	final public function clone(bool $recursive = false): object
	{
		$properties = $this->getAllInitializeable();
		return new static($recursive ? UType::cloneValue($properties, $recursive) : $properties, $this->isPersisted());
	}
	
	
	
	//Final public methods
	/**
	 * Check if has already been persisted at least once.
	 * 
	 * @return bool
	 * <p>Boolean <code>true</code> if has already been persisted at least once.</p>
	 */
	final public function isPersisted(): bool
	{
		return $this->arePropertiesPersisted();
	}
	
	
	
	//Final public static methods
	/**
	 * Build instance.
	 * 
	 * @param array $properties [default = []]
	 * <p>The properties to build with, as <samp>name => value</samp> pairs.</p>
	 * @param bool $persisted [default = false]
	 * <p>Set as having already been persisted at least once.</p>
	 * @return static
	 * <p>The built instance.</p>
	 */
	final public static function build(array $properties = [], bool $persisted = false): Entity
	{
		$builder = static::getDefaultBuilder();
		if ($builder !== null) {
			UCall::assert('builder', $builder, function (array $properties, bool $persisted): Entity {});
			return $builder($properties, $persisted);
		}
		return new static($properties, $persisted);
	}
	
	/**
	 * Evaluate a given value as an instance.
	 * 
	 * Only the following types and formats can be evaluated into an instance:<br>
	 * &nbsp; &#8226; &nbsp; <code>null</code>, a string or an instance;<br>
	 * &nbsp; &#8226; &nbsp; an array of properties, given as <samp>name => value</samp> pairs;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Arrayable</code> interface.
	 * 
	 * @see \Dracodeum\Kit\Interfaces\Arrayable
	 * @param mixed $value [reference]
	 * <p>The value to evaluate (validate and sanitize).</p>
	 * @param bool $persisted [default = false]
	 * <p>Set as having already been persisted at least once.</p>
	 * @param bool $clone [default = false]
	 * <p>If an instance is given, then clone it into a new one with the same properties.</p>
	 * @param callable|null $builder [default = null]
	 * <p>The function to use to build an instance with a given set of properties.<br>
	 * It is expected to be compatible with the following signature:<br>
	 * <br>
	 * <code>function (array $properties): Dracodeum\Kit\Entity</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>array $properties</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The properties to build with, as <samp>name => value</samp> pairs.<br>
	 * &nbsp; &nbsp; &nbsp; Required properties may also be given as an array of values 
	 * (<samp>[value1, value2, ...]</samp>), in the same order as how these properties were first declared.<br>
	 * <br>
	 * Return: <code><b>Dracodeum\Kit\Entity</b></code><br>
	 * The built instance with the given set of properties.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to evaluate as <code>null</code>.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value was successfully evaluated into an instance.</p>
	 */
	final public static function evaluate(
		&$value, bool $persisted = false, bool $clone = false, ?callable $builder = null, bool $nullable = false
	): bool
	{
		return self::processCoercion($value, $persisted, $clone, $builder, $nullable, true);
	}
	
	/**
	 * Coerce a given value into an instance.
	 * 
	 * Only the following types and formats can be coerced into an instance:<br>
	 * &nbsp; &#8226; &nbsp; <code>null</code>, a string or an instance;<br>
	 * &nbsp; &#8226; &nbsp; an array of properties, given as <samp>name => value</samp> pairs;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Arrayable</code> interface.
	 * 
	 * @see \Dracodeum\Kit\Interfaces\Arrayable
	 * @param mixed $value
	 * <p>The value to coerce (validate and sanitize).</p>
	 * @param bool $persisted [default = false]
	 * <p>Set as having already been persisted at least once.</p>
	 * @param bool $clone [default = false]
	 * <p>If an instance is given, then clone it into a new one with the same properties.</p>
	 * @param callable|null $builder [default = null]
	 * <p>The function to use to build an instance with a given set of properties.<br>
	 * It is expected to be compatible with the following signature:<br>
	 * <br>
	 * <code>function (array $properties): Dracodeum\Kit\Entity</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>array $properties</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The properties to build with, as <samp>name => value</samp> pairs.<br>
	 * &nbsp; &nbsp; &nbsp; Required properties may also be given as an array of values 
	 * (<samp>[value1, value2, ...]</samp>), in the same order as how these properties were first declared.<br>
	 * <br>
	 * Return: <code><b>Dracodeum\Kit\Entity</b></code><br>
	 * The built instance with the given set of properties.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @throws \Dracodeum\Kit\Entity\Exceptions\CoercionFailed
	 * @return static|null
	 * <p>The given value coerced into an instance.<br>
	 * If nullable, then <code>null</code> may also be returned.</p>
	 */
	final public static function coerce(
		$value, bool $persisted = false, bool $clone = false, ?callable $builder = null, bool $nullable = false
	): ?Entity
	{
		self::processCoercion($value, $persisted, $clone, $builder, $nullable);
		return $value;
	}
	
	/**
	 * Process the coercion of a given value into an instance.
	 * 
	 * Only the following types and formats can be coerced into an instance:<br>
	 * &nbsp; &#8226; &nbsp; <code>null</code>, a string or an instance;<br>
	 * &nbsp; &#8226; &nbsp; an array of properties, given as <samp>name => value</samp> pairs;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Arrayable</code> interface.
	 * 
	 * @see \Dracodeum\Kit\Interfaces\Arrayable
	 * @param mixed $value [reference]
	 * <p>The value to process (validate and sanitize).</p>
	 * @param bool $persisted [default = false]
	 * <p>Set as having already been persisted at least once.</p>
	 * @param bool $clone [default = false]
	 * <p>If an instance is given, then clone it into a new one with the same properties.</p>
	 * @param callable|null $builder [default = null]
	 * <p>The function to use to build an instance with a given set of properties.<br>
	 * It is expected to be compatible with the following signature:<br>
	 * <br>
	 * <code>function (array $properties): Dracodeum\Kit\Entity</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>array $properties</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The properties to build with, as <samp>name => value</samp> pairs.<br>
	 * &nbsp; &nbsp; &nbsp; Required properties may also be given as an array of values 
	 * (<samp>[value1, value2, ...]</samp>), in the same order as how these properties were first declared.<br>
	 * <br>
	 * Return: <code><b>Dracodeum\Kit\Entity</b></code><br>
	 * The built instance with the given set of properties.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Entity\Exceptions\CoercionFailed
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value was successfully coerced into an instance.</p>
	 */
	final public static function processCoercion(
		&$value, bool $persisted = false, bool $clone = false, ?callable $builder = null, bool $nullable = false,
		bool $no_throw = false
	): bool
	{
		//builder
		if ($builder !== null) {
			UCall::assert('builder', $builder, function (array $properties, bool $persisted): Entity {});
		} else {
			$builder = [static::class, 'build'];
		}
		
		//coerce
		try {
			if ($value === null && $nullable) {
				return true;
			} elseif ($value === null || is_array($value)) {
				$properties = $value ?? [];
				$value = UType::coerceObject($builder($properties, $persisted), static::class);
				return true;
			} elseif (is_object($value)) {
				$instance = $value;
				if ($instance instanceof Entity) {
					if (!UType::isA($instance, static::class)) {
						$value = UType::coerceObject($builder($instance->getAll(), $persisted), static::class);
					} elseif ($clone) {
						$value = $value->clone();
					}
					return true;
				} elseif (UData::evaluate($instance)) {
					$value = UType::coerceObject($builder($instance, $persisted), static::class);
					return true;
				}
			}
		} catch (\Exception $exception) {
			if ($no_throw) {
				return false;
			}
			throw new Exceptions\CoercionFailed([
				'value' => $value,
				'entity' => static::class,
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
			'entity' => static::class,
			'error_code' => Exceptions\CoercionFailed::ERROR_CODE_INVALID_TYPE,
			'error_message' => "Only the following types and formats can be coerced into an instance:\n" . 
				" - null, a string or an instance;\n" . 
				" - an array of properties, given as \"name => value\" pairs;\n" . 
				" - an object implementing the \"Dracodeum\\Kit\\Interfaces\\Arrayable\" interface."
		]);
	}
}
