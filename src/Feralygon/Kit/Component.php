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
	Type as UType
};
use Feralygon\Kit\Utilities\Type\Exceptions as UTypeExceptions;

/**
 * Component class.
 * 
 * This class is the base to be extended from when creating a component.<br>
 * <br>
 * A component is an object which represents a specific functional part of an application and is expected to have 
 * a very high number of implementations, each one tailored to each specific purpose, but with its core behavior, 
 * functionality and interface mostly intact.<br>
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
 * @see \Feralygon\Kit\Component\Traits\PrototypeInitialization
 * @see \Feralygon\Kit\Component\Traits\Prototypes
 */
abstract class Component
{
	//Traits
	use KitTraits\LazyProperties;
	use Traits\Properties;
	use Traits\Initialization;
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
	 * If not set, the base prototype class is used.</p>
	 * @param array $properties [default = []] <p>The properties, as <samp>name => value</samp> pairs.</p>
	 * @param array $prototype_properties [default = []] <p>The prototype properties, 
	 * as <samp>name => value</samp> pairs.</p>
	 * @throws \Feralygon\Kit\Component\Exceptions\InvalidBasePrototypeClass
	 * @throws \Feralygon\Kit\Component\Exceptions\PrototypeNameNotFound
	 * @throws \Feralygon\Kit\Component\Exceptions\InvalidPrototypeClass
	 * @throws \Feralygon\Kit\Component\Exceptions\PrototypePropertiesNotAllowed
	 */
	final public function __construct($prototype = null, array $properties = [], array $prototype_properties = [])
	{
		//prototype base class
		$prototype_base_class = $this->getBasePrototypeClass();
		if (!UType::isA($prototype_base_class, Prototype::class)) {
			throw new Exceptions\InvalidBasePrototypeClass([
				'component' => $this,
				'base_class' => $prototype_base_class
			]);
		}
		
		//prototype validation
		if (!isset($prototype)) {
			$prototype = $prototype_base_class;
		} else {
			//build
			if (is_string($prototype)) {
				$instance = $this->buildPrototype($prototype, $prototype_properties);
				if (isset($instance)) {
					$prototype = $instance;
					$prototype_properties = [];
				} elseif (!class_exists($prototype)) {
					throw new Exceptions\PrototypeNameNotFound(['component' => $this, 'name' => $prototype]);
				}
			}
			
			//check
			if (!UType::isA($prototype, $prototype_base_class)) {
				throw new Exceptions\InvalidPrototypeClass([
					'component' => $this,
					'class' => UType::class($prototype),
					'base_class' => $prototype_base_class
				]);
			}
		}
		
		//prototype instantiation
		if (is_string($prototype)) {
			$prototype = new $prototype($prototype_properties);
		} elseif (!empty($prototype_properties)) {
			throw new Exceptions\PrototypePropertiesNotAllowed(['component' => $this]);
		}
		$this->initializePrototype($prototype);
		$this->prototype = $prototype;
		
		//properties
		$this->initializeProperties(
			\Closure::fromCallable([$this, 'buildProperty']), $properties, $this->getRequiredPropertyNames()
		);
		
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
	 * @return string <p>The base prototype class.</p>
	 */
	abstract public static function getBasePrototypeClass() : string;
	
	
	
	//Final public static methods
	/**
	 * Evaluate a given value as an instance.
	 * 
	 * Only a component instance, prototype instance, class or name can be evaluated into an instance.
	 * 
	 * @since 1.0.0
	 * @param mixed $value [reference] <p>The value to evaluate (validate and sanitize).</p>
	 * @param array $properties [default = []] <p>The properties to use, as <samp>name => value</samp> pairs.</p>
	 * @param array $prototype_properties [default = []] <p>The prototype properties to use, 
	 * as <samp>name => value</samp> pairs.</p>
	 * @param callable|null $builder [default = null] <p>The function to build an instance.<br>
	 * It is expected to be compatible with the following signature:<br><br>
	 * <code>function ($prototype, array $properties, array $prototype_properties) : \Feralygon\Kit\Component</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>\Feralygon\Kit\Prototype|string $prototype</b></code> : 
	 * The prototype instance, class or name to build with.<br>
	 * &nbsp; &#8226; &nbsp; <code><b>array $properties</b></code> : 
	 * The properties to build with, as <samp>name => value</samp> pairs.<br>
	 * &nbsp; &#8226; &nbsp; <code><b>array $prototype_properties</b></code> : 
	 * The prototype properties to build with, as <samp>name => value</samp> pairs.<br>
	 * <br>
	 * Return: <code><b>\Feralygon\Kit\Component</b></code><br>
	 * The built instance.
	 * </p>
	 * @param bool $nullable [default = false] <p>Allow the given value to evaluate as <code>null</code>.</p>
	 * @return bool <p>Boolean <code>true</code> if the given value is successfully evaluated into an instance.</p>
	 */
	final public static function evaluate(
		&$value, array $properties = [], array $prototype_properties = [], ?callable $builder = null,
		bool $nullable = false
	) : bool
	{
		try {
			$value = static::coerce($value, $properties, $prototype_properties, $builder, $nullable);
		} catch (Exceptions\CoercionFailed $exception) {
			return false;
		}
		return true;
	}
	
	/**
	 * Coerce a given value into an instance.
	 * 
	 * Only a component instance, prototype instance, class or name can be coerced into an instance.
	 * 
	 * @since 1.0.0
	 * @param mixed $value <p>The value to coerce (validate and sanitize).</p>
	 * @param array $properties [default = []] <p>The properties to use, as <samp>name => value</samp> pairs.</p>
	 * @param array $prototype_properties [default = []] <p>The prototype properties to use, 
	 * as <samp>name => value</samp> pairs.</p>
	 * @param callable|null $builder [default = null] <p>The function to build an instance.<br>
	 * It is expected to be compatible with the following signature:<br><br>
	 * <code>function ($prototype, array $properties, array $prototype_properties) : \Feralygon\Kit\Component</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>\Feralygon\Kit\Prototype|string $prototype</b></code> : 
	 * The prototype instance, class or name to build with.<br>
	 * &nbsp; &#8226; &nbsp; <code><b>array $properties</b></code> : 
	 * The properties to build with, as <samp>name => value</samp> pairs.<br>
	 * &nbsp; &#8226; &nbsp; <code><b>array $prototype_properties</b></code> : 
	 * The prototype properties to build with, as <samp>name => value</samp> pairs.<br>
	 * <br>
	 * Return: <code><b>\Feralygon\Kit\Component</b></code><br>
	 * The built instance.
	 * </p>
	 * @param bool $nullable [default = false] <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @throws \Feralygon\Kit\Component\Exceptions\CoercionFailed
	 * @return static|null <p>The given value coerced into an instance.<br>
	 * If nullable, <code>null</code> may also be returned.</p>
	 */
	final public static function coerce(
		$value, array $properties = [], array $prototype_properties = [], ?callable $builder = null,
		bool $nullable = false
	) : ?Component
	{
		//initialize
		if (!isset($value)) {
			if ($nullable) {
				return null;
			}
			throw new Exceptions\CoercionFailed([
				'value' => $value,
				'component' => static::class,
				'error_code' => Exceptions\CoercionFailed::ERROR_CODE_NULL,
				'error_message' => "A null value is not allowed."
			]);
		} elseif (is_object($value) && UType::isA($value, static::class)) {
			return $value;
		} elseif (!is_string($value) && (!is_object($value) || !UType::isA($value, Prototype::class))) {
			throw new Exceptions\CoercionFailed([
				'value' => $value,
				'component' => static::class,
				'error_code' => Exceptions\CoercionFailed::ERROR_CODE_INVALID_TYPE,
				'error_message' => "Only a component instance, prototype instance, class or name " . 
					"can be coerced into an instance."
			]);
		} elseif (is_object($value)) {
			$prototype_properties = [];
		}
		
		//builder
		if (isset($builder)) {
			//assert
			UCall::assert(
				'builder', $builder,
				function ($prototype, array $properties, array $prototype_properties) : Component {},
				true
			);
			
			//coerce
			try {
				return UType::coerceObject($builder($value, $properties, $prototype_properties), static::class);
			} catch (UTypeExceptions\ObjectCoercionFailed $exception) {
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
			return new static($value, $properties, $prototype_properties);
		} catch (\Exception $exception) {
			throw new Exceptions\CoercionFailed([
				'value' => $value,
				'component' => static::class,
				'error_code' => Exceptions\CoercionFailed::ERROR_CODE_BUILD_EXCEPTION,
				'error_message' => $exception->getMessage()
			]);
		}
		throw new Exceptions\CoercionFailed([
			'value' => $value,
			'component' => static::class
		]);
	}
	
	
	
	//Final protected methods
	/**
	 * Get prototype instance.
	 * 
	 * @since 1.0.0
	 * @return \Feralygon\Kit\Prototype <p>The prototype instance.</p>
	 */
	final protected function getPrototype() : Prototype
	{
		return $this->prototype;
	}
}
