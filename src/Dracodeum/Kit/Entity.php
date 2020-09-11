<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit;

use Dracodeum\Kit\Interfaces\{
	Uid as IUid,
	DebugInfo as IDebugInfo,
	Properties as IProperties,
	Arrayable as IArrayable,
	Keyable as IKeyable,
	Readonlyable as IReadonlyable,
	Persistable as IPersistable,
	Unpersistable as IUnpersistable,
	ArrayInstantiable as IArrayInstantiable,
	Stringifiable as IStringifiable,
	Uncloneable as IUncloneable
};
use Dracodeum\Kit\Interfaces\Log\Event\Tag as ILogEventTag;
use Dracodeum\Kit\Traits\DebugInfo\Interfaces\DebugInfoProcessor as IDebugInfoProcessor;
use Dracodeum\Kit\Entity\{
	Traits,
	Options,
	Exceptions
};
use Dracodeum\Kit\Traits as KitTraits;
use Dracodeum\Kit\Traits\DebugInfo\Info as DebugInfo;
use Dracodeum\Kit\Components\Store;
use Dracodeum\Kit\Components\Store\{
	Exception as StoreException,
	Exceptions as StoreExceptions
};
use Dracodeum\Kit\Structures\Uid;
use Dracodeum\Kit\Structures\Uid\Exceptions as UidExceptions;
use Dracodeum\Kit\Enumerations\Log\Level as ELogLevel;
use Dracodeum\Kit\Options\Text as TextOptions;
use Dracodeum\Kit\Utilities\{
	Call as UCall,
	Data as UData,
	Text as UText,
	Type as UType
};
use Dracodeum\Kit\Root\Log;

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
 * and which may be retrieved and set directly just like any public object property.<br>
 * <br>
 * Additional internal functions may also be called automatically upon the persistence of a property value change.<br>
 * <br>
 * It may also be set as read-only to prevent any further changes.
 * 
 * @see https://en.wikipedia.org/wiki/Entity%E2%80%93relationship_model
 * @see \Dracodeum\Kit\Entity\Traits\DefaultBuilder
 * @see \Dracodeum\Kit\Entity\Traits\Initializer
 * @see \Dracodeum\Kit\Entity\Traits\PropertiesInitializer
 * @see \Dracodeum\Kit\Entity\Traits\IdPropertyName
 * @see \Dracodeum\Kit\Entity\Traits\BaseScope
 * @see \Dracodeum\Kit\Entity\Traits\PreInsertProcessor
 * @see \Dracodeum\Kit\Entity\Traits\PostInsertProcessor
 * @see \Dracodeum\Kit\Entity\Traits\PreUpdateProcessor
 * @see \Dracodeum\Kit\Entity\Traits\PostUpdateProcessor
 * @see \Dracodeum\Kit\Entity\Traits\PreDeleteProcessor
 * @see \Dracodeum\Kit\Entity\Traits\PostDeleteProcessor
 * @see \Dracodeum\Kit\Entity\Traits\LogEventProcessor
 */
