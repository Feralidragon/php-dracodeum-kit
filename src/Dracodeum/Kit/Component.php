<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit;

use Dracodeum\Kit\Interfaces\{
	DebugInfo as IDebugInfo,
	Propertiesable as IPropertiesable,
	Uncloneable as IUncloneable
};
use Dracodeum\Kit\Traits\DebugInfo\Interfaces\DebugInfoProcessor as IDebugInfoProcessor;
use Dracodeum\Kit\Component\{
	Exceptions,
	Traits,
	PrototypeProducer
};
use Dracodeum\Kit\Traits as KitTraits;
use Dracodeum\Kit\Traits\DebugInfo\Info as DebugInfo;
use Dracodeum\Kit\Utilities\{
	Call as UCall,
	Type as UType
};
use Dracodeum\Kit\Utilities\Type\Exceptions as UTypeExceptions;

/**
 * This class is the base to be extended from when creating a component.
 * 
 * A component is an object which represents a specific functional part of an application and is expected to have 
 * a high number of different implementations, each one tailored to a specific purpose, but with their core behavior, 
 * functionality and interface intact.<br>
 * <br>
 * Some examples of this kind of object are inputs, outputs, tables, parameters, filters, constraints, models, 
 * controllers, handlers, and others, all of which are expected to have tens or even hundreds of different 
 * implementations under a common functional interface and behavior when seen and used by other objects.<br>
 * <br>
 * This implementation is performed through a <b>prototype</b>.<br>
 * <br>
 * Any methods meant to implement a component are publicly declared as abstract in a prototype instead, 
 * which generally shares the same class name as that component, but resides in a different namespace, 
 * resulting in a component using a prototype to define specific details of its internal behavior.<br>
 * <br>
 * While every method declared in a prototype must be implemented, additional interfaces recognized by the component 
 * may be defined and implemented in the prototype to enable additional optional features.<br>
 * <br>
 * A component never exposes its prototype to other outside objects, ensuring that every public method defined 
 * in a prototype remains hidden from other objects, to be exclusively used by its component alone.<br>
 * Additionally, a component may be extended or recreated to modify or refactor any internal behavior and still be able 
 * to reuse all the existing prototypes in the same way.<br>
 * <br>
 * A prototype may require the implementation of a specific contract, as an interface, by any component which uses it, 
 * so that a prototype may call specific methods from a component safely, without exposing the component itself.<br>
 * Additional subcontracts, as interfaces, may also be implemented so a component may safely override some other 
 * internal method calls from a prototype, but they are not required to be implemented by a component.<br>
 * <br>
 * The implementation of any contracts and subcontracts may be delegated to a proxy, 
 * for cases when it is preferable for the component to not implement the methods from such contracts and subcontracts, 
 * or to implement them with a different access or signature.<br>
 * <br>
 * Both components and prototypes may also have a layer of custom lazy-loaded properties, 
 * which may be given during instantiation.<br>
 * All properties from either a component or prototype may be accessed from any scope.<br>
 * <br>
 * While the prototype to use may be given through its class or an instance (dependency injection pattern), 
 * a component may also map specific names towards specific prototypes, so that a prototype may also be instantiated 
 * and used through the usage of a name instead, so that the class to use does not need to be known ahead of time 
 * (factory pattern).
 * 
 * @see \Dracodeum\Kit\Prototype
 * @see \Dracodeum\Kit\Component\Traits\DefaultBuilder
 * @see \Dracodeum\Kit\Component\Traits\PreInitializer
 * @see \Dracodeum\Kit\Component\Traits\RequiredPropertyNamesLoader
 * @see \Dracodeum\Kit\Component\Traits\PropertyBuilder
 * @see \Dracodeum\Kit\Component\Traits\Initializer
 * @see \Dracodeum\Kit\Component\Traits\DefaultPrototypeProducer
 * @see \Dracodeum\Kit\Component\Traits\PrototypeInitializer
 * @see \Dracodeum\Kit\Component\Traits\PrototypeProducer
 * @see \Dracodeum\Kit\Component\Traits\ProxyClass
 */
