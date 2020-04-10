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
	Keyable as IKeyable,
	Readonlyable as IReadonlyable,
	Persistable as IPersistable,
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
use Dracodeum\Kit\Components\Store\Structures\Uid;
use Dracodeum\Kit\Components\Store\Structures\Uid\Exceptions as UidExceptions;
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
 * @see \Dracodeum\Kit\Entity\Traits\PreInsertProcessor
 * @see \Dracodeum\Kit\Entity\Traits\PostInsertProcessor
 * @see \Dracodeum\Kit\Entity\Traits\PreUpdateProcessor
 * @see \Dracodeum\Kit\Entity\Traits\PostUpdateProcessor
 * @see \Dracodeum\Kit\Entity\Traits\PreDeleteProcessor
 * @see \Dracodeum\Kit\Entity\Traits\PostDeleteProcessor
 */
abstract class Entity
implements IDebugInfo, IDebugInfoProcessor, IPropertiesable, \ArrayAccess, IArrayable, IKeyable, \JsonSerializable,
IReadonlyable, IPersistable, IArrayInstantiable, IStringifiable
{
	//Traits
	use KitTraits\DebugInfo;
	use KitTraits\DebugInfo\ReadonlyPropertiesDumpProcessor;
	use KitTraits\Properties;
	use KitTraits\Properties\Arrayable;
	use KitTraits\Properties\ArrayAccess;
	use KitTraits\Properties\Keyable;
	use KitTraits\Readonly;
	use KitTraits\Stringifiable;
	use KitTraits\Uncloneable;
	use Traits\DefaultBuilder;
	use Traits\Initializer;
	use Traits\IdPropertyName;
	use Traits\BaseScope;
	use Traits\PreInsertProcessor;
	use Traits\PostInsertProcessor;
	use Traits\PreUpdateProcessor;
	use Traits\PostUpdateProcessor;
	use Traits\PreDeleteProcessor;
	use Traits\PostDeleteProcessor;
	
	
	
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
				UType::setValueAsReadonly($this->getAll(true), $recursive);
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
	
	
	
	//Implemented final public methods (Dracodeum\Kit\Interfaces\Persistable)
	/** {@inheritdoc} */
	final public function isPersisted(bool $recursive = false): bool
	{
		return $this->arePropertiesPersisted($recursive);
	}
	
	/**
	 * {@inheritdoc}
	 * @throws \Dracodeum\Kit\Entity\Exceptions\Conflict
	 * @throws \Dracodeum\Kit\Entity\Exceptions\NotFound
	 */
	final public function persist(bool $recursive = false): object
	{
		$this->persistProperties(
			\Closure::fromCallable([$this, 'insert']), \Closure::fromCallable([$this, 'update']), false, $recursive
		);
		return $this;
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
	 * @return int|float|string|null
	 * <p>The ID or <code>null</code> if none is set.</p>
	 */
	final public function getId()
	{
		$name = $this->getIdPropertyName();
		if ($name !== null) {
			$id = $this->get($name);
			if (!self::evaluateId($id)) {
				UCall::haltInternal([
					'error_message' => "Invalid ID {{id}} in entity {{entity}}.",
					'hint_message' => "Only an integer, float or string ID is allowed.",
					'parameters' => ['id' => $id, 'entity' => $this]
				]);
			}
			return $id;
		}
		return null;
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
	 * Check if has ID.
	 * 
	 * @return bool
	 * <p>Boolean <code>true</code> if has ID.</p>
	 */
	final public static function hasId(): bool
	{
		return static::getIdPropertyName() !== null;
	}
	
	/**
	 * Check if an instance exists.
	 * 
	 * @param int|float|string|null $id [default = null]
	 * <p>The ID to check with.</p>
	 * @param array $scope_values [default = []]
	 * <p>The scope values to check with, as <samp>name => value</samp> pairs.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if an instance exists.</p>
	 */
	final public static function exists($id = null, array $scope_values = []): bool
	{
		return static::getStore()->exists([
			'id' => static::coerceId($id),
			'name' => static::getName(),
			'base_scope' => static::getBaseScope(),
			'scope_values' => $scope_values
		]);
	}
	
	/**
	 * Load instance.
	 * 
	 * @param int|float|string|null $id [default = null]
	 * <p>The ID to load with.</p>
	 * @param array $scope_values [default = []]
	 * <p>The scope values to load with, as <samp>name => value</samp> pairs.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Entity\Exceptions\NotFound
	 * @return static|null
	 * <p>The loaded instance.<br>
	 * If <var>$no_throw</var> is set to boolean <code>true</code>, 
	 * then <code>null</code> is returned if it was not found.</p>
	 */
	final public static function load($id = null, array $scope_values = [], bool $no_throw = false): ?Entity
	{
		$properties = static::loadPropertyValues(static::coerceId($id), $scope_values, $no_throw);
		return $properties !== null ? static::build($properties, true) : null;
	}
	
	/**
	 * Delete instance.
	 * 
	 * @param int|float|string|null $id [default = null]
	 * <p>The ID to delete with.</p>
	 * @param array $scope_values [default = []]
	 * <p>The scope values to delete with, as <samp>name => value</samp> pairs.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Entity\Exceptions\NotFound
	 * @return void|bool
	 * <p>If <var>$no_throw</var> is set to boolean <code>true</code>, 
	 * then boolean <code>true</code> is returned if the instance was found and deleted, 
	 * or boolean <code>false</code> if otherwise.</p>
	 */
	final public static function delete($id = null, array $scope_values = [], bool $no_throw = false)
	{
		//initialize
		$id = static::coerceId($id);
		$store = static::getStore();
		$base_scope = static::getBaseScope();
		
		//pre-delete
		static::processPreDelete($id, $scope_values);
		
		//delete
		$deleted = $store->delete([
			'id' => $id,
			'name' => static::getName(),
			'base_scope' => $base_scope,
			'scope_values' => $scope_values
		], true);
		
		//check
		if (!$deleted) {
			if ($no_throw) {
				return false;
			}
			$scope = $base_scope !== null ? $store->getUidScope($base_scope, $scope_values) : null;
			throw new Exceptions\NotFound([static::class, $id, 'scope' => $scope]);
		}
		
		//post-delete
		static::processPostDelete($id, $scope_values);
		
		//return
		if ($no_throw) {
			return true;
		}
	}
	
	/**
	 * Evaluate a given value as an instance.
	 * 
	 * Only the following types and formats can be evaluated into an instance:<br>
	 * &nbsp; &#8226; &nbsp; <code>null</code> or an instance;<br>
	 * &nbsp; &#8226; &nbsp; an integer, float or string, given as an ID to be loaded from;<br>
	 * &nbsp; &#8226; &nbsp; an array of properties, given as <samp>name => value</samp> pairs;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Arrayable</code> interface;<br>
	 * &nbsp; &#8226; &nbsp; an instance of <code>Dracodeum\Kit\Components\Store\Structures\Uid</code>, 
	 * to be loaded from;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>__toString</code> method, 
	 * given as an ID to be loaded from;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Stringifiable</code> interface, 
	 * given as an ID to be loaded from.
	 * 
	 * @see \Dracodeum\Kit\Interfaces\Arrayable
	 * @see \Dracodeum\Kit\Components\Store\Structures\Uid
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
	 * &nbsp; &#8226; &nbsp; an integer, float or string, given as an ID to be loaded from;<br>
	 * &nbsp; &#8226; &nbsp; an array of properties, given as <samp>name => value</samp> pairs;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Arrayable</code> interface;<br>
	 * &nbsp; &#8226; &nbsp; an instance of <code>Dracodeum\Kit\Components\Store\Structures\Uid</code>, 
	 * to be loaded from;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>__toString</code> method, 
	 * given as an ID to be loaded from;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Stringifiable</code> interface, 
	 * given as an ID to be loaded from.
	 * 
	 * @see \Dracodeum\Kit\Interfaces\Arrayable
	 * @see \Dracodeum\Kit\Components\Store\Structures\Uid
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
	 * &nbsp; &#8226; &nbsp; an integer, float or string, given as an ID to be loaded from;<br>
	 * &nbsp; &#8226; &nbsp; an array of properties, given as <samp>name => value</samp> pairs;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Arrayable</code> interface;<br>
	 * &nbsp; &#8226; &nbsp; an instance of <code>Dracodeum\Kit\Components\Store\Structures\Uid</code>, 
	 * to be loaded from;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>__toString</code> method, 
	 * given as an ID to be loaded from;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Stringifiable</code> interface, 
	 * given as an ID to be loaded from.
	 * 
	 * @see \Dracodeum\Kit\Interfaces\Arrayable
	 * @see \Dracodeum\Kit\Components\Store\Structures\Uid
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
			} elseif (is_int($value) || is_float($value) || is_string($value)) {
				$value = UType::coerceObject($builder(static::loadPropertyValues($value), true), static::class);
				return true;
			} elseif (is_object($value)) {
				$instance = $value;
				if ($instance instanceof Entity) {
					if (!UType::isA($instance, static::class)) {
						$value = UType::coerceObject($builder($instance->getAll(true), $persisted), static::class);
					}
					return true;
				} elseif ($instance instanceof Uid) {
					$value = UType::coerceObject(
						$builder(static::loadPropertyValues($instance->id, $instance->scope_values), true),
						static::class
					);
					return true;
				} elseif (static::evaluateId($instance)) {
					$value = UType::coerceObject($builder(static::loadPropertyValues($instance), true), static::class);
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
				" - an integer, float or string, given as an ID to be loaded from;\n" . 
				" - an array of properties, given as \"name => value\" pairs;\n" . 
				" - an object implementing the \"Dracodeum\\Kit\\Interfaces\\Arrayable\" interface;\n" . 
				" - an instance of \"Dracodeum\\Kit\\Components\\Store\\Structures\\Uid\", to be loaded from;\n" . 
				" - an object implementing the \"__toString\" method, given as an ID to be loaded from;\n" . 
				" - an object implementing the \"Dracodeum\\Kit\\Interfaces\\Stringifiable\" interface, " . 
				"given as an ID to be loaded from."
		]);
	}
	
	/**
	 * Evaluate a given value as an ID.
	 * 
	 * Only the following types and formats can be evaluated into an ID:<br>
	 * &nbsp; &#8226; &nbsp; an integer, float or string;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>__toString</code> method;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Stringifiable</code> interface.
	 * 
	 * @param mixed $value [reference]
	 * <p>The value to evaluate (validate and sanitize).</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to evaluate as <code>null</code>.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value was successfully evaluated into an ID.</p>
	 */
	final public static function evaluateId(&$value, bool $nullable = false): bool
	{
		return self::processIdCoercion($value, $nullable, true);
	}
	
	/**
	 * Coerce a given value into an ID.
	 * 
	 * Only the following types and formats can be coerced into an ID:<br>
	 * &nbsp; &#8226; &nbsp; an integer, float or string;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>__toString</code> method;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Stringifiable</code> interface.
	 * 
	 * @param mixed $value
	 * <p>The value to coerce (validate and sanitize).</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @throws \Dracodeum\Kit\Entity\Exceptions\IdCoercionFailed
	 * @return int|float|string|null
	 * <p>The given value coerced into an ID.<br>
	 * If nullable, then <code>null</code> may also be returned.</p>
	 */
	final public static function coerceId($value, bool $nullable = false)
	{
		self::processIdCoercion($value, $nullable);
		return $value;
	}
	
	/**
	 * Process the coercion of a given value into an ID.
	 * 
	 * Only the following types and formats can be coerced into an ID:<br>
	 * &nbsp; &#8226; &nbsp; an integer, float or string;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>__toString</code> method;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Stringifiable</code> interface.
	 * 
	 * @param mixed $value [reference]
	 * <p>The value to process (validate and sanitize).</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Entity\Exceptions\IdCoercionFailed
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value was successfully coerced into an ID.</p>
	 */
	final public static function processIdCoercion(&$value, bool $nullable = false, bool $no_throw = false): bool
	{
		try {
			if (!Uid::processIdCoercion($value, $nullable || !static::hasId(), $no_throw)) {
				return false;
			}
		} catch (UidExceptions\IdCoercionFailed $exception) {
			throw new Exceptions\IdCoercionFailed([
				'value' => $exception->getValue(),
				'entity' => static::class,
				'error_code' => $exception->getErrorCode(),
				'error_message' => $exception->getErrorMessage()
			]);
		}
		return true;
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
	
	
	
	//Final private methods
	/**
	 * Insert a given set of values.
	 * 
	 * @param array $values
	 * <p>The values to insert, as <samp>name => value</samp> pairs.</p>
	 * @throws \Dracodeum\Kit\Entity\Exceptions\Conflict
	 * @return array
	 * <p>The inserted values, as <samp>name => value</samp> pairs.</p>
	 */
	final private function insert(array $values): array
	{
		//pre-insert
		$this->processPreInsert($values);
		
		//pre-persistence
		$this->processPrePersistence($values, $uid);
		
		//insert
		$inserted_values = $this->getStore()->insert($uid, $values, true);
		if ($inserted_values === null) {
			throw new Exceptions\Conflict([$this, $uid->id, 'scope' => $uid->scope]);
		}
		
		//post-persistence
		$this->processPostPersistence($inserted_values, $uid);
		
		//post-insert
		$this->processPostInsert($inserted_values);
		
		//return
		return $inserted_values;
	}
	
	/**
	 * Update from a given old set of values to a new given set.
	 * 
	 * @param array $old_values
	 * <p>The old values to update from, as <samp>name => value</samp> pairs.</p>
	 * @param array $new_values
	 * <p>The new values to update to, as <samp>name => value</samp> pairs.</p>
	 * @param string[] $changed_names
	 * <p>The changed property names to update.</p>
	 * @throws \Dracodeum\Kit\Entity\Exceptions\NotFound
	 * @return array
	 * <p>The updated values, as <samp>name => value</samp> pairs.</p>
	 */
	final private function update(array $old_values, array $new_values, array $changed_names): array
	{
		//pre-persistence
		$this->processPrePersistence($new_values, $uid, $old_values);
		
		//changes
		$changes_map = array_flip($changed_names);
		$old_values = array_intersect_key($old_values, $changes_map);
		$new_values = array_intersect_key($new_values, $changes_map);
		
		//pre-update
		$this->processPreUpdate($old_values, $new_values);
		
		//update
		$updated_values = $this->getStore()->update($uid, $new_values, true);
		if ($updated_values === null) {
			throw new Exceptions\NotFound([$this, $uid->id, 'scope' => $uid->scope]);
		}
		
		//post-update
		$this->processPostUpdate($old_values, $updated_values + $new_values);
		
		//post-persistence
		$this->processPostPersistence($updated_values, $uid);
		
		//return
		return $updated_values;
	}
	
	/**
	 * Process pre-persistence with a given set of new values.
	 * 
	 * @param array $new_values [reference]
	 * <p>The new values to process with, as <samp>name => value</samp> pairs.</p>
	 * @param \Dracodeum\Kit\Components\Store\Structures\Uid|null $uid [reference output]
	 * <p>The output UID instance to use.</p>
	 * @param array|null $old_values [reference] [default = null]
	 * <p>The old values to process with, as <samp>name => value</samp> pairs.</p>
	 * @return void
	 */
	final private function processPrePersistence(array &$new_values, ?Uid &$uid, ?array &$old_values = null): void
	{
		//initialize
		$uid = null;
		$n_values = $new_values;
		$o_values = $old_values;
		
		//id
		$id = null;
		$id_name = $this->getIdPropertyName();
		if ($id_name !== null) {
			if (array_key_exists($id_name, $n_values)) {
				//old values
				if ($o_values !== null) {
					if (!array_key_exists($id_name, $o_values)) {
						UCall::haltParameter('old_values', $old_values, [
							'error_message' => "Missing ID property {{name}}.",
							'parameters' => ['name' => $id_name]
						]);
					} elseif (static::coerceId($n_values[$id_name]) !== static::coerceId($o_values[$id_name])) {
						UCall::haltParameter('new_values', $new_values, [
							'error_message' => "ID property {{name}} new value {{new_value}} mismatches " . 
								"old value {{old_value}}.",
							'hint_message' => "The ID property must be immutable.",
							'parameters' => [
								'name' => $id_name,
								'new_value' => $n_values[$id_name],
								'old_value' => $o_values[$id_name]
							]
						]);
					}
					unset($o_values[$id_name]);
				}
				
				//finalize
				$id = static::coerceId($n_values[$id_name]);
				unset($n_values[$id_name]);
				
			} elseif ($o_values !== null) {
				UCall::haltParameter('new_values', $new_values, [
					'error_message' => "Missing ID property {{name}}.",
					'parameters' => ['name' => $id_name]
				]);
			}
		}
		
		//scope
		$scope_values = [];
		$base_scope = $this->getBaseScope();
		if ($base_scope !== null) {
			foreach (UText::placeholders($base_scope, true) as $name) {
				//check
				if (!array_key_exists($name, $n_values)) {
					UCall::haltParameter('new_values', $new_values, [
						'error_message' => "Missing scope property {{name}}.",
						'parameters' => ['name' => $name]
					]);
				} elseif ($o_values !== null) {
					if (!array_key_exists($name, $o_values)) {
						UCall::haltParameter('old_values', $old_values, [
							'error_message' => "Missing scope property {{name}}.",
							'parameters' => ['name' => $name]
						]);
					} elseif ($n_values[$name] !== $o_values[$name]) {
						UCall::haltParameter('new_values', $new_values, [
							'error_message' => "Scope property {{name}} new value {{new_value}} mismatches " . 
								"old value {{old_value}}.",
							'hint_message' => "The scope properties must be immutable.",
							'parameters' => [
								'name' => $name,
								'new_value' => $n_values[$name],
								'old_value' => $o_values[$name]
							]
						]);
					}
					unset($o_values[$name]);
				}
				
				//finalize
				$scope_values[$name] = $n_values[$name];
				unset($n_values[$name]);
			}
		}
		
		//finalize
		$uid = $this->getStore()->coerceUid([
			'id' => $id,
			'name' => $this->getName(),
			'base_scope' => $base_scope,
			'scope_values' => $scope_values
		]);
		$new_values = $n_values;
		$old_values = $o_values;
	}
	
	/**
	 * Process post-persistence with a given set of values.
	 * 
	 * @param array $values [reference]
	 * <p>The values to process with, as <samp>name => value</samp> pairs.</p>
	 * @param \Dracodeum\Kit\Components\Store\Structures\Uid $uid
	 * <p>The UID instance to use.</p>
	 * @return void
	 */
	final private function processPostPersistence(array &$values, Uid $uid): void
	{
		//id
		$id_name = $this->getIdPropertyName();
		if ($id_name !== null) {
			$values += [$id_name => $uid->id];
		}
		
		//scope
		$values += $uid->scope_values;
	}
	
	
	
	//Final private static methods
	/**
	 * Load property values.
	 * 
	 * @param int|float|string|null $id [default = null]
	 * <p>The ID to load with.</p>
	 * @param array $scope_values [default = []]
	 * <p>The scope values to load with, as <samp>name => value</samp> pairs.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Entity\Exceptions\NotFound
	 * @return array|null
	 * <p>The loaded property values, as <samp>name => value</samp> pairs.<br>
	 * If <var>$no_throw</var> is set to boolean <code>true</code>, 
	 * then <code>null</code> is returned if none were found.</p>
	 */
	final private static function loadPropertyValues(
		$id = null, array $scope_values = [], bool $no_throw = false
	): ?array
	{
		//initialize
		$id = static::coerceId($id);
		$store = static::getStore();
		$base_scope = static::getBaseScope();
		
		//values
		$values = $store->return([
			'id' => $id,
			'name' => static::getName(),
			'base_scope' => $base_scope,
			'scope_values' => $scope_values
		], true);
		
		//check
		if ($values === null) {
			if ($no_throw) {
				return null;
			}
			$scope = $base_scope !== null ? $store->getUidScope($base_scope, $scope_values) : null;
			throw new Exceptions\NotFound([static::class, $id, 'scope' => $scope]);
		}
		
		//finalize
		$id_name = static::getIdPropertyName();
		if ($id_name !== null) {
			$values += [$id_name => $id];
		}
		$values += $scope_values;
		
		//return
		return $values;
	}
}