abstract class Entity
implements IUid, IDebugInfo, IDebugInfoProcessor, IProperties, \ArrayAccess, IArrayable, IKeyable, \JsonSerializable,
IReadonlyable, IPersistable, IUnpersistable, ILogEventTag, IArrayInstantiable, IStringifiable, IUncloneable
{
	//Traits
	use KitTraits\DebugInfo;
	use KitTraits\Properties;
	use KitTraits\Properties\Arrayable;
	use KitTraits\Properties\ArrayAccess;
	use KitTraits\Properties\Keyable;
	use KitTraits\Readonly;
	use KitTraits\Stringifiable;
	use KitTraits\Uncloneable;
	use Traits\DefaultBuilder;
	use Traits\Initializer;
	use Traits\PropertiesInitializer;
	use Traits\IdPropertyName;
	use Traits\BaseScope;
	use Traits\PreInsertProcessor;
	use Traits\PostInsertProcessor;
	use Traits\PreUpdateProcessor;
	use Traits\PostUpdateProcessor;
	use Traits\PreDeleteProcessor;
	use Traits\PostDeleteProcessor;
	use Traits\LogEventProcessor;
	
	
	
	//Private properties
	/** @var \Dracodeum\Kit\Structures\Uid|null */
	private $uid = null;
	
	/** @var int|string|null */
	private $temporary_id = null;
	
	
	
	//Private static properties
	/** @var \Dracodeum\Kit\Components\Store[] */
	private static $stores = [];
	
	/** @var string[] */
	private static $scope_property_names = [];
	
	
	
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
		$this->initializePropertiesManager(
			\Closure::fromCallable([$this, 'loadProperties']), $properties, 'rw',
			\Closure::fromCallable([$this, 'initializeProperties']), $persisted
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
	
	
	
	//Implemented final public methods (Dracodeum\Kit\Interfaces\Uid)
	/** {@inheritdoc} */
	final public function getUid(): Uid
	{
		if ($this->uid === null) {
			$this->uid = $this->getStore()->coerceUid([
				'id' => $this->getId(),
				'name' => $this->getName(),
				'base_scope' => $this->getBaseScope(),
				'scope_ids' => $this->getScopeIds()
			])->setAsReadonly(true);
		}
		return $this->uid;
	}
	
	
	
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
		return self::build($array);
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Interfaces\Stringifiable)
	/** {@inheritdoc} */
	public function toString(?TextOptions $text_options = null): string
	{
		return UText::stringify($this->getAll(), $text_options);
	}
	
	
	
	//Implemented final public methods (Dracodeum\Kit\Interfaces\Persistable)
	/** {@inheritdoc} */
	final public function isPersisted(): bool
	{
		return $this->arePropertiesPersisted();
	}
	
	/**
	 * {@inheritdoc}
	 * @throws \Dracodeum\Kit\Entity\Exceptions\NotFound
	 * @throws \Dracodeum\Kit\Entity\Exceptions\ScopeNotFound
	 * @throws \Dracodeum\Kit\Entity\Exceptions\Conflict
	 */
	final public function persist(): object
	{
		//guard
		$this->guardNonReadonlyCall();
		
		//persist
		try {
			$this->persistProperties(
				\Closure::fromCallable([$this, 'insert']),
				\Closure::fromCallable([$this, 'update'])
			);
		} catch (\Throwable $throwable) {
			$this->logThrowableEvent(ELogLevel::ERROR, $throwable);
			throw $throwable;
		}
		
		//return
		return $this;
	}
	
	
	
	//Implemented final public methods (Dracodeum\Kit\Interfaces\Unpersistable)
	/**
	 * {@inheritdoc}
	 * @throws \Dracodeum\Kit\Entity\Exceptions\NotFound
	 */
	final public function unpersist(): object
	{
		//guard
		$this->guardNonReadonlyCall();
		
		//check
		if (!$this->isPersisted()) {
			return $this;
		}
		
		//unpersist
		try {
			//pre-delete
			$this->processPreDelete();
			
			//delete
			$id = $this->getId();
			try {
				$this->getStore()->delete([
					'id' => $id,
					'name' => $this->getName(),
					'base_scope' => $this->getBaseScope(),
					'scope_ids' => $this->getScopeIds()
				]);
			} catch (StoreException $exception) {
				throw $this->mutateStoreException($exception);
			}
			
			//log
			$this->logEvent(ELogLevel::INFO, "Entity {{name}} deleted.", [
				'name' => 'entity.delete',
				'data' => [
					'id' => $id,
					'scope' => $this->getScope()
				],
				'parameters' => ['name' => $this->getName()]
			]);
			
			//post-delete
			$this->processPostDelete();
			
			//properties
			$this->unpersistProperties();
			
		} catch (\Throwable $throwable) {
			$this->logThrowableEvent(ELogLevel::ERROR, $throwable);
			throw $throwable;
		}
		
		//return
		return $this;
	}
	
	
	
	//Implemented final public methods (Dracodeum\Kit\Interfaces\Log\Event\Tag)
	/** {@inheritdoc} */
	final public function getLogEventTag(): string
	{
		//initialize
		$strings = ['entity', $this->getName()];
		
		//scope
		$scope = $this->getScope();
		if ($scope !== null) {
			$strings[] = $scope;
		}
		
		//id
		$id = $this->getId();
		if ($id !== null) {
			$strings[] = $id;
		}
		
		//return
		return Log::composeEventTag($strings);
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Traits\DebugInfo\Interfaces\DebugInfoProcessor)
	/** {@inheritdoc} */
	public function processDebugInfo(DebugInfo $info): void
	{
		$info->set('@persisted', $this->isPersisted());
		$this->processReadonlyDebugInfo($info)->processPropertiesDebugInfo($info);
		$info
			->enableObjectPropertiesDump()
			->hideObjectProperty('uid', self::class)
			->hideObjectProperty('temporary_id', self::class)
		;
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
	 * @return int|string|null
	 * <p>The ID or <code>null</code> if none is set.</p>
	 */
	final public function getId()
	{
		//temporary
		if ($this->temporary_id !== null) {
			return $this->temporary_id;
		}
		
		//property
		$name = $this->getIdPropertyName();
		if ($name !== null && $this->gettable($name)) {
			$id = $this->get($name);
			if (!self::evaluateId($id)) {
				UCall::haltInternal([
					'error_message' => "Invalid ID {{id}} in entity {{entity}}.",
					'hint_message' => "Only an integer or string ID is allowed.",
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
		$base = $this->getBaseScope();
		return $base !== null ? $this->getStore()->getUidScope($base, $this->getScopeIds()) : null;
	}
	
	/**
	 * Get scope IDs.
	 * 
	 * @return int[]|string[]
	 * <p>The scope IDs, as <samp>name => id</samp> pairs.</p>
	 */
	final public function getScopeIds(): array
	{
		$ids = [];
		foreach ($this->getScopePropertyNames() as $name) {
			$ids[$name] = $this->get($name);
		}
		return $ids;
	}
	
	/**
	 * Reload.
	 * 
	 * This method may only be called after persistence.
	 * 
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function reload(): Entity
	{
		if ($this->isPersisted()) {
			$this->reloadProperties(function (): array {
				return $this->loadPropertyValues($this->getId(), $this->getScopeIds());
			});
		} else {
			UCall::halt(['hint_message' => "This method may only be called after persistence."]);
		}
		return $this;
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
	 * Check if has scope.
	 * 
	 * @return bool
	 * <p>Boolean <code>true</code> if has scope.</p>
	 */
	final public static function hasScope(): bool
	{
		return static::getBaseScope() !== null;
	}
	
	/**
	 * Get scope property names.
	 * 
	 * @return string[]
	 * <p>The scope property names.</p>
	 */
	final public static function getScopePropertyNames(): array
	{
		if (!isset(self::$scope_property_names[static::class])) {
			$base = static::getBaseScope();
			self::$scope_property_names[static::class] = $base !== null ? UText::placeholders($base, true) : [];
		}
		return self::$scope_property_names[static::class];
	}
	
	/**
	 * Get static scope.
	 * 
	 * @param int[]|string[] $ids [default = []]
	 * <p>The IDs to get with, as <samp>name => id</samp> pairs.</p>
	 * @return string|null
	 * <p>The static scope or <code>null</code> if none is set.</p>
	 */
	final public static function getStaticScope(array $ids = []): ?string
	{
		$ids = self::coerceScopeIds($ids);
		$base = static::getBaseScope();
		return $base !== null ? self::getStore()->getUidScope($base, $ids) : null;
	}
	
	/**
	 * Check if an instance exists.
	 * 
	 * @param int|string|null $id [default = null]
	 * <p>The ID to check with.</p>
	 * @param int[]|string[] $scope_ids [default = []]
	 * <p>The scope IDs to check with, as <samp>name => id</samp> pairs.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if an instance exists.</p>
	 */
	final public static function exists($id = null, array $scope_ids = []): bool
	{
		return self::getStore()->exists([
			'id' => self::coerceId($id),
			'name' => static::getName(),
			'base_scope' => static::getBaseScope(),
			'scope_ids' => self::coerceScopeIds($scope_ids)
		]);
	}
	
	/**
	 * Load an instance.
	 * 
	 * @param int|string|null $id [default = null]
	 * <p>The ID to load with.</p>
	 * @param int[]|string[] $scope_ids [default = []]
	 * <p>The scope IDs to load with, as <samp>name => id</samp> pairs.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Entity\Exceptions\NotFound
	 * @return static|null
	 * <p>The loaded instance.<br>
	 * If <var>$no_throw</var> is set to boolean <code>true</code>, 
	 * then <code>null</code> is returned if it was not found.</p>
	 */
	final public static function load($id = null, array $scope_ids = [], bool $no_throw = false): ?Entity
	{
		$properties = self::loadPropertyValues($id, $scope_ids, $no_throw);
		return $properties !== null ? self::build($properties, true) : null;
	}
	
	/**
	 * Delete an instance.
	 * 
	 * @param int|string|null $id [default = null]
	 * <p>The ID to delete with.</p>
	 * @param int[]|string[] $scope_ids [default = []]
	 * <p>The scope IDs to delete with, as <samp>name => id</samp> pairs.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Entity\Exceptions\NotFound
	 * @return void|bool
	 * <p>If <var>$no_throw</var> is set to boolean <code>true</code>, 
	 * then boolean <code>true</code> is returned if the instance was found and deleted, 
	 * or boolean <code>false</code> if otherwise.</p>
	 */
	final public static function delete($id = null, array $scope_ids = [], bool $no_throw = false)
	{
		//instance
		$instance = self::load($id, $scope_ids, $no_throw);
		if ($instance === null) {
			if ($no_throw) {
				return false;
			}
			return;
		}
		
		//unpersist
		try {
			$instance->unpersist();
		} catch (Exceptions\NotFound $exception) {
			if ($no_throw) {
				return false;
			}
			throw $exception;
		}
		
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
	 * &nbsp; &#8226; &nbsp; an integer or string, given as an ID to be loaded from;<br>
	 * &nbsp; &#8226; &nbsp; an array of properties, given as <samp>name => value</samp> pairs;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Arrayable</code> interface;<br>
	 * &nbsp; &#8226; &nbsp; an instance of <code>Dracodeum\Kit\Structures\Uid</code>, 
	 * to be loaded from;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>__toString</code> method, 
	 * given as an ID to be loaded from;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Stringifiable</code> interface, 
	 * given as an ID to be loaded from.
	 * 
	 * @see \Dracodeum\Kit\Interfaces\Arrayable
	 * @see \Dracodeum\Kit\Interfaces\Stringifiable
	 * @see \Dracodeum\Kit\Structures\Uid
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
	 * &nbsp; &#8226; &nbsp; an integer or string, given as an ID to be loaded from;<br>
	 * &nbsp; &#8226; &nbsp; an array of properties, given as <samp>name => value</samp> pairs;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Arrayable</code> interface;<br>
	 * &nbsp; &#8226; &nbsp; an instance of <code>Dracodeum\Kit\Structures\Uid</code>, 
	 * to be loaded from;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>__toString</code> method, 
	 * given as an ID to be loaded from;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Stringifiable</code> interface, 
	 * given as an ID to be loaded from.
	 * 
	 * @see \Dracodeum\Kit\Interfaces\Arrayable
	 * @see \Dracodeum\Kit\Interfaces\Stringifiable
	 * @see \Dracodeum\Kit\Structures\Uid
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
	 * &nbsp; &#8226; &nbsp; an integer or string, given as an ID to be loaded from;<br>
	 * &nbsp; &#8226; &nbsp; an array of properties, given as <samp>name => value</samp> pairs;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Arrayable</code> interface;<br>
	 * &nbsp; &#8226; &nbsp; an instance of <code>Dracodeum\Kit\Structures\Uid</code>, 
	 * to be loaded from;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>__toString</code> method, 
	 * given as an ID to be loaded from;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Stringifiable</code> interface, 
	 * given as an ID to be loaded from.
	 * 
	 * @see \Dracodeum\Kit\Interfaces\Arrayable
	 * @see \Dracodeum\Kit\Interfaces\Stringifiable
	 * @see \Dracodeum\Kit\Structures\Uid
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
			} elseif (is_int($value) || is_string($value)) {
				$value = UType::coerceObject($builder(self::loadPropertyValues($value), true), static::class);
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
						$builder(self::loadPropertyValues($instance->id, $instance->scope_ids), true),
						static::class
					);
					return true;
				} elseif (self::evaluateId($instance)) {
					$value = UType::coerceObject($builder(self::loadPropertyValues($instance), true), static::class);
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
		
		//finalize
		if ($no_throw) {
			return false;
		}
		throw new Exceptions\CoercionFailed([
			'value' => $value,
			'entity' => static::class,
			'error_code' => Exceptions\CoercionFailed::ERROR_CODE_INVALID_TYPE,
			'error_message' => "Only the following types and formats can be coerced into an instance:\n" . 
				" - null or an instance;\n" . 
				" - an integer or string, given as an ID to be loaded from;\n" . 
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
	 * &nbsp; &#8226; &nbsp; an integer or string;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>__toString</code> method;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Stringifiable</code> interface.
	 * 
	 * @see \Dracodeum\Kit\Interfaces\Stringifiable
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
	 * &nbsp; &#8226; &nbsp; an integer or string;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>__toString</code> method;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Stringifiable</code> interface.
	 * 
	 * @see \Dracodeum\Kit\Interfaces\Stringifiable
	 * @param mixed $value
	 * <p>The value to coerce (validate and sanitize).</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @throws \Dracodeum\Kit\Entity\Exceptions\IdCoercionFailed
	 * @return int|string|null
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
	 * &nbsp; &#8226; &nbsp; an integer or string;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>__toString</code> method;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Stringifiable</code> interface.
	 * 
	 * @see \Dracodeum\Kit\Interfaces\Stringifiable
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
		//check
		if (!self::hasId()) {
			if ($value === null) {
				return true;
			} elseif ($no_throw) {
				return false;
			}
			throw new Exceptions\IdCoercionFailed([
				'value' => $value,
				'entity' => static::class,
				'error_code' => Exceptions\IdCoercionFailed::ERROR_CODE_NOT_IMPLEMENTED,
				'error_message' => "This entity does not have an ID implemented."
			]);
		}
		
		//process
		try {
			if (!Uid::processIdCoercion($value, $nullable, $no_throw)) {
				return false;
			}
		} catch (UidExceptions\IdCoercionFailed $exception) {
			throw new Exceptions\IdCoercionFailed([
				'value' => $exception->value,
				'entity' => static::class,
				'error_code' => $exception->error_code,
				'error_message' => $exception->error_message
			]);
		}
		
		//return
		return true;
	}
	
	/**
	 * Evaluate a given value with a given name as a scope ID.
	 * 
	 * Only the following types and formats can be evaluated into a scope ID:<br>
	 * &nbsp; &#8226; &nbsp; an integer or string;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>__toString</code> method;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Stringifiable</code> interface.
	 * 
	 * @see \Dracodeum\Kit\Interfaces\Stringifiable
	 * @param string $name
	 * <p>The name to evaluate with.</p>
	 * @param mixed $value [reference]
	 * <p>The value to evaluate (validate and sanitize).</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to evaluate as <code>null</code>.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value with the given name was successfully evaluated into a 
	 * scope ID.</p>
	 */
	final public static function evaluateScopeId(string $name, &$value, bool $nullable = false): bool
	{
		return self::processScopeIdCoercion($name, $value, $nullable, true);
	}
	
	/**
	 * Coerce a given value with a given name into a scope ID.
	 * 
	 * Only the following types and formats can be coerced into a scope ID:<br>
	 * &nbsp; &#8226; &nbsp; an integer or string;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>__toString</code> method;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Stringifiable</code> interface.
	 * 
	 * @see \Dracodeum\Kit\Interfaces\Stringifiable
	 * @param string $name
	 * <p>The name to coerce with.</p>
	 * @param mixed $value
	 * <p>The value to coerce (validate and sanitize).</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @throws \Dracodeum\Kit\Entity\Exceptions\ScopeIdCoercionFailed
	 * @return int|string|null
	 * <p>The given value with the given name coerced into a scope ID.<br>
	 * If nullable, then <code>null</code> may also be returned.</p>
	 */
	final public static function coerceScopeId(string $name, $value, bool $nullable = false)
	{
		self::processScopeIdCoercion($name, $value, $nullable);
		return $value;
	}
	
	/**
	 * Process the coercion of a given value with a given name into a scope ID.
	 * 
	 * Only the following types and formats can be coerced into a scope ID:<br>
	 * &nbsp; &#8226; &nbsp; an integer or string;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>__toString</code> method;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Stringifiable</code> interface.
	 * 
	 * @see \Dracodeum\Kit\Interfaces\Stringifiable
	 * @param string $name
	 * <p>The name to process with.</p>
	 * @param mixed $value [reference]
	 * <p>The value to process (validate and sanitize).</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Entity\Exceptions\ScopeIdCoercionFailed
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value with the given name was successfully coerced into a scope ID.</p>
	 */
	final public static function processScopeIdCoercion(
		string $name, &$value, bool $nullable = false, bool $no_throw = false
	): bool
	{
		//check
		if (!self::hasScope()) {
			if ($no_throw) {
				return false;
			}
			throw new Exceptions\ScopeIdCoercionFailed([
				'name' => $name,
				'value' => $value,
				'entity' => static::class,
				'error_code' => Exceptions\ScopeIdCoercionFailed::ERROR_CODE_NOT_IMPLEMENTED,
				'error_message' => "This entity does not have a scope implemented."
			]);
		}
		
		//name
		if (!in_array($name, self::getScopePropertyNames(), true)) {
			if ($no_throw) {
				return false;
			}
			throw new Exceptions\ScopeIdCoercionFailed([
				'name' => $name,
				'value' => $value,
				'entity' => static::class,
				'error_code' => Exceptions\ScopeIdCoercionFailed::ERROR_CODE_INVALID_NAME,
				'error_message' => "Invalid scope ID name."
			]);
		}
		
		//process
		try {
			if (!Uid::processScopeIdCoercion($name, $value, $nullable, $no_throw)) {
				return false;
			}
		} catch (UidExceptions\ScopeIdCoercionFailed $exception) {
			throw new Exceptions\ScopeIdCoercionFailed([
				'name' => $name,
				'value' => $exception->value,
				'entity' => static::class,
				'error_code' => $exception->error_code,
				'error_message' => $exception->error_message
			]);
		}
		
		//return
		return true;
	}
	
	/**
	 * Evaluate a given set of values as a set of scope IDs.
	 * 
	 * Only the following types and formats can be evaluated into scope IDs:<br>
	 * &nbsp; &#8226; &nbsp; an array of integers or strings;<br>
	 * &nbsp; &#8226; &nbsp; an array of objects implementing the <code>__toString</code> method;<br>
	 * &nbsp; &#8226; &nbsp; an array of objects implementing the <code>Dracodeum\Kit\Interfaces\Stringifiable</code> 
	 * interface.
	 * 
	 * @see \Dracodeum\Kit\Interfaces\Stringifiable
	 * @param array $values [reference]
	 * <p>The set of values to evaluate (validate and sanitize).</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given set of values was successfully evaluated into a set of scope IDs.</p>
	 */
	final public static function evaluateScopeIds(array &$values): bool
	{
		return self::processScopeIdsCoercion($values, true);
	}
	
	/**
	 * Coerce a given set of values into a set of scope IDs.
	 * 
	 * Only the following types and formats can be coerced into scope IDs:<br>
	 * &nbsp; &#8226; &nbsp; an array of integers or strings;<br>
	 * &nbsp; &#8226; &nbsp; an array of objects implementing the <code>__toString</code> method;<br>
	 * &nbsp; &#8226; &nbsp; an array of objects implementing the <code>Dracodeum\Kit\Interfaces\Stringifiable</code> 
	 * interface.
	 * 
	 * @see \Dracodeum\Kit\Interfaces\Stringifiable
	 * @param array $values
	 * <p>The set of values to coerce (validate and sanitize).</p>
	 * @throws \Dracodeum\Kit\Entity\Exceptions\ScopeIdsCoercionFailed
	 * @return int[]|string[]
	 * <p>The given set of values coerced into a set of scope IDs.</p>
	 */
	final public static function coerceScopeIds(array $values): array
	{
		self::processScopeIdsCoercion($values);
		return $values;
	}
	
	/**
	 * Process the coercion of a given set of values into a set of scope IDs.
	 * 
	 * Only the following types and formats can be coerced into scope IDs:<br>
	 * &nbsp; &#8226; &nbsp; an array of integers or strings;<br>
	 * &nbsp; &#8226; &nbsp; an array of objects implementing the <code>__toString</code> method;<br>
	 * &nbsp; &#8226; &nbsp; an array of objects implementing the <code>Dracodeum\Kit\Interfaces\Stringifiable</code> 
	 * interface.
	 * 
	 * @param array $values [reference]
	 * <p>The set of values to process (validate and sanitize).</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Entity\Exceptions\ScopeIdsCoercionFailed
	 * @return bool
	 * <p>Boolean <code>true</code> if the given set of values was successfully coerced into a set of scope IDs.</p>
	 */
	final public static function processScopeIdsCoercion(array &$values, bool $no_throw = false): bool
	{
		//check
		if (!self::hasScope()) {
			if (empty($values)) {
				return true;
			} elseif ($no_throw) {
				return false;
			}
			throw new Exceptions\ScopeIdsCoercionFailed([
				'value' => $values,
				'entity' => static::class,
				'error_code' => Exceptions\ScopeIdsCoercionFailed::ERROR_CODE_NOT_IMPLEMENTED,
				'error_message' => "This entity does not have a scope implemented."
			]);
		}
		
		//map
		$names_map = array_flip(self::getScopePropertyNames());
		
		//missing
		$missing_names = array_keys(array_diff_key($names_map, $values));
		if (!empty($missing_names)) {
			if ($no_throw) {
				return false;
			}
			throw new Exceptions\ScopeIdsCoercionFailed([
				'value' => $values,
				'entity' => static::class,
				'error_code' => Exceptions\ScopeIdsCoercionFailed::ERROR_CODE_MISSING_NAMES,
				'error_message' => UText::pfill(
					"Missing scope ID for {{names}}.", "Missing scope IDs for {{names}}.",
					count($missing_names), null, ['names' => UText::commify($missing_names, null, 'and', true)]
				)
			]);
		}
		unset($missing_names);
		
		//invalid
		$invalid_names = array_keys(array_diff_key($values, $names_map));
		if (!empty($invalid_names)) {
			if ($no_throw) {
				return false;
			}
			throw new Exceptions\ScopeIdsCoercionFailed([
				'value' => $values,
				'entity' => static::class,
				'error_code' => Exceptions\ScopeIdsCoercionFailed::ERROR_CODE_INVALID_NAMES,
				'error_message' => UText::pfill(
					"Invalid scope ID name {{names}}.", "Invalid scope ID names {{names}}.",
					count($invalid_names), null, ['names' => UText::commify($invalid_names, null, 'and', true)]
				)
			]);
		}
		unset($invalid_names);
		
		//process
		$ids = [];
		try {
			foreach ($values as $name => $value) {
				if (self::processScopeIdCoercion($name, $value, false, $no_throw)) {
					$ids[$name] = $value;
				} else {
					return false;
				}
			}
		} catch (Exceptions\ScopeIdCoercionFailed $exception) {
			throw new Exceptions\ScopeIdsCoercionFailed([
				'value' => [$exception->name => $exception->value],
				'entity' => static::class,
				'error_code' => $exception->error_code,
				'error_message' => $exception->error_message
			]);
		}
		$values = $ids;
		
		//return
		return true;
	}
	
	
	
	//Final protected methods
	/**
	 * Log event with a given level and message.
	 * 
	 * @see \Dracodeum\Kit\Enumerations\Log\Level
	 * @param int|string $level
	 * <p>The level to log with, 
	 * as a name or value from the <code>Dracodeum\Kit\Enumerations\Log\Level</code> enumeration.</p>
	 * @param string $message
	 * <p>The message to log with, optionally set with placeholders as <samp>{{placeholder}}</samp>.<br>
	 * <br>
	 * If set, then placeholders must be exclusively composed by identifiers, 
	 * which are defined as words which must start with a letter (<samp>a-z</samp> and <samp>A-Z</samp>) 
	 * or underscore (<samp>_</samp>), and may only contain letters (<samp>a-z</samp> and <samp>A-Z</samp>), 
	 * digits (<samp>0-9</samp>) and underscores (<samp>_</samp>).<br>
	 * <br>
	 * They may also be used with pointers to specific object properties or associative array values, 
	 * by using a dot between identifiers, such as <samp>{{object.property}}</samp>, 
	 * with no limit on the number of chained pointers.<br>
	 * <br>
	 * If suffixed with opening and closing parenthesis, such as <samp>{{object.method()}}</samp>, 
	 * then the identifiers are interpreted as getter method calls, but they cannot be given any arguments.</p>
	 * @param \Dracodeum\Kit\Entity\Options\LogEvent|array|null $options [default = null]
	 * <p>Additional options to use, as an instance or <samp>name => value</samp> pairs.</p>
	 * @return void
	 */
	final protected function logEvent($level, string $message, $options = null): void
	{
		//initialize
		$options = Options\LogEvent::coerce($options, false);
		$options->stack_offset++;
		
		//event
		$event = Log::createEvent($level, $message, $options);
		$this->processLogEvent($event);
		
		//add
		Log::addEvent($event);
	}
	
	/**
	 * Log event with a given level and message in plural form.
	 * 
	 * @see \Dracodeum\Kit\Enumerations\Log\Level
	 * @param int|string $level
	 * <p>The level to log with, 
	 * as a name or value from the <code>Dracodeum\Kit\Enumerations\Log\Level</code> enumeration.</p>
	 * @param string $message1
	 * <p>The message in singular form to log with, 
	 * optionally set with placeholders as <samp>{{placeholder}}</samp>.<br>
	 * <br>
	 * If set, then placeholders must be exclusively composed by identifiers, 
	 * which are defined as words which must start with a letter (<samp>a-z</samp> and <samp>A-Z</samp>) 
	 * or underscore (<samp>_</samp>), and may only contain letters (<samp>a-z</samp> and <samp>A-Z</samp>), 
	 * digits (<samp>0-9</samp>) and underscores (<samp>_</samp>).<br>
	 * <br>
	 * They may also be used with pointers to specific object properties or associative array values, 
	 * by using a dot between identifiers, such as <samp>{{object.property}}</samp>, 
	 * with no limit on the number of chained pointers.<br>
	 * <br>
	 * If suffixed with opening and closing parenthesis, such as <samp>{{object.method()}}</samp>, 
	 * then the identifiers are interpreted as getter method calls, but they cannot be given any arguments.</p>
	 * @param string $message2
	 * <p>The message in plural form to log with, optionally set with placeholders as <samp>{{placeholder}}</samp>.<br>
	 * <br>
	 * If set, then placeholders must be exclusively composed by identifiers, 
	 * which are defined as words which must start with a letter (<samp>a-z</samp> and <samp>A-Z</samp>) 
	 * or underscore (<samp>_</samp>), and may only contain letters (<samp>a-z</samp> and <samp>A-Z</samp>), 
	 * digits (<samp>0-9</samp>) and underscores (<samp>_</samp>).<br>
	 * <br>
	 * They may also be used with pointers to specific object properties or associative array values, 
	 * by using a dot between identifiers, such as <samp>{{object.property}}</samp>, 
	 * with no limit on the number of chained pointers.<br>
	 * <br>
	 * If suffixed with opening and closing parenthesis, such as <samp>{{object.method()}}</samp>, 
	 * then the identifiers are interpreted as getter method calls, but they cannot be given any arguments.</p>
	 * @param float $number
	 * <p>The number to use to select either the singular (<var>$message1</var>) or plural (<var>$message2</var>) form 
	 * of the message.</p>
	 * @param \Dracodeum\Kit\Entity\Options\PlogEvent|array|null $options [default = null]
	 * <p>Additional options to use, as an instance or <samp>name => value</samp> pairs.</p>
	 * @return void
	 */
	final protected function plogEvent($level, string $message1, string $message2, float $number, $options = null): void
	{
		//initialize
		$options = Options\PlogEvent::coerce($options, false);
		$options->stack_offset++;
		
		//event
		$event = Log::createPEvent($level, $message1, $message2, $number, $options);
		$this->processLogEvent($event);
		
		//add
		Log::addEvent($event);
	}
	
	/**
	 * Log event with a given level and throwable instance.
	 * 
	 * @see \Dracodeum\Kit\Enumerations\Log\Level
	 * @param int|string $level
	 * <p>The level to log with, 
	 * as a name or value from the <code>Dracodeum\Kit\Enumerations\Log\Level</code> enumeration.</p>
	 * @param \Throwable $throwable
	 * <p>The throwable instance to log with.</p>
	 * @param \Dracodeum\Kit\Entity\Options\LogThrowableEvent|array|null $options [default = null]
	 * <p>Additional options to use, as an instance or <samp>name => value</samp> pairs.</p>
	 * @return void
	 */
	final protected function logThrowableEvent($level, \Throwable $throwable, $options = null): void
	{
		//initialize
		$options = Options\LogThrowableEvent::coerce($options, false);
		$options->stack_offset++;
		
		//event
		$event = Log::createThrowableEvent($level, $throwable, $options);
		$this->processLogEvent($event);
		
		//add
		Log::addEvent($event);
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
	 * @return array
	 * <p>The inserted values, as <samp>name => value</samp> pairs.</p>
	 */
	final private function insert(array $values): array
	{
		try {
			//pre-insert
			$this->processPreInsert($values);
			
			//pre-persistence
			$this->processPrePersistence($values, $uid);
			$this->temporary_id = $uid->id;
			
			//insert
			try {
				$values = $this->getStore()->insert($uid, $values) + $values;
			} catch (StoreException $exception) {
				throw $this->mutateStoreException($exception);
			}
			$this->temporary_id = $uid->id;
			
			//log
			$this->logEvent(ELogLevel::INFO, "Entity {{name}} inserted.", [
				'name' => 'entity.insert',
				'data' => [
					'id' => $uid->id,
					'scope' => $uid->scope,
					'properties' => $values
				],
				'parameters' => ['name' => $this->getName()]
			]);
			
			//post-persistence
			$this->processPostPersistence($values, $uid);
			
			//post-insert
			$this->processPostInsert($values);
			
		} finally {
			$this->temporary_id = null;
		}
		
		//return
		return $values;
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
		try {
			$new_values = $this->getStore()->update($uid, $new_values) + $new_values;
		} catch (StoreException $exception) {
			throw $this->mutateStoreException($exception);
		}
		
		//log
		$this->logEvent(ELogLevel::INFO, "Entity {{name}} updated.", [
			'name' => 'entity.update',
			'data' => [
				'id' => $uid->id,
				'scope' => $uid->scope,
				'properties' => [
					'old' => $old_values,
					'new' => $new_values
				]
			],
			'parameters' => ['name' => $this->getName()]
		]);
		
		//post-update
		$this->processPostUpdate($old_values, $new_values);
		
		//post-persistence
		$this->processPostPersistence($new_values, $uid);
		
		//return
		return $new_values;
	}
	
	/**
	 * Process pre-persistence with a given set of new values.
	 * 
	 * @param array $new_values [reference]
	 * <p>The new values to process with, as <samp>name => value</samp> pairs.</p>
	 * @param \Dracodeum\Kit\Structures\Uid|null $uid [reference output]
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
					} elseif (self::coerceId($n_values[$id_name]) !== self::coerceId($o_values[$id_name])) {
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
				$id = self::coerceId($n_values[$id_name]);
				unset($n_values[$id_name]);
				
			} elseif ($o_values !== null) {
				UCall::haltParameter('new_values', $new_values, [
					'error_message' => "Missing ID property {{name}}.",
					'parameters' => ['name' => $id_name]
				]);
			}
		}
		
		//scope
		$scope_ids = [];
		$base_scope = $this->getBaseScope();
		if ($base_scope !== null) {
			foreach ($this->getScopePropertyNames() as $name) {
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
					} elseif (
						self::coerceScopeId($name, $n_values[$name]) !== self::coerceScopeId($name, $o_values[$name])
					) {
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
				$scope_ids[$name] = self::coerceScopeId($name, $n_values[$name]);
				unset($n_values[$name]);
			}
		}
		
		//finalize
		$uid = $this->getStore()->coerceUid([
			'id' => $id,
			'name' => $this->getName(),
			'base_scope' => $base_scope,
			'scope_ids' => $scope_ids
		]);
		$new_values = $n_values;
		$old_values = $o_values;
	}
	
	/**
	 * Process post-persistence with a given set of values.
	 * 
	 * @param array $values [reference]
	 * <p>The values to process with, as <samp>name => value</samp> pairs.</p>
	 * @param \Dracodeum\Kit\Structures\Uid $uid
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
		$values += $uid->scope_ids;
	}
	
	/**
	 * Mutate a given store exception instance into another exception instance.
	 * 
	 * @param \Dracodeum\Kit\Components\Store\Exception $exception
	 * <p>The store exception instance to mutate.</p>
	 * @return \Exception
	 * <p>The mutated exception instance.</p>
	 */
	final private function mutateStoreException(StoreException $exception): \Exception
	{
		if ($exception instanceof StoreExceptions\NotFound) {
			return new Exceptions\NotFound([$this, 'id' => $exception->uid->id, 'scope' => $exception->uid->scope]);
		} elseif ($exception instanceof StoreExceptions\ScopeNotFound) {
			return new Exceptions\ScopeNotFound([$this, $exception->uid->scope]);
		} elseif ($exception instanceof StoreExceptions\Conflict) {
			return new Exceptions\Conflict([$this, 'id' => $exception->uid->id, 'scope' => $exception->uid->scope]);
		}
		return $exception;
	}
	
	
	
	//Final private static methods
	/**
	 * Load property values.
	 * 
	 * @param int|string|null $id [default = null]
	 * <p>The ID to load with.</p>
	 * @param int[]|string[] $scope_ids [default = []]
	 * <p>The scope IDs to load with, as <samp>name => id</samp> pairs.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Entity\Exceptions\NotFound
	 * @return array|null
	 * <p>The loaded property values, as <samp>name => value</samp> pairs.<br>
	 * If <var>$no_throw</var> is set to boolean <code>true</code>, 
	 * then <code>null</code> is returned if none were found.</p>
	 */
	final private static function loadPropertyValues($id = null, array $scope_ids = [], bool $no_throw = false): ?array
	{
		//initialize
		$id = self::coerceId($id);
		$scope_ids = self::coerceScopeIds($scope_ids);
		$store = self::getStore();
		$base_scope = static::getBaseScope();
		
		//values
		$values = $store->select([
			'id' => $id,
			'name' => static::getName(),
			'base_scope' => $base_scope,
			'scope_ids' => $scope_ids
		], true);
		
		//check
		if ($values === null) {
			if ($no_throw) {
				return null;
			}
			$scope = $base_scope !== null ? $store->getUidScope($base_scope, $scope_ids) : null;
			throw new Exceptions\NotFound([static::class, 'id' => $id, 'scope' => $scope]);
		}
		
		//finalize
		$id_name = static::getIdPropertyName();
		if ($id_name !== null) {
			$values += [$id_name => $id];
		}
		$values += $scope_ids;
		
		//return
		return $values;
	}
}
