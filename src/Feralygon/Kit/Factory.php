<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit;

use Feralygon\Kit\Factory\{
	Objects,
	Exceptions
};
use Feralygon\Kit\Utilities\Call as UCall;

/**
 * This class is the base to be extended from when creating a factory.
 * 
 * A factory is a class which is able to build specific types of objects using <b>builders</b>.<br>
 * A builder must implement an interface with a <code>build</code> method defined, which is used to build an object.<br>
 * <br>
 * Every type has a default builder set, but another one may be set during runtime (dependency injection).
 * 
 * @since 1.0.0
 * @see https://en.wikipedia.org/wiki/Factory_method_pattern
 * @see \Feralygon\Kit\Factory\Builder
 * @see \Feralygon\Kit\Factory\Objects\Type
 */
abstract class Factory
{
	//Traits
	use Traits\NonInstantiable;
	
	
	
	//Private static properties
	/** @var \Feralygon\Kit\Factory\Objects\Type[] */
	private static $types = [];
	
	/** @var string|null */
	private static $current_type_name = null;
	
	
	
	//Abstract protected static methods
	/**
	 * Build type instance for a given name.
	 * 
	 * @since 1.0.0
	 * @param string $name
	 * <p>The name to build for.</p>
	 * @return \Feralygon\Kit\Factory\Objects\Type|null
	 * <p>The built type instance for the given name or <code>null</code> if none was built.</p>
	 */
	abstract protected static function buildType(string $name) : ?Objects\Type;
	
	
	
	//Final public static methods
	/**
	 * Set builder for a given type.
	 * 
	 * @since 1.0.0
	 * @param string $type
	 * <p>The type to set for.</p>
	 * @param \Feralygon\Kit\Factory\Builder|string $builder
	 * <p>The builder instance or class to set.<br>
	 * It must implement the builder interface set for the given type.</p>
	 * @return void
	 */
	final public static function setBuilder(string $type, $builder) : void
	{
		static::getType($type)->setBuilder($builder);
	}
	
	
	
	//Final protected static methods
	/**
	 * Get type instance for a given name.
	 * 
	 * @since 1.0.0
	 * @param string $name
	 * <p>The name to get for.</p>
	 * @throws \Feralygon\Kit\Factory\Exceptions\TypeNotFound
	 * @return \Feralygon\Kit\Factory\Objects\Type
	 * <p>The type instance for the given name.</p>
	 */
	final protected static function getType(string $name) : Objects\Type
	{
		if (!isset(self::$types[static::class][$name])) {
			//build
			$type = null;
			try {
				self::$current_type_name = $name;
				$type = static::buildType($name);
			} finally {
				self::$current_type_name = null;
			}
			
			//check
			if (!isset($type)) {
				throw new Exceptions\TypeNotFound(['factory' => static::class, 'name' => $name]);
			}
			UCall::guardInternal($type->getName() === $name, [
				'error_message' => "Type name {{type.getName()}} mismatches the expected name {{name}}.",
				'parameters' => ['type' => $type, 'name' => $name]
			]);
			
			//set
			self::$types[static::class][$name] = $type;
		}
		return self::$types[static::class][$name];
	}
	
	/**
	 * Create a new type instance with a given builder interface and instance or class.
	 * 
	 * This method may only be called from within the <code>buildType</code> method.
	 * 
	 * @since 1.0.0
	 * @param string $builder_interface
	 * <p>The builder interface to create with.<br>
	 * It must define a <code>build</code> method, which must return an object or <code>null</code>.</p>
	 * @param \Feralygon\Kit\Factory\Builder|string $builder
	 * <p>The builder instance or class to create with.</p>
	 * @return \Feralygon\Kit\Factory\Objects\Type
	 * <p>The created type instance with the given builder interface and instance or class.</p>
	 */
	final protected static function createType(string $builder_interface, $builder) : Objects\Type
	{
		UCall::guard(isset(self::$current_type_name), [
			'hint_message' => "This method may only be called from within the \"buildType\" method."
		]);
		return new Objects\Type(self::$current_type_name, $builder_interface, $builder);
	}
	
	/**
	 * Build object for a given type.
	 * 
	 * @since 1.0.0
	 * @param string $type
	 * <p>The type to build for.</p>
	 * @param mixed ...$arguments
	 * <p>The arguments to build with.</p>
	 * @return object
	 * <p>The built object for the given type.</p>
	 */
	final protected static function build(string $type, ...$arguments) : object
	{
		//build
		$type = static::getType($type);
		$object = $type->getBuilder()->build(...$arguments);
		
		//guard
		UCall::guardInternal(isset($object), [
			'error_message' => "No object has been built for type {{type.getName()}} " . 
				"from builder {{type.getBuilder()}}.",
			'parameters' => ['type' => $type]
		]);
		UCall::guardInternal(is_object($object), [
			'error_message' => "An invalid object {{object}} has been built for type {{type.getName()}} " . 
				"from builder {{type.getBuilder()}}.",
			'hint_message' => "Only an object is allowed to be built.",
			'parameters' => ['type' => $type, 'object' => $object]
		]);
		
		//return
		return $object;
	}
}
