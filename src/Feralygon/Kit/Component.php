<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit;

use Feralygon\Kit\Interfaces\Propertiesable as IPropertiesable;
use Feralygon\Kit\Component\{
	Exceptions,
	Traits,
	Proxy
};
use Feralygon\Kit\Traits as KitTraits;
use Feralygon\Kit\Utilities\{
	Call as UCall,
	Text as UText,
	Type as UType
};
use Feralygon\Kit\Utilities\Type\Exceptions as UTypeExceptions;

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
 * which generally shares the same class name as that component, but reside under different namespaces, 
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
 * or to implement them with a different signature.<br>
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
 * @since 1.0.0
 * @see \Feralygon\Kit\Prototype
 * @see \Feralygon\Kit\Component\Proxy
 * @see \Feralygon\Kit\Component\Traits\DefaultBuilder
 * @see \Feralygon\Kit\Component\Traits\PreInitializer
 * @see \Feralygon\Kit\Component\Traits\RequiredPropertyNamesLoader
 * @see \Feralygon\Kit\Component\Traits\PropertyBuilder
 * @see \Feralygon\Kit\Component\Traits\Initializer
 * @see \Feralygon\Kit\Component\Traits\DefaultPrototypeProducer
 * @see \Feralygon\Kit\Component\Traits\PrototypeInitializer
 * @see \Feralygon\Kit\Component\Traits\PrototypeProducer
 * @see \Feralygon\Kit\Component\Traits\ProxyProducer
 */
abstract class Component implements IPropertiesable
{
	//Traits
	use KitTraits\LazyProperties;
	use Traits\DefaultBuilder;
	use Traits\PreInitializer;
	use Traits\RequiredPropertyNamesLoader;
	use Traits\PropertyBuilder;
	use Traits\Initializer;
	use Traits\DefaultPrototypeProducer;
	use Traits\PrototypeInitializer;
	use Traits\PrototypeProducer;
	use Traits\ProxyProducer;
	
	
	
	//Private properties
	/** @var \Feralygon\Kit\Prototype */
	private $prototype;
	
	/** @var \Feralygon\Kit\Component\Proxy|null */
	private $proxy = null;
	
	
	
