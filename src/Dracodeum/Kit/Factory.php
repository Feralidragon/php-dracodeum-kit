<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit;

use Dracodeum\Kit\Interfaces\Uninstantiable as IUninstantiable;
use Dracodeum\Kit\Factory\{
	Type,
	Exceptions
};
use Dracodeum\Kit\Utilities\Call as UCall;

/**
 * This class is the base to be extended from when creating a factory.
 * 
 * A factory is a class which is able to build specific types of objects using <b>builders</b>.<br>
 * A builder must implement an interface with a <code>build</code> method defined, which is used to build an object.<br>
 * <br>
 * Every type has a default builder set, but another one may be set during runtime (dependency injection).
 * 
 * @see https://en.wikipedia.org/wiki/Factory_method_pattern
 * @see \Dracodeum\Kit\Factory\Builder
 * @see \Dracodeum\Kit\Factory\Type
 */
abstract class Factory implements IUninstantiable
{
	//Traits
	use Traits\Uninstantiable;
	
	
	
	//Private static properties
	/** @var \Dracodeum\Kit\Factory\Type[] */
	private static $types = [];
	
	/** @var string|null */
	private static $current_type_name = null;
	
	
	
	//Abstract protected static methods
	/**
	 * Build type instance with a given name.
	 * 
	 * @param string $name
	 * <p>The name to build with.</p>
	 * @return \Dracodeum\Kit\Factory\Type|null
	 * <p>The built type instance with the given name or <code>null</code> if none was built.</p>
	 */
	abstract protected static function buildType(string $name): ?Type;
	
	
	
	//Final public static methods
	/**
	 * Set builder for a given type.
	 * 
	 * @param string $type
	 * <p>The type to set for.</p>
	 * @param \Dracodeum\Kit\Factory\Builder|string $builder
	 * <p>The builder instance or class to set.<br>
	 * It must implement the builder interface set for the given type.</p>
	 */
	final public static function setBuilder(string $type, $builder): void
	{
		static::getType($type)->setBuilder($builder);
	}
	
	
	
	//Final protected static methods
	/**
	 * Get type instance with a given name.
	 * 
	 * @param string $name
	 * <p>The name to get with.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Factory\Exceptions\TypeNotFound
	 * @return \Dracodeum\Kit\Factory\Type|null
	 * <p>The type instance with the given name.<br>
	 * If <var>$no_throw</var> is set to boolean <code>true</code>, 
	 * then <code>null</code> is returned if it was not found.</p>
	 */
	final protected static function getType(string $name, bool $no_throw = false): ?Type
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
				if ($no_throw) {
					return null;
				}
				throw new Exceptions\TypeNotFound([static::class, $name]);
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
	 * @param string $builder_interface
	 * <p>The builder interface to create with.<br>
	 * It must define a <code>build</code> method, which must return an object or <code>null</code>.</p>
	 * @param \Dracodeum\Kit\Factory\Builder|string $builder
	 * <p>The builder instance or class to create with.</p>
	 * @return \Dracodeum\Kit\Factory\Type
	 * <p>The created type instance with the given builder interface and instance or class.</p>
	 */
	final protected static function createType(string $builder_interface, $builder): Type
	{
		UCall::guard(isset(self::$current_type_name), [
			'hint_message' => "This method may only be called from within the \"buildType\" method."
		]);
		return new Type(self::$current_type_name, $builder_interface, $builder);
	}
	
	/**
	 * Build object for a given type.
	 * 
	 * @param string $type
	 * <p>The type to build for.</p>
	 * @param mixed ...$arguments
	 * <p>The arguments to build with.</p>
	 * @return object
	 * <p>The built object for the given type.</p>
	 */
	final protected static function build(string $type, ...$arguments): object
	{
		//build
		$type = static::getType($type);
		$object = $type->getBuilder()->build(...$arguments);
		
		//guard
		UCall::guardInternal(isset($object), [
			'error_message' => "No object built for type {{type.getName()}} from builder {{type.getBuilder()}}.",
			'parameters' => ['type' => $type]
		]);
		UCall::guardInternal(is_object($object), [
			'error_message' => "Invalid object {{object}} built for type {{type.getName()}} " . 
				"from builder {{type.getBuilder()}}.",
			'hint_message' => "Only an object is allowed to be built.",
			'parameters' => ['type' => $type, 'object' => $object]
		]);
		
		//return
		return $object;
	}
}
