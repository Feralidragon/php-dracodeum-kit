<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core;

use Feralygon\Kit\Core\Component\{
	Exceptions,
	Traits
};
use Feralygon\Kit\Core\Traits\ExtendedPropertiesArrayAccess as TExtendedPropertiesArrayAccess;
use Feralygon\Kit\Core\Utilities\{
	Call as UCall,
	Type as UType
};
use Feralygon\Kit\Core\Utilities\Type\Exceptions as UTypeExceptions;

/**
 * Core component class.
 * 
 * This class is the base to be extended from when creating a component.<br>
 * <br>
 * A component is an object which represents a specific functional part of an application and is expected to have 
 * several different implementations from the developer.<br>
 * Examples of this kind of object are inputs, outputs, tables, parameters, filters, constraints, models, controllers, 
 * handlers, entities, etc, all of which are expected to end up having tens or even hundreds of different 
 * implementations over time, each one representing a specific input, output, table, etc, respectively.<br>
 * <br>
 * Usually, this would be achieved by extending the base class, and implementing all the methods declared as abstract, 
 * or through one or more interfaces recognized by the base class, or even overriding existing methods.<br>
 * Each one of them has several issues however, such as having internal code of the base class mixed with the actual 
 * methods meant to implement a component, and it becomes unclear what each method is intended to do and how to extend 
 * and implement it, especially in relatively complex components, while overriding would certainly end up accidentally 
 * breaking the base code of the base class in some given way.<br>
 * <br>
 * Interfaces, the ideal way of defining methods for implementation, only allow public methods to be defined, 
 * and many of the methods of a component are actually meant to be implemented as protected, given that they should not 
 * become visible when the component is used.<br>
 * Furthermore, the overusage of interfaces leads to classes completely loosing their own identity, 
 * or never having one in the first place, potentially violating the Single Responsibility Principle.<br>
 * <br>
 * In summary, it makes code much harder to read and maintain, and it's very error prone as it doesn't properly 
 * and cleanly isolate internal code from what the developer actually intends and is supposed to define and customize, 
 * and becomes highly unflexible for extension.<br>
 * <br>
 * Therefore, the implementation of a component is not done by extending the component itself, nor through interfaces, 
 * a <b>prototype</b> is used instead.<br>
 * A prototype is an object which represents a specific implementation or set of definitions of a component.<br>
 * <br>
 * Any methods meant to implement a component are declared in a prototype instead, generally with the same class name 
 * as the component, but in a different namespace.<br>
 * This completely encapsulates the internal implementation from what is to be implemented by a developer 
 * when using it, and this way both the component and its prototype may share the same names for the same kind 
 * of methods without conflict, and all these methods may be public without any issues, allowing the usage 
 * of interfaces to segregate optional implementations from the prototype itself.<br>
 * <br>
 * This encapsulation is further reinforced by the fact that a prototype is never aware of which specific component 
 * is using it, as there is no back reference to it from the prototype.<br>
 * Also, a component may be extended or recreated to modify or refactor any internal behavior and still be able 
 * to reuse the existing prototypes in the same way, and the prototype methods may be called directly 
 * for testing purposes, allowing for more powerful debugging and unit testing.<br>
 * <br>
 * When using a component, the prototype itself is not accessible in any way whatsoever, other than from within 
 * the component itself, thus a prototype should not have any public methods meant to be called from anywhere else 
 * other than its component, except for testing purposes.<br>
 * Therefore, whenever a new public method is required to be called from a component, the component itself should be 
 * extended and have such a method implemented there instead, given that the public methods of a component are the only 
 * ones visible from any scope, which also means that for such cases it's not necessary to extend the prototypes.<br>
 * <br>
 * Both components and prototypes may also have a layer of custom properties, 
 * which may be given during instantiation.<br>
 * While all readable properties from a component may be accessed from any scope, in the case of a prototype they 
 * are effectively only visible to itself and the component using it.<br>
 * <br>
 * A prototype may also require to have existing functions bound to itself by a component, which must strictly follow 
 * the function templates defined by the prototype itself, 
 * and which may or may not correspond to actual methods from the component itself.<br>
 * <br>
 * A single prototype instance may also be used by multiple component instances at the same time, although it's not 
 * a normal use case, and it's limited to the prototype whether or not requiring functions to be bound to itself, 
 * however a single component can never have more than a single prototype.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Core\Prototype
 * @see \Feralygon\Kit\Core\Component\Traits\Properties
 * @see \Feralygon\Kit\Core\Component\Traits\Initialization
 * @see \Feralygon\Kit\Core\Component\Traits\PrototypeInitialization
 * @see \Feralygon\Kit\Core\Component\Traits\Prototypes
 */
abstract class Component implements \ArrayAccess
{
	//Traits
	use TExtendedPropertiesArrayAccess;
	use Traits\Properties;
	use Traits\Initialization;
	use Traits\PrototypeInitialization;
	use Traits\Prototypes;
	
	
	
	//Private properties
	/** @var \Feralygon\Kit\Core\Prototype */
	private $prototype;
	
	
	
