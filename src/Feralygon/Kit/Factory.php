<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit;

use Feralygon\Kit\Factory\Builder;
use Feralygon\Kit\Utilities\Type as UType;

/**
 * Factory class.
 * 
 * This class is the base to be extended from when creating a factory.<br>
 * <br>
 * A factory is a class which is able to build specific types of objects for any given names.<br>
 * Each object is built using a <b>factory builder</b> object, which is able to build a new object based on its name 
 * and instantiation arguments.<br>
 * Every object type must have a default factory builder, but another one may be set (dependency injection pattern).
 * 
 * @since 1.0.0
 * @see https://en.wikipedia.org/wiki/Factory_method_pattern
 * @see \Feralygon\Kit\Factory\Builder
 */
abstract class Factory
{
	//Traits
	use Traits\NonInstantiable;
	
	
	
	//Private static properties
	/** @var \Feralygon\Kit\Factory\Builder[] */
	private static $builders = [];
	
	
	
	//Abstract protected static methods
	/**
	 * Build builder instance for a given type.
	 * 
	 * @since 1.0.0
	 * @param string $type <p>The type to build for.</p>
	 * @return \Feralygon\Kit\Factory\Builder|null <p>The built builder instance for the given type or 
	 * <code>null</code> if none was built.</p>
	 */
	abstract protected static function buildBuilder(string $type) : ?Builder;
	
	
	
	//Final public static methods
	/**
	 * Build an object for a given type and name.
	 * 
	 * @since 1.0.0
	 * @param string $type <p>The type to build for.</p>
	 * @param string $name <p>The name to build for.</p>
	 * @param mixed $arguments [variadic] <p>The arguments to build with.</p>
	 * @return object <p>The built object.</p>
	 */
	final public static function build(string $type, string $name, ...$arguments)
	{
		//builder
		if (!isset(self::$builders[static::class][$type])) {
			$builder = static::buildBuilder($type);
			if (!isset($builder)) {
				
				//TODO: throw exception
				
			}
			self::$builders[static::class][$type] = $builder;
		}
		
		//build
		$object = self::$builders[static::class][$type]->build($name, ...$arguments);
		if (!isset($object)) {
			
			//TODO: throw exception
			
		} elseif (!is_object($object)) {
			
			//TODO: throw exception
			
		}
		return $object;
	}
	
	/**
	 * Set builder for a given type.
	 * 
	 * @since 1.0.0
	 * @param string $type <p>The type to set for.</p>
	 * @param \Feralygon\Kit\Factory\Builder|string $builder <p>The builder instance or class to set.</p>
	 * @return void
	 */
	final protected static function setBuilder(string $type, $builder) : void
	{
		self::$builders[static::class][$type] = UType::coerceObject($builder, Builder::class);
	}
}
