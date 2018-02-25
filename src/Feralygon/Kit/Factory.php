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
use Feralygon\Kit\Factory\Builder\Interfaces as BuilderInterfaces;
use Feralygon\Kit\Utilities\{
	Call as UCall,
	Type as UType
};

/**
 * Factory class.
 * 
 * This class is the base to be extended from when creating a factory.<br>
 * <br>
 * A factory is a class which is able to build specific types of objects using <b>builders</b>.<br>
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
	 * @param string $name <p>The name to build for.</p>
	 * @return \Feralygon\Kit\Factory\Objects\Type|null <p>The built type instance for the given name or 
	 * <code>null</code> if none was built.</p>
	 */
	abstract protected static function buildType(string $name) : ?Objects\Type;
	
	
	
	//Final public static methods
	/**
	 * Set builder for a given type.
	 * 
	 * @since 1.0.0
	 * @param string $type <p>The type to set for.</p>
	 * @param \Feralygon\Kit\Factory\Builder|string $builder <p>The builder instance or class to set.</p>
	 * @return void
	 */
	final public static function setBuilder(string $type, $builder) : void
	{
		static::getType($type)->setBuilder($builder);
	}
	
	
	
	//Final protected static methods
	/**
	 * Get type instance with a given name.
	 * 
	 * @since 1.0.0
	 * @param string $name <p>The name to get with.</p>
	 * @throws \Feralygon\Kit\Factory\Exceptions\TypeNotFound
	 * @throws \Feralygon\Kit\Factory\Exceptions\TypeNameMismatch
	 * @return \Feralygon\Kit\Factory\Objects\Type <p>The type instance with the given name.</p>
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
			} elseif ($type->getName() !== $name) {
				throw new Exceptions\TypeNameMismatch(['factory' => static::class, 'name' => $name, 'type' => $type]);
			}
			
			//set
			self::$types[static::class][$name] = $type;
		}
		return self::$types[static::class][$name];
	}
	
	/**
	 * Create a new type instance with a given builder.
	 * 
	 * This method may only be called from within the <code>buildType</code> method.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Factory\Builder|string $builder <p>The builder instance or class to create with.</p>
	 * @param string|null $class [default = null] <p>The class to create with.<br>
	 * Any object built by the created type instance must be or extend from the same class as the one given here.<br>
	 * If no class is set, then any object built is assumed to be valid.</p>
	 * @return \Feralygon\Kit\Factory\Objects\Type <p>The created type instance with the given builder.</p>
	 */
	final protected static function createType($builder, ?string $class = null) : Objects\Type
	{
		UCall::guard(isset(self::$current_type_name), [
			'hint_message' => "This method may only be called from within the \"buildType\" method."
		]);
		return new Objects\Type(self::$current_type_name, $builder, $class);
	}
	
	/**
	 * Build object from a given type.
	 * 
	 * This method requires the builder of the given type to have 
	 * the <code>Feralygon\Kit\Factory\Builder\Interfaces\Build</code> interface implemented.
	 * 
	 * @since 1.0.0
	 * @param string $type <p>The type to build from.</p>
	 * @param mixed ...$arguments <p>The arguments to build with.</p>
	 * @throws \Feralygon\Kit\Factory\Exceptions\NoObjectBuilt
	 * @throws \Feralygon\Kit\Factory\Exceptions\InvalidObjectBuilt
	 * @return object <p>The built object from the given type.</p>
	 */
	final protected static function build(string $type, ...$arguments) : object
	{
		//initialize
		$type = static::getType($type);
		$builder = $type->getBuilder();
		
		//guard
		UCall::guard($builder instanceof BuilderInterfaces\Build, [
			'hint_message' => "This method requires the builder of the given type {{type}} to have " . 
				"the \"Feralygon\\Kit\\Factory\\Builder\\Interfaces\\Build\" interface implemented.",
			'parameters' => ['type' => $type->getName()],
			'object_class' => static::class
		]);
		
		//build
		$object = $builder->build(...$arguments);
		if (!isset($object)) {
			throw new Exceptions\NoObjectBuilt(['factory' => static::class, 'type' => $type]);
		} elseif ($type->hasClass() && !UType::isA($object, $type->getClass())) {
			throw new Exceptions\InvalidObjectBuilt(['factory' => static::class, 'type' => $type, 'object' => $object]);
		}
		return $object;
	}
	
	/**
	 * Build object from a given type by using a given name.
	 * 
	 * This method requires the builder of the given type to have 
	 * the <code>Feralygon\Kit\Factory\Builder\Interfaces\NamedBuild</code> interface implemented.
	 * 
	 * @since 1.0.0
	 * @param string $type <p>The type to build from.</p>
	 * @param string $name <p>The name to use.</p>
	 * @param mixed ...$arguments <p>The arguments to build with.</p>
	 * @throws \Feralygon\Kit\Factory\Exceptions\NoObjectBuilt
	 * @throws \Feralygon\Kit\Factory\Exceptions\InvalidObjectBuilt
	 * @return object <p>The built object from the given type by using the given name.</p>
	 */
	final protected static function buildByName(string $type, string $name, ...$arguments) : object
	{
		//initialize
		$type = static::getType($type);
		$builder = $type->getBuilder();
		
		//guard
		UCall::guard($builder instanceof BuilderInterfaces\NamedBuild, [
			'hint_message' => "This method requires the builder of the given type {{type}} to have " . 
				"the \"Feralygon\\Kit\\Factory\\Builder\\Interfaces\\NamedBuild\" interface implemented.",
			'parameters' => ['type' => $type->getName()],
			'object_class' => static::class
		]);
		
		//build
		$object = $builder->buildByName($name, ...$arguments);
		if (!isset($object)) {
			throw new Exceptions\NoObjectBuilt(['factory' => static::class, 'type' => $type, 'name' => $name]);
		} elseif ($type->hasClass() && !UType::isA($object, $type->getClass())) {
			throw new Exceptions\InvalidObjectBuilt([
				'factory' => static::class, 'type' => $type, 'object' => $object, 'name' => $name
			]);
		}
		return $object;
	}
}