abstract class Component implements IDebugInfo, IDebugInfoProcessor, IPropertiesable, IUncloneable
{
	//Traits
	use KitTraits\DebugInfo;
	use KitTraits\LazyProperties;
	use KitTraits\Uncloneable;
	use Traits\DefaultBuilder;
	use Traits\PreInitializer;
	use Traits\RequiredPropertyNamesLoader;
	use Traits\PropertyBuilder;
	use Traits\Initializer;
	use Traits\DefaultPrototypeProducer;
	use Traits\PrototypeInitializer;
	use Traits\PrototypeProducer;
	use Traits\ProxyClass;
	
	
	
	//Private properties
	/** @var \Dracodeum\Kit\Prototype */
	private $prototype;
	
	/** @var \Dracodeum\Kit\Proxy|null */
	private $proxy = null;
	
	
	
	//Private static properties
	/** @var \Dracodeum\Kit\Component\PrototypeProducer[] */
	private static $prototype_producers = [];
	
	
	
	//Final public magic methods
	/**
	 * Instantiate class.
	 * 
	 * @param \Dracodeum\Kit\Prototype|string|null $prototype [default = null]
	 * <p>The prototype instance, class or name to instantiate with.<br>
	 * If not set, then the default prototype instance or the base prototype class is used.</p>
	 * @param array $properties [default = []]
	 * <p>The properties to instantiate with, as <samp>name => value</samp> pairs, 
	 * if a prototype class or name, or <code>null</code>, is given.<br>
	 * Required properties may also be given as an array of values (<samp>[value1, value2, ...]</samp>), 
	 * in the same order as how these properties were first declared.<br>
	 * <br>
	 * They are applied to both the component and prototype.</p>
	 */
	final public function __construct($prototype = null, array $properties = [])
	{
		//pre-initialize
		$this->preInitialize($prototype, $properties);
		
		//remainderer
		$remainderer = function (array $properties) use ($prototype): void {
			//prototype base class
			$prototype_base_class = $this->getBasePrototypeClass();
			UCall::guardInternal(UType::isA($prototype_base_class, Prototype::class), [
				'error_message' => "Invalid base prototype class {{base_class}}.",
				'parameters' => ['base_class' => $prototype_base_class],
				'function_name' => '__construct'
			]);
			
			//prototype
			if (!isset($prototype)) {
				//build
				$prototype = UCall::guardExecution(
					\Closure::fromCallable([$this, 'produceDefaultPrototype']),
					[$properties],
					function (&$value) use ($prototype_base_class, $properties): bool {
						if (isset($value)) {
							$value = UType::coerceObjectClass($value, $prototype_base_class);
							if (!is_object($value)) {
								$value = UType::instantiate($value, $properties);
							}
						}
						return true;
					},
					['function_name' => '__construct']
				);
				
				//check
				if (isset($prototype)) {
					$properties = [];
				} else {
					$prototype = $prototype_base_class;
				}
			} elseif (is_string($prototype)) {
				//initialize
				$instance = null;
				$callback = function (&$value) use ($prototype_base_class, $properties): bool {
					$value = UType::coerceObject($value, $prototype_base_class, [$properties], true);
					return true;
				};
				
				//build (prototype producers)
				if (!empty(self::$prototype_producers)) {
					for ($class = static::class; $class !== false; $class = get_parent_class($class)) {
						//producers
						if (!empty(self::$prototype_producers[$class])) {
							foreach (self::$prototype_producers[$class] as $prototype_producer) {
								//instance
								$instance = UCall::guardExecution(
									[$prototype_producer, 'produce'], [$prototype, $properties],
									$callback, ['function_name' => '__construct']
								);
								
								//check
								if (isset($instance)) {
									break;
								}
							}
						}
						
						//check
						if (isset($instance)) {
							break;
						}
					}
				}
				
				//build (method)
				if (!isset($instance)) {
					$instance = UCall::guardExecution(
						\Closure::fromCallable([$this, 'producePrototype']), [$prototype, $properties],
						$callback, ['function_name' => '__construct']
					);
				}
				
				//check
				if (isset($instance)) {
					$prototype = $instance;
					$properties = [];
				} else {
					UCall::guardParameter('prototype', $prototype, class_exists($prototype), [
						'error_message' => "Prototype name not found.",
						'function_name' => '__construct'
					]);
				}
			} else {
				UCall::guardParameter('prototype', $prototype, is_object($prototype), [
					'hint_message' => "Only an instance, class or name is allowed.",
					'function_name' => '__construct'
				]);
			}
			
			//guard prototype
			UCall::guardParameter('prototype', $prototype, UType::isA($prototype, $prototype_base_class), [
				'error_message' => "Invalid prototype class.",
				'hint_message' => "Only a class or subclass of {{base_class}} is allowed for this component.",
				'parameters' => ['base_class' => $prototype_base_class],
				'function_name' => '__construct'
			]);
			
			//prototype instantiation
			if (is_string($prototype)) {
				$prototype = UType::instantiate($prototype, $properties);
			} else {
				UCall::guardParameter('properties', $properties, empty($properties), [
					'hint_message' => "Prototype specific properties are only allowed to be given whenever " . 
						"the prototype is given as a class, a name or not given at all.",
					'function_name' => '__construct'
				]);
			}
			$prototype->setComponent($this);
			$this->initializePrototype($prototype);
			$this->prototype = $prototype;
		};
		
		//properties
		$this->initializeProperties(
			\Closure::fromCallable([$this, 'buildProperty']), $properties,
			\Closure::fromCallable([$this, 'loadRequiredPropertyNames']), 'rw', false, $remainderer
		);
		$this->setPropertiesFallbackObject($this->prototype);
		
		//initialize
		$this->initialize();
	}
	
	
	
