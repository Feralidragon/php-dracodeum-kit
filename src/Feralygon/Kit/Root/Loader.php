<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Root;

use Feralygon\Kit\Root\Loader\Objects;
use Feralygon\Kit\Core\Utilities\Type as UType;

/**
 * Root loader class.
 * 
 * This class is used to statically set packages and autoload their classes.
 * 
 * @since 1.0.0
 */
final class Loader
{
	//Private static properties
	/** @var bool */
	private static $initialized = false;
	
	/** @var \Feralygon\Kit\Root\Loader\Objects\Package[] */
	private static $packages = [];
	
	
	
	//Final public magic methods
	/**
	 * Prevent class from being instantiated.
	 * 
	 * @since 1.0.0
	 * @throws \RuntimeException
	 */
	final public function __construct()
	{
		throw new \RuntimeException("The loader class cannot be instantiated.");
	}
	
	
	
	//Final public static methods
	/**
	 * Check if has package with a given vendor and name.
	 * 
	 * Both the given vendor and name are checked in a case-insensitive manner.
	 * 
	 * @since 1.0.0
	 * @param string $vendor <p>The package vendor to check for.</p>
	 * @param string $name <p>The package name to check for.</p>
	 * @return bool <p>Boolean <code>true</code> if has the package with the given vendor and name.</p>
	 */
	final public static function hasPackage(string $vendor, string $name) : bool
	{
		return isset(self::$packages[strtolower($vendor)][strtolower($name)]);
	}
	
	/**
	 * Get package instance with a given vendor and name.
	 * 
	 * Both the given vendor and name are used in a case-insensitive manner.
	 * 
	 * @since 1.0.0
	 * @param string $vendor <p>The package vendor to get with.</p>
	 * @param string $name <p>The package name to get with.</p>
	 * @throws \RuntimeException
	 * @return \Feralygon\Kit\Root\Loader\Objects\Package <p>The package instance with the given vendor and name.</p>
	 */
	final public static function getPackage(string $vendor, string $name) : Objects\Package
	{
		$vendor = strtolower($vendor);
		$name = strtolower($name);
		if (!isset(self::$packages[$vendor][$name])) {
			throw new \RuntimeException("The package \"{$vendor}/{$name}\" was not found.");
		}
		return self::$packages[$vendor][$name];
	}
	
	/**
	 * Set package with a given vendor, name and path.
	 * 
	 * Both the given vendor and name are set in a case-insensitive manner and converted to lowercase.
	 * 
	 * @since 1.0.0
	 * @param string $vendor <p>The package vendor to set with.</p>
	 * @param string $name <p>The package name to set with.</p>
	 * @param string $path <p>The package path to set with.</p>
	 * @throws \RuntimeException
	 * @return \Feralygon\Kit\Root\Loader\Objects\Package <p>The package instance set with the given vendor, 
	 * name and path.</p>
	 */
	final public static function setPackage(string $vendor, string $name, string $path) : Objects\Package
	{
		//initialize
		if (!self::$initialized) {
			spl_autoload_register(function (string $class) : void {
				if (preg_match('/^(\w+)\\\\(\w+)\\\\/', $class, $matches)) {
					if (self::hasPackage($matches[1], $matches[2])) {
						@include_once self::getPackage($matches[1], $matches[2])->getPath() . 
							'/' . str_replace('\\', '/', $class) . '.php';
					}
				}
			});
			self::$initialized = true;
		}
		
		//set
		$vendor = strtolower($vendor);
		$name = strtolower($name);
		if (isset(self::$packages[$vendor][$name])) {
			throw new \RuntimeException("The package \"{$vendor}/{$name}\" has already been set.");
		}
		self::$packages[$vendor][$name] = new Objects\Package($vendor, $name, $path);
		
		//return
		return self::$packages[$vendor][$name];
	}
	
	/**
	 * Get package instance from a given object or class.
	 * 
	 * @since 1.0.0
	 * @param object|string $object_class <p>The object or class to get from.</p>
	 * @return \Feralygon\Kit\Root\Loader\Objects\Package|null <p>The package instance from the given object or class 
	 * or <code>null</code> if none is set.</p>
	 */
	final public static function getClassPackage($object_class) : ?Objects\Package
	{
		if (
			preg_match('/^(\w+)\\\\(\w+)\\\\/', UType::class($object_class), $matches) && 
			self::hasPackage($matches[1], $matches[2])
		) {
			return self::getPackage($matches[1], $matches[2]);
		}
		return null;
	}
}