	//Final public magic methods
	/**
	 * Instantiate class.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Core\Prototype|string|null $prototype [default = null] 
	 * <p>The prototype instance, class or name.<br>
	 * If not set, the base prototype class is used.</p>
	 * @param array $prototype_properties [default = []] <p>The prototype properties, 
	 * as <samp>name => value</samp> pairs.</p>
	 * @param array $properties [default = []] <p>The properties, as <samp>name => value</samp> pairs.</p>
	 * @throws \Feralygon\Kit\Core\Component\Exceptions\InvalidPrototypeBaseClass
	 * @throws \Feralygon\Kit\Core\Component\Exceptions\PrototypeNameNotFound
	 * @throws \Feralygon\Kit\Core\Component\Exceptions\InvalidPrototypeClass
	 * @throws \Feralygon\Kit\Core\Component\Exceptions\PrototypePropertiesNotAllowed
	 */
	final public function __construct($prototype = null, array $prototype_properties = [], array $properties = [])
	{
		//prototype base class
		$prototype_base_class = $this->getPrototypeBaseClass();
		if (!UType::isA($prototype_base_class, Prototype::class)) {
			throw new Exceptions\InvalidPrototypeBaseClass([
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
			$properties, \Closure::fromCallable([$this, 'buildProperty']), $this->getRequiredPropertyNames()
		);
		
		//initialize
		$this->initialize();
	}
	
	
	
	//Abstract public static methods
	/**
	 * Get prototype base class.
	 * 
	 * Any prototype class or instance given to be used by this component must be or 
	 * extend from the same class as the prototype base class returned here.
	 * 
	 * @since 1.0.0
	 * @return string <p>The prototype base class.</p>
	 */
	abstract public static function getPrototypeBaseClass() : string;
	
	
	
	//Final public static methods
	/**
	 * Evaluate a given value as an instance.
	 * 
	 * Only a component instance, prototype instance, class or name can be evaluated into an instance.
	 * 
	 * @since 1.0.0
	 * @param mixed $value [reference] <p>The value to evaluate (validate and sanitize).</p>
	 * @param array $prototype_properties [default = []] <p>The prototype properties to use, 
	 * as <samp>name => value</samp> pairs.</p>
	 * @param array $properties [default = []] <p>The properties to use, as <samp>name => value</samp> pairs.</p>
	 * @param callable|null $builder [default = null] <p>The function to build an instance.<br>
	 * The expected function signature is represented as:<br><br>
	 * <code>function ($prototype, array $prototype_properties, array $properties) : 
	 * \Feralygon\Kit\Core\Component</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>\Feralygon\Kit\Core\Prototype|string $prototype</b></code> : 
	 * The prototype instance, class or name to build with.<br>
	 * &nbsp; &#8226; &nbsp; <code><b>array $prototype_properties</b></code> : 
	 * The prototype properties to build with, as <samp>name => value</samp> pairs.<br>
	 * &nbsp; &#8226; &nbsp; <code><b>array $properties</b></code> : 
	 * The properties to build with, as <samp>name => value</samp> pairs.<br>
	 * <br>
	 * Return: <code><b>\Feralygon\Kit\Core\Component</b></code><br>
	 * The built instance.
	 * </p>
	 * @param bool $nullable [default = false] <p>Allow the given value to evaluate as <code>null</code>.</p>
	 * @return bool <p>Boolean <code>true</code> if the given value is successfully evaluated into an instance.</p>
	 */
	final public static function evaluate(
		&$value, array $prototype_properties = [], array $properties = [], ?callable $builder = null,
		bool $nullable = false
	) : bool
	{
		try {
			$value = static::coerce($value, $prototype_properties, $properties, $builder, $nullable);
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
	 * @param array $prototype_properties [default = []] <p>The prototype properties to use, 
	 * as <samp>name => value</samp> pairs.</p>
	 * @param array $properties [default = []] <p>The properties to use, as <samp>name => value</samp> pairs.</p>
	 * @param callable|null $builder [default = null] <p>The function to build an instance.<br>
	 * The expected function signature is represented as:<br><br>
	 * <code>function ($prototype, array $prototype_properties, array $properties) : 
	 * \Feralygon\Kit\Core\Component</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>\Feralygon\Kit\Core\Prototype|string $prototype</b></code> : 
	 * The prototype instance, class or name to build with.<br>
	 * &nbsp; &#8226; &nbsp; <code><b>array $prototype_properties</b></code> : 
	 * The prototype properties to build with, as <samp>name => value</samp> pairs.<br>
	 * &nbsp; &#8226; &nbsp; <code><b>array $properties</b></code> : 
	 * The properties to build with, as <samp>name => value</samp> pairs.<br>
	 * <br>
	 * Return: <code><b>\Feralygon\Kit\Core\Component</b></code><br>
	 * The built instance.
	 * </p>
	 * @param bool $nullable [default = false] <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @throws \Feralygon\Kit\Core\Component\Exceptions\CoercionFailed
	 * @return static|null <p>The given value coerced into an instance.<br>
	 * If nullable, <code>null</code> may also be returned.</p>
	 */
	final public static function coerce(
		$value, array $prototype_properties = [], array $properties = [], ?callable $builder = null,
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
			UCall::assertSignature(
				'builder',
				$builder,
				function ($prototype, array $prototype_properties, array $properties) : Component {},
				true
			);
			try {
				return UType::coerceObject($builder($value, $prototype_properties, $properties), static::class);
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
			return new static($value, $prototype_properties, $properties);
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
	 * @return \Feralygon\Kit\Core\Prototype <p>The prototype instance.</p>
	 */
	final protected function getPrototype() : Prototype
	{
		return $this->prototype;
	}
}