	//Abstract public static methods
	/**
	 * Get base prototype class.
	 * 
	 * Any prototype class or instance given to be used by this component must be or 
	 * extend from the same class as the base prototype class returned here.
	 * 
	 * @return string
	 * <p>The base prototype class.</p>
	 */
	abstract public static function getBasePrototypeClass(): string;
	
	
	
	//Implemented public methods (Dracodeum\Kit\Traits\DebugInfo\Interfaces\DebugInfoProcessor)
	/** {@inheritdoc} */
	public function processDebugInfo(DebugInfo $info): void
	{
		//initialize
		$this->processPropertiesDebugInfo($info);
		$info->enableObjectPropertiesDump();
		
		//ignored properties
		if ($this->proxy === null) {
			$info->hideObjectProperty('proxy', self::class);
		}
	}
	
	
	
	//Final public methods
	/**
	 * Check if has proxy.
	 * 
	 * @return bool
	 * <p>Boolean <code>true</code> if has proxy.</p>
	 */
	final public function hasProxy(): bool
	{
		return $this->getProxy(true) !== null;
	}
	
	/**
	 * Get proxy instance.
	 * 
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Component\Exceptions\ProxyNotSet
	 * @return \Dracodeum\Kit\Proxy|null
	 * <p>The proxy instance.<br>
	 * If <var>$no_throw</var> is set to boolean <code>true</code>, 
	 * then <code>null</code> is returned if none is set.</p>
	 */
	final public function getProxy(bool $no_throw = false): ?Proxy
	{
		if ($this->proxy === null) {
			$class = $this->getProxyClass();
			if ($class === null) {
				if ($no_throw) {
					return null;
				}
				throw new Exceptions\ProxyNotSet([$this]);
			}
			$this->proxy = UType::coerceObject($class, Proxy::class, [$this]);
		}
		return $this->proxy;
	}
	
	
	
	//Final public static methods
	/**
	 * Build instance.
	 * 
	 * @param \Dracodeum\Kit\Prototype|string|null $prototype [default = null]
	 * <p>The prototype instance, class or name to build with.<br>
	 * If not set, then the default prototype instance or the base prototype class is used.</p>
	 * @param array $properties [default = []]
	 * <p>The properties to build with, as <samp>name => value</samp> pairs, 
	 * if a prototype class or name, or <code>null</code>, is given.<br>
	 * Required properties may also be given as an array of values (<samp>[value1, value2, ...]</samp>), 
	 * in the same order as how these properties were first declared.<br>
	 * <br>
	 * They are applied to both the component and prototype.</p>
	 * @return static
	 * <p>The built instance.</p>
	 */
	final public static function build($prototype = null, array $properties = []): Component
	{
		$builder = static::getDefaultBuilder();
		if (isset($builder)) {
			UCall::assert('builder', $builder, function ($prototype, array $properties): Component {});
			return UType::coerceObject($builder($prototype, $properties), static::class);
		}
		return new static($prototype, $properties);
	}
	
