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
	Stringifiable as IStringifiable
};
use Dracodeum\Kit\Traits\DebugInfo\Interfaces\DebugInfoProcessor as IDebugInfoProcessor;
use Dracodeum\Kit\Entity\{
	Traits,
	Exceptions
};
use Dracodeum\Kit\Traits as KitTraits;
use Dracodeum\Kit\Components\Store;
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
 * An entity represents an object with an ID, a name, a scope and multiple properties of multiple types, 
 * on which all persistent CRUD operations may be performed: create (insert), read (exists and load), update and 
 * delete.<br>
 * <br>
 * As such, it may be used to verify, expose, add, modify or remove an object corresponding to a database record, 
 * or a resource from a remote service, or any other type of persistent object, using a common class interface.<br>
 * <br>
 * All persistent operations are optimized to only persist changes, as well prevent changes to protected properties, 
 * with each and every single one of its properties validated and sanitized, guaranteeing its type and integrity, 
 * and which may be got and set directly just like any public object property.<br>
 * <br>
 * Additional internal functions may also be called automatically upon the persistence of a property value change.<br>
 * <br>
 * It may also be set as read-only to prevent any further changes.
 * 
 * @see https://en.wikipedia.org/wiki/Entity%E2%80%93relationship_model
 * @see \Dracodeum\Kit\Entity\Traits\DefaultBuilder
 * @see \Dracodeum\Kit\Entity\Traits\Initializer
 * @see \Dracodeum\Kit\Entity\Traits\IdPropertyName
 * @see \Dracodeum\Kit\Entity\Traits\BaseScope
 */
