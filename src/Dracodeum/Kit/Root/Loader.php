<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Root;

use Dracodeum\Kit\Root\Loader\Package;
use Dracodeum\Kit\Utilities\Type as UType;

/** This class is used to statically set packages and autoload their classes. */
final class Loader
{
	//Private static properties
	/** @var bool */
	private static $initialized = false;
	
	/** @var \Dracodeum\Kit\Root\Loader\Package[] */
	private static $packages = [];
	
	
	
	//Final public magic methods
	/**
	 * Prevent class from being instantiated.
	 * 
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
	 * @param string $vendor
	 * <p>The vendor to check with.<br>
	 * It is checked in a case-insensitive manner.</p>
	 * @param string $name
	 * <p>The name to check with.<br>
	 * It is checked in a case-insensitive manner.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if has the package with the given vendor and name.</p>
	 */
	final public static function hasPackage(string $vendor, string $name): bool
	{
		return isset(self::$packages[strtolower($vendor)][strtolower($name)]);
	}
	
	/**
	 * Get package instance with a given vendor and name.
	 * 
	 * @param string $vendor
	 * <p>The vendor to get with.<br>
	 * It is used in a case-insensitive manner.</p>
	 * @param string $name
	 * <p>The name to get with.<br>
	 * It is used in a case-insensitive manner.</p>
	 * @throws \RuntimeException
	 * @return \Dracodeum\Kit\Root\Loader\Package
	 * <p>The package instance with the given vendor and name.</p>
	 */
	final public static function getPackage(string $vendor, string $name): Package
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
	 * @param string $vendor
	 * <p>The vendor to set with.<br>
	 * It is set in a case-insensitive manner and converted to lowercase.</p>
	 * @param string $name
	 * <p>The name to set with.<br>
	 * It is set in a case-insensitive manner and converted to lowercase.</p>
	 * @param string $path
	 * <p>The path to set with.</p>
	 * @throws \RuntimeException
	 * @return \Dracodeum\Kit\Root\Loader\Package
	 * <p>The package instance set with the given vendor, name and path.</p>
	 */
	final public static function setPackage(string $vendor, string $name, string $path): Package
	{
		//initialize
		if (!self::$initialized) {
			spl_autoload_register(function (string $class): void {
				if (preg_match('/^(?P<vendor>\w+)\\\\(?P<name>\w+)\\\\/', $class, $matches)) {
					if (self::hasPackage($matches['vendor'], $matches['name'])) {
						$path = self::getPackage($matches['vendor'], $matches['name'])->getPath($class);
						if (stream_resolve_include_path($path) !== false) {
							require_once $path;
						}
					}
				}
			}, true);
			self::$initialized = true;
		}
		
		//set
		$vendor = strtolower($vendor);
		$name = strtolower($name);
		if (isset(self::$packages[$vendor][$name])) {
			throw new \RuntimeException("The package \"{$vendor}/{$name}\" has already been set.");
		}
		self::$packages[$vendor][$name] = new Package($vendor, $name, $path);
		
		//return
		return self::$packages[$vendor][$name];
	}
	
	/**
	 * Get package instance from a given object or class.
	 * 
	 * @param object|string $object_class
	 * <p>The object or class to get from.</p>
	 * @return \Dracodeum\Kit\Root\Loader\Package|null
	 * <p>The package instance from the given object or class or <code>null</code> if none is set.</p>
	 */
	final public static function getClassPackage($object_class): ?Package
	{
		if (
			preg_match('/^(?P<vendor>\w+)\\\\(?P<name>\w+)\\\\/', UType::class($object_class), $matches) && 
			self::hasPackage($matches['vendor'], $matches['name'])
		) {
			return self::getPackage($matches['vendor'], $matches['name']);
		}
		return null;
	}
}