	/**
	 * Produce an instance from a given component or prototype.
	 * 
	 * @param \Dracodeum\Kit\Component|\Dracodeum\Kit\Prototype|string $component_prototype
	 * <p>The component instance or name, or prototype instance, class or name, to produce from.</p>
	 * @param array $properties [default = []]
	 * <p>The properties to produce with, as <samp>name => value</samp> pairs, 
	 * if a component name, or a prototype class or name, is given.<br>
	 * Required properties may also be given as an array of values (<samp>[value1, value2, ...]</samp>), 
	 * in the same order as how these properties were first declared.</p>
	 * @param callable|null $producer [default = null]
	 * <p>The function to use to produce a component instance or name, or a prototype instance, class or name, 
	 * for a given name with a given set of properties.<br>
	 * It is expected to be compatible with the following signature:<br>
	 * <br>
	 * <code>function (string $name, array $properties)</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>string $name</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The name to produce for.<br>
	 * &nbsp; &#8226; &nbsp; <code><b>array $properties</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The properties to produce with, as <samp>name => value</samp> pairs.<br>
	 * &nbsp; &nbsp; &nbsp; Required properties may also be given as an array of values 
	 * (<samp>[value1, value2, ...]</samp>), in the same order as how these properties were first declared.<br>
	 * <br>
	 * Return: <code><b>Dracodeum\Kit\Component|Dracodeum\Kit\Prototype|string|null</b></code><br>
	 * The produced component instance or name, or prototype instance, class or name, 
	 * for the given name with the given set of properties, or <code>null</code> if none was produced.</p>
	 * @param callable|null $builder [default = null]
	 * <p>The function to use to build an instance with a given prototype and set of properties.<br>
	 * It is expected to be compatible with the following signature:<br>
	 * <br>
	 * <code>function ($prototype, array $properties): Dracodeum\Kit\Component</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>Dracodeum\Kit\Prototype|string|null $prototype</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The prototype instance, class or name to build with.<br>
	 * &nbsp; &nbsp; &nbsp; If not set, then the default prototype instance or the base prototype class is used.<br>
	 * &nbsp; &#8226; &nbsp; <code><b>array $properties</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The properties to build with, as <samp>name => value</samp> pairs, 
	 * if a prototype class or name, or <code>null</code>, is given.<br>
	 * &nbsp; &nbsp; &nbsp; Required properties may also be given as an array of values 
	 * (<samp>[value1, value2, ...]</samp>), in the same order as how these properties were first declared.<br>
	 * <br>
	 * Return: <code><b>Dracodeum\Kit\Component</b></code><br>
	 * The built instance with the given prototype and set of properties.</p>
	 * @return static
	 * <p>The produced instance from the given component or prototype.</p>
	 */
	final public static function produce(
		$component_prototype, array $properties = [], ?callable $producer = null, ?callable $builder = null
	): Component
	{
		//assert
		if (isset($producer)) {
			UCall::assert('producer', $producer, function (string $name, array $properties) {});
		}
		if (isset($builder)) {
			UCall::assert('builder', $builder, function ($prototype, array $properties): Component {});
		}
		
		//produce
		if (is_string($component_prototype) && isset($producer)) {
			//instance
			$instance = UCall::guardExecution(
				$producer, [$component_prototype, $properties],
				function (&$value) use ($properties, $builder): bool {
					if (isset($value)) {
						$value = static::coerce($value, $properties, $builder);
					}
					return true;
				}
			);
			
			//return
			if (isset($instance)) {
				return $instance;
			}
		}
		
		//return
		return static::coerce($component_prototype, $properties, $builder);
	}
	
