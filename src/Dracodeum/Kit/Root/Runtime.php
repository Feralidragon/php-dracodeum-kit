<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Root;

use Dracodeum\Kit\Interfaces\Uninstantiable as IUninstantiable;
use Dracodeum\Kit\Traits;
use Dracodeum\Kit\Utilities\Call as UCall;

/**
 * This class represents the current application runtime and is used to generate a UUID which uniquely identifies 
 * a single runtime instance of the application, as well statically set and get its origin as the originally used 
 * entry point to execute the application.
 */
final class Runtime implements IUninstantiable
{
	//Traits
	use Traits\Uninstantiable;
	
	
	
	//Private static properties
	/** @var string|null */
	private static $uuid = null;
	
	/** @var string|null */
	private static $origin = null;
	
	
	
	//Final public static methods
	/**
	 * Get UUID (Universally Unique Identifier).
	 * 
	 * The returning UUID is a randomly generated string which uniquely identifies a single runtime instance of the 
	 * application.
	 * 
	 * @return string
	 * <p>The UUID (Universally Unique Identifier).</p>
	 */
	final public static function getUuid(): string
	{
		return self::$uuid ?? self::generateUuid();
	}
	
	/**
	 * Generate UUID (Universally Unique Identifier).
	 * 
	 * The generated UUID is a randomly generated string which uniquely identifies a single runtime instance of the 
	 * application.
	 * 
	 * @return string
	 * <p>The generated UUID (Universally Unique Identifier).</p>
	 */
	final public static function generateUuid(): string
	{
		self::$uuid = implode('-', array_map('bin2hex', [
			random_bytes(4), random_bytes(2), random_bytes(2), random_bytes(2), random_bytes(6)
		]));
		return self::$uuid;
	}
	
	/**
	 * Get origin.
	 * 
	 * The returning origin is the originally used entry point to execute the application, 
	 * such as <samp>POST http://myservice.com/myresource</samp> when the origin is an HTTP request for example.
	 * 
	 * @return string
	 * <p>The origin.</p>
	 */
	final public static function getOrigin(): string
	{
		//global
		global $argv;
		
		//origin
		if (self::$origin === null) {
			if (isset($_SERVER['REQUEST_METHOD'])) {
				//TODO: fetch current web URL from another proper method
				self::$origin = "{$_SERVER['REQUEST_METHOD']} " . ($_SERVER['REQUEST_SCHEME'] ?? 'http') . '://' . 
					($_SERVER['HTTP_HOST'] ?? 'localhost');
				if (isset($_SERVER['SERVER_PORT']) && !in_array($_SERVER['SERVER_PORT'], [80, 443])) {
					self::$origin .= ":{$_SERVER['SERVER_PORT']}";
				}
				if (isset($_SERVER['REQUEST_URI'])) {
					self::$origin .= $_SERVER['REQUEST_URI'];
				}
			} elseif (!empty($argv)) {
				self::$origin = implode(' ', $argv);
			} else {
				self::$origin = $_SERVER['SCRIPT_FILENAME'];
			}
		}
		
		//return
		return self::$origin;
	}
	
	/**
	 * Set origin.
	 * 
	 * @param string $origin
	 * <p>The origin to set.</p>
	 * @return void
	 */
	final public static function setOrigin(string $origin): void
	{
		if ($origin === '') {
			UCall::haltParameter('origin', $origin, ['error_message' => "An empty origin is not allowed."]);
		}
		self::$origin = $origin;
	}
	
	/**
	 * Unset origin.
	 * 
	 * @return void
	 */
	final public static function unsetOrigin(): void
	{
		self::$origin = null;
	}
}
