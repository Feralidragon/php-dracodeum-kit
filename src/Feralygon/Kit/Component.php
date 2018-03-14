<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit;

use Feralygon\Kit\Component\{
	Exceptions,
	Traits
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
 * a very high number of implementations, each one tailored to each specific purpose, but with its core behavior, 
 * functionality and interface mostly intact.<br>
 * <br>
 * Examples of this kind of object are inputs, outputs, tables, parameters, filters, constraints, models, controllers, 
 * handlers, and others, all of which are expected to have tens or even hundreds of different internal implementations 
 * under a common functional interface when seen and used by other objects.<br>
 * <br>
 * The implementation of a component is performed through a <b>prototype</b> object.<br>
 * <br>
 * Any methods meant to internally implement a component are declared as abstract in a prototype instead, 
 * generally sharing the same class name as the component, but under a different namespace, resulting in the component 
 * using the prototype to define the details of its internal behavior.<br>
 * <br>
 * While every method declared in a prototype must be implemented, additional interfaces recognized by the component 
 * may be defined and implemented in the prototype to enable additional optional internal features.<br>
 * <br>
 * A prototype is never aware of which specific component is using it, given that there is no back reference to it, 
 * and a component never exposes its prototype to other outside objects, ensuring that every public method defined 
 * in a prototype remains hidden from other objects and to be exclusively used by its component alone.<br>
 * Additionally, a component may be extended or recreated to modify or refactor any internal behavior and still be able 
 * to reuse all the existing prototypes in the same way.<br>
 * <br>
 * Both components and prototypes may also have a layer of custom lazy-loaded properties, 
 * which may be given during instantiation.<br>
 * While all readable properties from a component may be accessed from any scope, in the case of a prototype they 
 * are effectively only visible to itself and the component using it.<br>
 * <br>
 * A prototype may also require to have existing functions bound to itself by a component, which must be compatible 
 * with the function templates defined by the prototype itself, and which may or may not correspond to 
 * actual methods from the component itself.<br>
 * <br>
 * While the prototype to use may be given through its class or an instance (dependency injection pattern), 
 * a component may also map specific names towards specific prototypes, so that a prototype may also be instantiated 
 * and used through the usage of a name instead, so that the class to use does not need to be known ahead of time 
 * (factory pattern).<br>
 * This allows for both dependency injection and factory patterns to be used with prototypes, simultaneously.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Prototype
 * @see \Feralygon\Kit\Component\Traits\Properties
 * @see \Feralygon\Kit\Component\Traits\Initialization
 * @see \Feralygon\Kit\Component\Traits\DefaultPrototype
 * @see \Feralygon\Kit\Component\Traits\PrototypeInitialization
 * @see \Feralygon\Kit\Component\Traits\Prototypes
 */
abstract class Component
{
	//Traits
	use KitTraits\LazyProperties;
	use Traits\Properties;
	use Traits\Initialization;
	use Traits\DefaultPrototype;
	use Traits\PrototypeInitialization;
	use Traits\Prototypes;
	
	
	
	//Private properties
	/** @var \Feralygon\Kit\Prototype */
	private $prototype;
	
	
	
	//Final public magic methods
	/**
	 * Instantiate class.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Prototype|string|null $prototype [default = null]
	 * <p>The prototype instance, class or name.<br>
	 * If not set, the default prototype instance or the base prototype class is used.</p>
	 * @param array $properties [default = []]
	 * <p>The properties, as <samp>name => value</samp> pairs.<br>
	 * They are applied to both the component and prototype.</p>
	 * @throws \Feralygon\Kit\Component\Exceptions\InvalidBasePrototypeClass
	 * @throws \Feralygon\Kit\Component\Exceptions\InvalidPrototype
	 * @throws \Feralygon\Kit\Component\Exceptions\PrototypeNameNotFound
	 * @throws \Feralygon\Kit\Component\Exceptions\InvalidPrototypeClass
	 * @throws \Feralygon\Kit\Component\Exceptions\PrototypePropertiesNotAllowed
	 */
	final public function __construct($prototype = null, array $properties = [])
	{
		//properties
		$prototype_properties = [];
		$this->initializeProperties(
			\Closure::fromCallable([$this, 'buildProperty']), $properties, $this->getRequiredPropertyNames(), 'rw',
			$prototype_properties
		);
		
		//prototype base class
		$prototype_base_class = $this->getBasePrototypeClass();
		if (!UType::isA($prototype_base_class, Prototype::class)) {
			throw new Exceptions\InvalidBasePrototypeClass([
				'component' => $this, 'base_class' => $prototype_base_class
			]);
		}
		
		//prototype
		if (!isset($prototype)) {
			$prototype = $this->buildDefaultPrototype($prototype_properties);
			if (isset($prototype)) {
				$prototype_properties = [];
			} else {
				$prototype = $prototype_base_class;
			}
		} elseif (!is_string($prototype) && !is_object($prototype)) {
			throw new Exceptions\InvalidPrototype(['component' => $this, 'prototype' => $prototype]);
		}
		
		//build prototype
		if (is_string($prototype)) {
			$instance = $this->buildPrototype($prototype, $prototype_properties);
			if (isset($instance)) {
				$prototype = $instance;
				$prototype_properties = [];
			} elseif (!class_exists($prototype)) {
				throw new Exceptions\PrototypeNameNotFound(['component' => $this, 'name' => $prototype]);
			}
		}
		
		//check prototype
		if (!UType::isA($prototype, $prototype_base_class)) {
			throw new Exceptions\InvalidPrototypeClass([
				'component' => $this, 'class' => UType::class($prototype), 'base_class' => $prototype_base_class
			]);
		}
		
		//prototype instantiation
		if (is_string($prototype)) {
			$prototype = new $prototype($prototype_properties);
		} elseif (!empty($prototype_properties)) {
			throw new Exceptions\PrototypePropertiesNotAllowed(['component' => $this]);
		}
		$this->initializePrototype($prototype);
		$this->prototype = $prototype;
		
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
	abstract public static function getBasePrototypeClass() : string;
	
	
	
	//Final public static methods
	/**
	 * Evaluate a given value as an instance.
	 * 
	 * Only a component instance or name, or a prototype instance, class or name, can be evaluated into an instance.
	 * 
	 * @since 1.0.0
	 * @param mixed $value [reference]
	 * <p>The value to evaluate (validate and sanitize).</p>
	 * @param array $properties [default = []]
	 * <p>The properties to use, as <samp>name => value</samp> pairs.</p>
	 * @param callable|null $builder [default = null]
	 * <p>The function to build an instance.<br>
	 * It is expected to be compatible with the following signature:<br><br>
	 * <code>function ($prototype, array $properties) : Feralygon\Kit\Component</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>Feralygon\Kit\Prototype|string $prototype</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The prototype instance, class or name to build with.<br>
	 * &nbsp; &#8226; &nbsp; <code><b>array $properties</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The properties to build with, as <samp>name => value</samp> pairs.<br>
	 * <br>
	 * Return: <code><b>Feralygon\Kit\Component</b></code><br>
	 * The built instance.</p>
	 * @param callable|null $named_builder [default = null]
	 * <p>The function to build an instance for a given name.<br>
	 * It is expected to be compatible with the following signature:<br><br>
	 * <code>function (string $name, array $properties) : ?Feralygon\Kit\Component
	 * </code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>string $name</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The name to build for.<br>
	 * &nbsp; &#8226; &nbsp; <code><b>array $properties</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The properties to build with, as <samp>name => value</samp> pairs.<br>
	 * <br>
	 * Return: <code><b>Feralygon\Kit\Component|null</b></code><br>
	 * The built instance for the given name or <code>null</code> if none was built.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value is successfully evaluated into an instance.</p>
	 */
	final public static function evaluate(
		&$value, array $properties = [], ?callable $builder = null, ?callable $named_builder = null
	) : bool
	{
		try {
			$value = static::coerce($value, $properties, $builder, $named_builder);
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
	 * <p>The properties to use, as <samp>name => value</samp> pairs.</p>
	 * @param callable|null $builder [default = null]
	 * <p>The function to build an instance.<br>
	 * It is expected to be compatible with the following signature:<br><br>
	 * <code>function ($prototype, array $properties) : Feralygon\Kit\Component</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>Feralygon\Kit\Prototype|string $prototype</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The prototype instance, class or name to build with.<br>
	 * &nbsp; &#8226; &nbsp; <code><b>array $properties</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The properties to build with, as <samp>name => value</samp> pairs.<br>
	 * <br>
	 * Return: <code><b>Feralygon\Kit\Component</b></code><br>
	 * The built instance.</p>
	 * @param callable|null $named_builder [default = null]
	 * <p>The function to build an instance for a given name.<br>
	 * It is expected to be compatible with the following signature:<br><br>
	 * <code>function (string $name, array $properties) : ?Feralygon\Kit\Component
	 * </code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>string $name</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The name to build for.<br>
	 * &nbsp; &#8226; &nbsp; <code><b>array $properties</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The properties to build with, as <samp>name => value</samp> pairs.<br>
	 * <br>
	 * Return: <code><b>Feralygon\Kit\Component|null</b></code><br>
	 * The built instance for the given name or <code>null</code> if none was built.</p>
	 * @throws \Feralygon\Kit\Component\Exceptions\CoercionFailed
	 * @return static
	 * <p>The given value coerced into an instance.</p>
	 */
	final public static function coerce(
		$value, array $properties = [], ?callable $builder = null, ?callable $named_builder = null
	) : Component
	{
		//check
		if (!isset($value)) {
			return new static(null, $properties);
		} elseif (is_object($value) && UType::isA($value, static::class)) {
			return $value;
		} elseif (!is_string($value) && (!is_object($value) || !UType::isA($value, Prototype::class))) {
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
			UCall::assert(
				'named_builder', $named_builder, function (string $name, array $properties) : ?Component {}, true
			);
			
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
						"Component name {{name}} has not been found.", ['name' => $value], null, [
							'string_options' => ['quote_strings' => true]
						]
					)
				]);
			}
		}
		
		//builder
		if (isset($builder)) {
			UCall::assert('builder', $builder, function ($prototype, array $properties) : Component {}, true);
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
	final protected function getPrototype() : Prototype
	{
		return $this->prototype;
	}
}