	/**
	 * Evaluate a given value as an instance.
	 * 
	 * Only a component instance or name, or a prototype instance, class or name, can be evaluated into an instance.
	 * 
	 * @param mixed $value [reference]
	 * <p>The value to evaluate (validate and sanitize).</p>
	 * @param array $properties [default = []]
	 * <p>The properties to evaluate with, as <samp>name => value</samp> pairs, 
	 * if a component name, or a prototype class or name, or <code>null</code>, is given.<br>
	 * Required properties may also be given as an array of values (<samp>[value1, value2, ...]</samp>), 
	 * in the same order as how these properties were first declared.</p>
	 * @param callable|null $builder [default = null]
	 * <p>The function to use to build an instance with a given prototype and set of properties.<br>
	 * It is expected to be compatible with the following signature:<br>
	 * <br>
	 * <code>function ($prototype, array $properties): Dracodeum\Kit\Component</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>Dracodeum\Kit\Prototype|string|null $prototype</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The prototype instance, class or name to build with.<br>
	 * &nbsp; &nbsp; &nbsp; If not set, then the default prototype instance or the base prototype class is used.<br>
	 * &nbsp; &#8226; &nbsp; <code><b>array $properties</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The properties to build with, as <samp>name => value</samp> pairs, 
	 * if a prototype class or name, or <code>null</code>, is given.<br>
	 * &nbsp; &nbsp; &nbsp; Required properties may also be given as an array of values 
	 * (<samp>[value1, value2, ...]</samp>), in the same order as how these properties were first declared.<br>
	 * <br>
	 * Return: <code><b>Dracodeum\Kit\Component</b></code><br>
	 * The built instance with the given prototype and set of properties.</p>
	 * @param callable|null $named_builder [default = null]
	 * <p>The function to use to build an instance for a given name with a given set of properties.<br>
	 * It is expected to be compatible with the following signature:<br>
	 * <br>
	 * <code>function (string $name, array $properties): ?Dracodeum\Kit\Component</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>string $name</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The name to build for.<br>
	 * &nbsp; &#8226; &nbsp; <code><b>array $properties</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The properties to build with, as <samp>name => value</samp> pairs.<br>
	 * &nbsp; &nbsp; &nbsp; Required properties may also be given as an array of values 
	 * (<samp>[value1, value2, ...]</samp>), in the same order as how these properties were first declared.<br>
	 * <br>
	 * Return: <code><b>Dracodeum\Kit\Component|null</b></code><br>
	 * The built instance for the given name with the given set of properties 
	 * or <code>null</code> if none was built.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to evaluate as <code>null</code>.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value was successfully evaluated into an instance.</p>
	 */
	final public static function evaluate(
		&$value, array $properties = [], ?callable $builder = null, ?callable $named_builder = null,
		bool $nullable = false
	): bool
	{
		return self::processCoercion($value, $properties, $builder, $named_builder, $nullable, true);
	}
	
	/**
	 * Coerce a given value into an instance.
	 * 
	 * Only a component instance or name, or a prototype instance, class or name, can be coerced into an instance.
	 * 
	 * @param mixed $value
	 * <p>The value to coerce (validate and sanitize).</p>
	 * @param array $properties [default = []]
	 * <p>The properties to coerce with, as <samp>name => value</samp> pairs, 
	 * if a component name, or a prototype class or name, or <code>null</code>, is given.<br>
	 * Required properties may also be given as an array of values (<samp>[value1, value2, ...]</samp>), 
	 * in the same order as how these properties were first declared.</p>
	 * @param callable|null $builder [default = null]
	 * <p>The function to use to build an instance with a given prototype and set of properties.<br>
	 * It is expected to be compatible with the following signature:<br>
	 * <br>
	 * <code>function ($prototype, array $properties): Dracodeum\Kit\Component</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>Dracodeum\Kit\Prototype|string|null $prototype</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The prototype instance, class or name to build with.<br>
	 * &nbsp; &nbsp; &nbsp; If not set, then the default prototype instance or the base prototype class is used.<br>
	 * &nbsp; &#8226; &nbsp; <code><b>array $properties</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The properties to build with, as <samp>name => value</samp> pairs, 
	 * if a prototype class or name, or <code>null</code>, is given.<br>
	 * &nbsp; &nbsp; &nbsp; Required properties may also be given as an array of values 
	 * (<samp>[value1, value2, ...]</samp>), in the same order as how these properties were first declared.<br>
	 * <br>
	 * Return: <code><b>Dracodeum\Kit\Component</b></code><br>
	 * The built instance with the given prototype and set of properties.</p>
	 * @param callable|null $named_builder [default = null]
	 * <p>The function to use to build an instance for a given name with a given set of properties.<br>
	 * It is expected to be compatible with the following signature:<br>
	 * <br>
	 * <code>function (string $name, array $properties): ?Dracodeum\Kit\Component</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>string $name</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The name to build for.<br>
	 * &nbsp; &#8226; &nbsp; <code><b>array $properties</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The properties to build with, as <samp>name => value</samp> pairs.<br>
	 * &nbsp; &nbsp; &nbsp; Required properties may also be given as an array of values 
	 * (<samp>[value1, value2, ...]</samp>), in the same order as how these properties were first declared.<br>
	 * <br>
	 * Return: <code><b>Dracodeum\Kit\Component|null</b></code><br>
	 * The built instance for the given name with the given set of properties 
	 * or <code>null</code> if none was built.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @throws \Dracodeum\Kit\Component\Exceptions\CoercionFailed
	 * @return static|null
	 * <p>The given value coerced into an instance.<br>
	 * If nullable, then <code>null</code> may also be returned.</p>
	 */
	final public static function coerce(
		$value, array $properties = [], ?callable $builder = null, ?callable $named_builder = null,
		bool $nullable = false
	): ?Component
	{
		self::processCoercion($value, $properties, $builder, $named_builder, $nullable);
		return $value;
	}
	