abstract class Entity
implements IDebugInfo, IDebugInfoProcessor, IPropertiesable, \ArrayAccess, IArrayable, \JsonSerializable, IReadonlyable,
IArrayInstantiable, IStringifiable
{
	//Traits
	use KitTraits\DebugInfo;
	use KitTraits\DebugInfo\ReadonlyPropertiesDumpProcessor;
	use KitTraits\Properties;
	use KitTraits\Properties\Arrayable;
	use KitTraits\Properties\ArrayAccess;
	use KitTraits\Readonly;
	use KitTraits\Stringifiable;
	use KitTraits\Uncloneable;
	use Traits\DefaultBuilder;
	use Traits\Initializer;
	use Traits\IdPropertyName;
	use Traits\BaseScope;
	
	
	
	//Private static properties
	/** @var \Dracodeum\Kit\Components\Store[] */
	private static $stores = [];
	
	
	
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
		
		//initialize
		$this->initialize();
	}
	
	
	
	//Abstract public static methods
	/**
	 * Get name.
	 * 
	 * The returning name is a canonical string which identifies this entity class.
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
	 * Produce store.
	 * 
	 * @return \Dracodeum\Kit\Components\Store|\Dracodeum\Kit\Prototypes\Store|string
	 * <p>The produced store component instance or name, or prototype instance, class or name.</p>
	 */
	abstract protected static function produceStore();
	
	
	
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
	
	
	
	//Protected static methods
	/**
	 * Create a store instance with a given prototype.
	 * 
	 * @param \Dracodeum\Kit\Prototypes\Store|string $prototype
	 * <p>The prototype instance, class or name to create with.</p>
	 * @param array $properties [default = []]
	 * <p>The properties to create with, as <samp>name => value</samp> pairs, if a prototype class or name is given.<br>
	 * Required properties may also be given as an array of values (<samp>[value1, value2, ...]</samp>), 
	 * in the same order as how these properties were first declared.</p>
	 * @return \Dracodeum\Kit\Components\Store
	 * <p>The created store instance with the given prototype.</p>
	 */
	protected static function createStore($prototype, array $properties = []): Store
	{
		return Store::build($prototype, $properties);
	}
	
	
	
	//Final public methods
	/**
	 * Get ID.
	 * 
	 * @return mixed
	 * <p>The ID or <code>null</code> if none is set.</p>
	 */
	final public function getId()
	{
		$name = $this->getIdPropertyName();
		return $name !== null ? $this->get($name) : null;
	}
	
	/**
	 * Get scope.
	 * 
	 * @return string|null
	 * <p>The scope or <code>null</code> if none is set.</p>
	 */
	final public function getScope(): ?string
	{
		//base
		$base = $this->getBaseScope();
		if ($base === null) {
			return null;
		}
		
		//values
		$values = [];
		foreach (UText::placeholders($base, true) as $name) {
			$values[$name] = $this->get($name);
		}
		
		//return
		return $this->getStore()->getUidScope($base, $values);
	}
	
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
	 * <p>The properties to build with, as <samp>name => value</samp> pairs.<br>
	 * Required properties may also be given as an array of values (<samp>[value1, value2, ...]</samp>), 
	 * in the same order as how these properties were first declared.</p>
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
	 * Check if an instance with a given ID exists.
	 * 
	 * @param mixed $id
	 * <p>The ID to check with.</p>
	 * @param array $scope_values [default = []]
	 * <p>The scope values to check with, as <samp>name => value</samp> pairs.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if an instance with the given ID exists.</p>
	 */
	final public static function exists($id, array $scope_values = []): bool
	{
		return static::getStore()->exists([
			'id' => $id,
			'name' => static::getName(),
			'base_scope' => static::getBaseScope(),
			'scope_values' => $scope_values
		]);
	}
	
	/**
	 * Load instance with a given ID.
	 * 
	 * @param mixed $id
	 * <p>The ID to load with.</p>
	 * @param array $scope_values [default = []]
	 * <p>The scope values to load with, as <samp>name => value</samp> pairs.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Entity\Exceptions\NotFound
	 * @return static|null
	 * <p>The loaded instance with the given ID.<br>
	 * If <var>$no_throw</var> is set to boolean <code>true</code>, 
	 * then <code>null</code> is returned if it was not found.</p>
	 */
	final public static function load($id, array $scope_values = [], bool $no_throw = false): ?Entity
	{
		//initialize
		$store = static::getStore();
		$base_scope = static::getBaseScope();
		
		//properties
		$properties = $store->return([
			'id' => $id,
			'name' => static::getName(),
			'base_scope' => $base_scope,
			'scope_values' => $scope_values
		]);
		
		//check
		if ($properties === null) {
			if ($no_throw) {
				return null;
			}
			$scope = $base_scope !== null ? $store->getUidScope($base_scope, $scope_values) : null;
			throw new Exceptions\NotFound([static::class, $id, 'scope' => $scope]);
		}
		
		//return
		return static::build($properties, true);
	}
	
	/**
	 * Evaluate a given value as an instance.
	 * 
	 * Only the following types and formats can be evaluated into an instance:<br>
	 * &nbsp; &#8226; &nbsp; <code>null</code> or an instance;<br>
	 * &nbsp; &#8226; &nbsp; an array of properties, given as <samp>name => value</samp> pairs;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Arrayable</code> interface.
	 * 
	 * @see \Dracodeum\Kit\Interfaces\Arrayable
	 * @param mixed $value [reference]
	 * <p>The value to evaluate (validate and sanitize).</p>
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
	 * <p>Allow the given value to evaluate as <code>null</code>.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value was successfully evaluated into an instance.</p>
	 */
	final public static function evaluate(
		&$value, bool $persisted = false, ?callable $builder = null, bool $nullable = false
	): bool
	{
		return self::processCoercion($value, $persisted, $builder, $nullable, true);
	}
	
	/**
	 * Coerce a given value into an instance.
	 * 
	 * Only the following types and formats can be coerced into an instance:<br>
	 * &nbsp; &#8226; &nbsp; <code>null</code> or an instance;<br>
	 * &nbsp; &#8226; &nbsp; an array of properties, given as <samp>name => value</samp> pairs;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Arrayable</code> interface.
	 * 
	 * @see \Dracodeum\Kit\Interfaces\Arrayable
	 * @param mixed $value
	 * <p>The value to coerce (validate and sanitize).</p>
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
	 * <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @throws \Dracodeum\Kit\Entity\Exceptions\CoercionFailed
	 * @return static|null
	 * <p>The given value coerced into an instance.<br>
	 * If nullable, then <code>null</code> may also be returned.</p>
	 */
	final public static function coerce(
		$value, bool $persisted = false, ?callable $builder = null, bool $nullable = false
	): ?Entity
	{
		self::processCoercion($value, $persisted, $builder, $nullable);
		return $value;
	}
	
	/**
	 * Process the coercion of a given value into an instance.
	 * 
	 * Only the following types and formats can be coerced into an instance:<br>
	 * &nbsp; &#8226; &nbsp; <code>null</code> or an instance;<br>
	 * &nbsp; &#8226; &nbsp; an array of properties, given as <samp>name => value</samp> pairs;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Arrayable</code> interface.
	 * 
	 * @see \Dracodeum\Kit\Interfaces\Arrayable
	 * @param mixed $value [reference]
	 * <p>The value to process (validate and sanitize).</p>
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
	 * <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Entity\Exceptions\CoercionFailed
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value was successfully coerced into an instance.</p>
	 */
	final public static function processCoercion(
		&$value, bool $persisted = false, ?callable $builder = null, bool $nullable = false, bool $no_throw = false
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
				$value = UType::coerceObject($builder($value ?? [], $persisted), static::class);
				return true;
			} elseif (is_object($value)) {
				$instance = $value;
				if ($instance instanceof Entity) {
					if (!UType::isA($instance, static::class)) {
						$value = UType::coerceObject($builder($instance->getAll(), $persisted), static::class);
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
				" - null or an instance;\n" . 
				" - an array of properties, given as \"name => value\" pairs;\n" . 
				" - an object implementing the \"Dracodeum\\Kit\\Interfaces\\Arrayable\" interface."
		]);
	}
	
	
	
	//Final protected static methods
	/**
	 * Get store instance.
	 * 
	 * @return \Dracodeum\Kit\Components\Store
	 * <p>The store instance.</p>
	 */
	final protected static function getStore(): Store
	{
		if (!isset(self::$stores[static::class])) {
			self::$stores[static::class] = UCall::guardExecution(
				\Closure::fromCallable([static::class, 'produceStore']), [],
				function (&$value): bool {
					$value = Store::coerce($value, [], \Closure::fromCallable([static::class, 'createStore']));
					return true;
				}
			);
		}
		return self::$stores[static::class];
	}
}