	//Final public magic methods
	/**
	 * Instantiate class.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Prototype|string|null $prototype [default = null]
	 * <p>The prototype instance, class or name.<br>
	 * If not set, then the default prototype instance or the base prototype class is used.</p>
	 * @param array $properties [default = []]
	 * <p>The properties, as <samp>name => value</samp> pairs.<br>
	 * Required properties may also be given as an array of values (<samp>[value1, value2, ...]</samp>), 
	 * in the same order as how these properties were first declared.<br>
	 * <br>
	 * They are applied to both the component and prototype.</p>
	 */
	final public function __construct($prototype = null, array $properties = [])
	{
		//pre-initialize
		$this->preInitialize();
		
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
								$value = new $value($properties);
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
				//build
				$instance = UCall::guardExecution(
					\Closure::fromCallable([$this, 'producePrototype']),
					[$prototype, $properties],
					function (&$value) use ($prototype_base_class, $properties): bool {
						if (isset($value)) {
							$value = UType::coerceObjectClass($value, $prototype_base_class);
							if (!is_object($value)) {
								$value = new $value($properties);
							}
						}
						return true;
					},
					['function_name' => '__construct']
				);
				
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
				$prototype = new $prototype($properties);
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
			\Closure::fromCallable([$this, 'loadRequiredPropertyNames']), 'rw', $remainderer
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
	 * @since 1.0.0
	 * @return string
	 * <p>The base prototype class.</p>
	 */
	abstract public static function getBasePrototypeClass(): string;
	
	
	
	//Final public methods
	/**
	 * Check if has proxy.
	 * 
	 * @since 1.0.0
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
	 * @since 1.0.0
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Feralygon\Kit\Component\Exceptions\ProxyNotSet
	 * @return \Feralygon\Kit\Component\Proxy|null
	 * <p>The proxy instance.<br>
	 * If <var>$no_throw</var> is set to <code>true</code>, then <code>null</code> is returned if none is set.</p>
	 */
	final public function getProxy(bool $no_throw = false): ?Proxy
	{
		if (!isset($this->proxy)) {
			$proxy = UType::coerceObject($this->produceProxy(), Proxy::class, [], true);
			if (isset($proxy)) {
				$proxy->setComponent($this);
			} elseif ($no_throw) {
				return null;
			} else {
				throw new Exceptions\ProxyNotSet([$this]);
			}
			$this->proxy = $proxy;
		}
		return $this->proxy;
	}
	
	
	
	//Final public static methods
	/**
	 * Build instance.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Prototype|string|null $prototype [default = null]
	 * <p>The prototype instance, class or name to build with.<br>
	 * If not set, then the default prototype instance or the base prototype class is used.</p>
	 * @param array $properties [default = []]
	 * <p>The properties to build with, as <samp>name => value</samp> pairs.<br>
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
			return $builder($prototype, $properties);
		}
		return new static($prototype, $properties);
	}
	
	/**
	 * Evaluate a given value as an instance.
	 * 
	 * Only a component instance or name, or a prototype instance, class or name, can be evaluated into an instance.
	 * 
	 * @since 1.0.0
	 * @param mixed $value [reference]
	 * <p>The value to evaluate (validate and sanitize).</p>
	 * @param array $properties [default = []]
	 * <p>The properties to evaluate with, as <samp>name => value</samp> pairs.<br>
	 * Required properties may also be given as an array of values (<samp>[value1, value2, ...]</samp>), 
	 * in the same order as how these properties were first declared.<br>
	 * <br>
	 * If a component instance is given, then the given properties are ignored.</p>
	 * @param callable|null $builder [default = null]
	 * <p>The function to use to build an instance.<br>
	 * It is expected to be compatible with the following signature:<br>
	 * <br>
	 * <code>function ($prototype, array $properties): Feralygon\Kit\Component</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>Feralygon\Kit\Prototype|string $prototype</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The prototype instance, class or name to build with.<br>
	 * &nbsp; &#8226; &nbsp; <code><b>array $properties</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The properties to build with, as <samp>name => value</samp> pairs.<br>
	 * &nbsp; &nbsp; &nbsp; Required properties may also be given as an array of values 
	 * (<samp>[value1, value2, ...]</samp>), in the same order as how these properties were first declared.<br>
	 * <br>
	 * Return: <code><b>Feralygon\Kit\Component</b></code><br>
	 * The built instance.</p>
	 * @param callable|null $named_builder [default = null]
	 * <p>The function to use to build an instance for a given name.<br>
	 * It is expected to be compatible with the following signature:<br>
	 * <br>
	 * <code>function (string $name, array $properties): ?Feralygon\Kit\Component</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>string $name</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The name to build for.<br>
	 * &nbsp; &#8226; &nbsp; <code><b>array $properties</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The properties to build with, as <samp>name => value</samp> pairs.<br>
	 * &nbsp; &nbsp; &nbsp; Required properties may also be given as an array of values 
	 * (<samp>[value1, value2, ...]</samp>), in the same order as how these properties were first declared.<br>
	 * <br>
	 * Return: <code><b>Feralygon\Kit\Component|null</b></code><br>
	 * The built instance for the given name or <code>null</code> if none was built.</p>
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
		try {
			$value = static::coerce($value, $properties, $builder, $named_builder, $nullable);
		} catch (Exceptions\CoercionFailed $exception) {
			return false;
		}
		return true;
	}
	
	/**
	 * Coerce a given value into an instance.
	 * 
	 * Only a component instance or name, or a prototype instance, class or name, can be coerced into an instance.
	 * 
	 * @since 1.0.0
	 * @param mixed $value
	 * <p>The value to coerce (validate and sanitize).</p>
	 * @param array $properties [default = []]
	 * <p>The properties to coerce with, as <samp>name => value</samp> pairs.<br>
	 * Required properties may also be given as an array of values (<samp>[value1, value2, ...]</samp>), 
	 * in the same order as how these properties were first declared.<br>
	 * <br>
	 * If a component instance is given, then the given properties are ignored.</p>
	 * @param callable|null $builder [default = null]
	 * <p>The function to use to build an instance.<br>
	 * It is expected to be compatible with the following signature:<br>
	 * <br>
	 * <code>function ($prototype, array $properties): Feralygon\Kit\Component</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>Feralygon\Kit\Prototype|string $prototype</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The prototype instance, class or name to build with.<br>
	 * &nbsp; &#8226; &nbsp; <code><b>array $properties</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The properties to build with, as <samp>name => value</samp> pairs.<br>
	 * &nbsp; &nbsp; &nbsp; Required properties may also be given as an array of values 
	 * (<samp>[value1, value2, ...]</samp>), in the same order as how these properties were first declared.<br>
	 * <br>
	 * Return: <code><b>Feralygon\Kit\Component</b></code><br>
	 * The built instance.</p>
	 * @param callable|null $named_builder [default = null]
	 * <p>The function to use to build an instance for a given name.<br>
	 * It is expected to be compatible with the following signature:<br>
	 * <br>
	 * <code>function (string $name, array $properties): ?Feralygon\Kit\Component</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>string $name</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The name to build for.<br>
	 * &nbsp; &#8226; &nbsp; <code><b>array $properties</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The properties to build with, as <samp>name => value</samp> pairs.<br>
	 * &nbsp; &nbsp; &nbsp; Required properties may also be given as an array of values 
	 * (<samp>[value1, value2, ...]</samp>), in the same order as how these properties were first declared.<br>
	 * <br>
	 * Return: <code><b>Feralygon\Kit\Component|null</b></code><br>
	 * The built instance for the given name or <code>null</code> if none was built.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @throws \Feralygon\Kit\Component\Exceptions\CoercionFailed
	 * @return static|null
	 * <p>The given value coerced into an instance.<br>
	 * If nullable, then <code>null</code> may also be returned.</p>
	 */
	final public static function coerce(
		$value, array $properties = [], ?callable $builder = null, ?callable $named_builder = null,
		bool $nullable = false
	): ?Component
	{
		//check
		if (!isset($value)) {
			return $nullable ? null : new static(null, $properties);
		} elseif (is_object($value) && UType::isA($value, static::class)) {
			return $value;
		} elseif (!is_string($value) && (!is_object($value) || !($value instanceof Prototype))) {
			throw new Exceptions\CoercionFailed([
				'value' => $value,
				'component' => static::class,
				'error_code' => Exceptions\CoercionFailed::ERROR_CODE_INVALID_TYPE,
				'error_message' => "Only a component instance or name, or a prototype instance, class or name, " . 
					"is allowed."
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
					$instance = UType::coerceObject($instance, static::class);
				} catch (UTypeExceptions\ObjectCoercionFailed $exception) {
					throw new Exceptions\CoercionFailed([
						'value' => $value,
						'component' => static::class,
						'error_code' => Exceptions\CoercionFailed::ERROR_CODE_BUILD_EXCEPTION,
						'error_message' => $exception->getMessage()
					]);
				}
				return $instance;
			} elseif (!class_exists($value)) {
				throw new Exceptions\CoercionFailed([
					'value' => $value,
					'component' => static::class,
					'error_code' => Exceptions\CoercionFailed::ERROR_CODE_BUILD_EXCEPTION,
					'error_message' => UText::fill(
						"Component name {{name}} not found.", ['name' => $value], null, [
							'string_options' => ['quote_strings' => true]
						]
					)
				]);
			}
		}
		
		//builder
		if (!isset($builder)) {
			$builder = static::getDefaultBuilder();
		}
		if (isset($builder)) {
			UCall::assert('builder', $builder, function ($prototype, array $properties): Component {});
			try {
				return UType::coerceObject($builder($value, $properties), static::class);
			} catch (\Exception $exception) {
				throw new Exceptions\CoercionFailed([
					'value' => $value,
					'component' => static::class,
					'error_code' => Exceptions\CoercionFailed::ERROR_CODE_BUILD_EXCEPTION,
					'error_message' => $exception->getMessage()
				]);
			}
		}
		
		//finish
		try {
			return new static($value, $properties);
		} catch (\Exception $exception) {
			throw new Exceptions\CoercionFailed([
				'value' => $value,
				'component' => static::class,
				'error_code' => Exceptions\CoercionFailed::ERROR_CODE_BUILD_EXCEPTION,
				'error_message' => $exception->getMessage()
			]);
		}
		
		//throw
		throw new Exceptions\CoercionFailed(['value' => $value, 'component' => static::class]);
	}
	
	
	
	//Final protected methods
	/**
	 * Get prototype instance.
	 * 
	 * @since 1.0.0
	 * @return \Feralygon\Kit\Prototype
	 * <p>The prototype instance.</p>
	 */
	final protected function getPrototype(): Prototype
	{
		return $this->prototype;
	}
}