	/**
	 * Process the coercion of a given value into an instance.
	 * 
	 * Only a component instance or name, or a prototype instance, class or name, can be coerced into an instance.
	 * 
	 * @param mixed $value [reference]
	 * <p>The value to process (validate and sanitize).</p>
	 * @param array $properties [default = []]
	 * <p>The properties to coerce with, as <samp>name => value</samp> pairs, 
	 * if a component name, or a prototype class or name, or <code>null</code>, is given.<br>
	 * Required properties may also be given as an array of values (<samp>[value1, value2, ...]</samp>), 
	 * in the same order as how these properties were first declared.</p>
	 * @param callable|null $builder [default = null]
	 * <p>The function to use to build an instance with a given prototype and set of properties.<br>
	 * It is expected to be compatible with the following signature:<br>
	 * <br>
	 * <code>function ($prototype, array $properties): Dracodeum\Kit\Component</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>Dracodeum\Kit\Prototype|string|null $prototype</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The prototype instance, class or name to build with.<br>
	 * &nbsp; &nbsp; &nbsp; If not set, then the default prototype instance or the base prototype class is used.<br>
	 * &nbsp; &#8226; &nbsp; <code><b>array $properties</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The properties to build with, as <samp>name => value</samp> pairs, 
	 * if a prototype class or name, or <code>null</code>, is given.<br>
	 * &nbsp; &nbsp; &nbsp; Required properties may also be given as an array of values 
	 * (<samp>[value1, value2, ...]</samp>), in the same order as how these properties were first declared.<br>
	 * <br>
	 * Return: <code><b>Dracodeum\Kit\Component</b></code><br>
	 * The built instance with the given prototype and set of properties.</p>
	 * @param callable|null $named_builder [default = null]
	 * <p>The function to use to build an instance for a given name with a given set of properties.<br>
	 * It is expected to be compatible with the following signature:<br>
	 * <br>
	 * <code>function (string $name, array $properties): ?Dracodeum\Kit\Component</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>string $name</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The name to build for.<br>
	 * &nbsp; &#8226; &nbsp; <code><b>array $properties</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The properties to build with, as <samp>name => value</samp> pairs.<br>
	 * &nbsp; &nbsp; &nbsp; Required properties may also be given as an array of values 
	 * (<samp>[value1, value2, ...]</samp>), in the same order as how these properties were first declared.<br>
	 * <br>
	 * Return: <code><b>Dracodeum\Kit\Component|null</b></code><br>
	 * The built instance for the given name with the given set of properties 
	 * or <code>null</code> if none was built.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Component\Exceptions\CoercionFailed
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value was successfully coerced into an instance.</p>
	 */
	final public static function processCoercion(
		&$value, array $properties = [], ?callable $builder = null, ?callable $named_builder = null,
		bool $nullable = false, bool $no_throw = false
	): bool
	{
		//check
		if ((!isset($value) && $nullable) || (is_object($value) && UType::isA($value, static::class))) {
			return true;
		} elseif (isset($value) && !is_string($value) && (!is_object($value) || !($value instanceof Prototype))) {
			if ($no_throw) {
				return false;
			}
			throw new Exceptions\CoercionFailed([
				'value' => $value,
				'component' => static::class,
				'error_code' => Exceptions\CoercionFailed::ERROR_CODE_INVALID_TYPE,
				'error_message' => "Only a component instance or name, " . 
					"or a prototype instance, class or name, is allowed."
			]);
		}
		
		//named builder
		if (isset($named_builder) && is_string($value)) {
			//assert
			UCall::assert('named_builder', $named_builder, function (string $name, array $properties): ?Component {});
			
			//build
			$instance = null;
			try {
				$instance = $named_builder($value, $properties);
			} catch (\Exception $exception) {
				if ($no_throw) {
					return false;
				}
				throw new Exceptions\CoercionFailed([
					'value' => $value,
					'component' => static::class,
					'error_code' => Exceptions\CoercionFailed::ERROR_CODE_BUILD_EXCEPTION,
					'error_message' => $exception->getMessage()
				]);
			}
			
			//check
			if (isset($instance)) {
				try {
					$value = UType::coerceObject($instance, static::class);
					return true;
				} catch (UTypeExceptions\ObjectCoercionFailed $exception) {
					if ($no_throw) {
						return false;
					}
					throw new Exceptions\CoercionFailed([
						'value' => $value,
						'component' => static::class,
						'error_code' => Exceptions\CoercionFailed::ERROR_CODE_BUILD_EXCEPTION,
						'error_message' => $exception->getMessage()
					]);
				}
			}
		}
		
		//builder
		if (isset($builder)) {
			UCall::assert('builder', $builder, function ($prototype, array $properties): Component {});
			try {
				$value = UType::coerceObject($builder($value, $properties), static::class);
				return true;
			} catch (\Exception $exception) {
				if ($no_throw) {
					return false;
				}
				throw new Exceptions\CoercionFailed([
					'value' => $value,
					'component' => static::class,
					'error_code' => Exceptions\CoercionFailed::ERROR_CODE_BUILD_EXCEPTION,
					'error_message' => $exception->getMessage()
				]);
			}
		}
		
		//build
		try {
			$value = static::build($value, $properties);
			return true;
		} catch (\Exception $exception) {
			if ($no_throw) {
				return false;
			}
			throw new Exceptions\CoercionFailed([
				'value' => $value,
				'component' => static::class,
				'error_code' => Exceptions\CoercionFailed::ERROR_CODE_BUILD_EXCEPTION,
				'error_message' => $exception->getMessage()
			]);
		}
		
		//finish
		if ($no_throw) {
			return false;
		}
		throw new Exceptions\CoercionFailed(['value' => $value, 'component' => static::class]);
	}
	
	/**
	 * Prepend prototype producer.
	 * 
	 * @param \Dracodeum\Kit\Component\PrototypeProducer|string $prototype_producer
	 * <p>The prototype producer instance or class to prepend.</p>
	 * @return void
	 */
	final public static function prependPrototypeProducer($prototype_producer): void
	{
		$prototype_producer = UType::coerceObject($prototype_producer, PrototypeProducer::class);
		if (isset(self::$prototype_producers[static::class])) {
			array_unshift(self::$prototype_producers[static::class], $prototype_producer);
		} else {
			self::$prototype_producers[static::class][] = $prototype_producer;
		}
	}
	
	/**
	 * Append prototype producer.
	 * 
	 * @param \Dracodeum\Kit\Component\PrototypeProducer|string $prototype_producer
	 * <p>The prototype producer instance or class to append.</p>
	 * @return void
	 */
	final public static function appendPrototypeProducer($prototype_producer): void
	{
		$prototype_producer = UType::coerceObject($prototype_producer, PrototypeProducer::class);
		self::$prototype_producers[static::class][] = $prototype_producer;
	}
	
	
	
	//Final protected methods
	/**
	 * Get prototype instance.
	 * 
	 * @return \Dracodeum\Kit\Prototype
	 * <p>The prototype instance.</p>
	 */
	final protected function getPrototype(): Prototype
	{
		return $this->prototype;
	}
	
	/**
	 * Halt the current method call in the stack over a given prototype method not being implemented.
	 * 
	 * @param string $name
	 * <p>The prototype method name to use.</p>
	 * @return void
	 */
	final protected function haltPrototypeMethodNotImplemented(string $name): void
	{
		UCall::halt([
			'error_message' => "Method {{name}} not implemented in prototype {{prototype}}.",
			'parameters' => [
				'name' => $name,
				'prototype' => $this->prototype
			],
			'stack_offset' => 1
		]);
	}
}
