<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Root;

use Feralygon\Kit\Root\System\{
	Objects,
	Environment,
	Environments,
	Exceptions
};
use Feralygon\Kit\Core\Traits\{
	NonInstantiable as TNonInstantiable,
	Memoization as TMemoization
};
use Feralygon\Kit\Core\Utilities\{
	Text as UText,
	Type as UType
};

/**
 * Root system class.
 * 
 * This class represents the local system and is used to statically set up the environment, retrieve local information and launch an application, 
 * thus it also holds a global system state with the currently active system related objects, such as the operating system, main process, environment 
 * and running application, all of which can be accessed statically from anywhere through this class.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Root\System\Environments\Development [environment, name = 'development']
 * @see \Feralygon\Kit\Root\System\Environments\Staging [environment, name = 'staging']
 * @see \Feralygon\Kit\Root\System\Environments\Production [environment, name = 'production']
 */
final class System
{
	//Traits
	use TNonInstantiable;
	use TMemoization;
	
	
	
	//Private static properties
	/** @var bool */
	private static $library = false;
	
	/** @var \Feralygon\Kit\Root\System\Environment */
	private static $environment;
	
	/** @var \Feralygon\Kit\Root\System\Objects\Os */
	private static $os;
	
	
	
	//Final public static methods
	/**
	 * Use as library.
	 * 
	 * When set to be used as a library, it will only work as such, thus it won't modify any global PHP settings 
	 * (such as ones through <code>ini_set</code> calls) which might be used and set by other scripts, frameworks or any other systems being used instead.
	 * 
	 * @since 1.0.0
	 * @return void
	 */
	final public static function useAsLibrary() : void
	{
		self::$library = true;
	}
	
	/**
	 * Check if is library.
	 * 
	 * @since 1.0.0
	 * @return bool <p>Boolean <samp>true</samp> if is a library.</p>
	 */
	final public static function isLibrary() : bool
	{
		return self::$library;
	}
	
	/**
	 * Get environment instance.
	 * 
	 * @since 1.0.0
	 * @return \Feralygon\Kit\Root\System\Environment <p>The environment instance.</p>
	 */
	final public static function getEnvironment() : Environment
	{
		return self::loadEnvironment();
	}
	
	/**
	 * Set environment.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Root\System\Environment|string $environment <p>The environment instance, class or name to set.</p>
	 * @throws \Feralygon\Kit\Root\System\Exceptions\InvalidEnvironment
	 * @return void
	 */
	final public static function setEnvironment($environment) : void
	{
		//validate
		if (is_string($environment)) {
			$instance = self::buildEnvironment($environment);
			if (isset($instance)) {
				$environment = $instance;
			}
		}
		if (!UType::evaluateObject($environment, Environment::class)) {
			throw new Exceptions\InvalidEnvironment(['environment' => $environment]);
		}
		
		//set
		if (!self::$library) {
			ini_set('display_errors', $environment->canDisplayErrors());
			error_reporting($environment->getErrorReportingFlags());
		}
		self::$environment = $environment;
	}
	
	/**
	 * Get hostname.
	 * 
	 * @since 1.0.0
	 * @return string|null <p>The hostname or <samp>null</samp> if none is set.</p>
	 */
	final public static function getHostname() : ?string
	{
		$hostname = gethostname();
		return $hostname !== false ? $hostname : null;
	}
	
	/**
	 * Get IP address.
	 * 
	 * @since 1.0.0
	 * @return string|null <p>The IP address or <samp>null</samp> if none is set.</p>
	 */
	final public static function getIpAddress() : ?string
	{
		$ip_address = $_SERVER['SERVER_ADDR'] ?? null;
		if (!isset($ip_address)) {
			$hostname = self::getHostname();
			if (!isset($hostname)) {
				return null;
			}
			$ip_address = gethostbyname($hostname);
			if ($ip_address === $hostname) {
				return null;
			}
		}
		return $ip_address;
	}
	
	/**
	 * Get OS (Operating System) instance.
	 * 
	 * @since 1.0.0
	 * @return \Feralygon\Kit\Root\System\Objects\Os <p>The OS (Operating System) instance.</p>
	 */
	final public static function getOs() : Objects\Os
	{
		if (!isset(self::$os)) {
			self::$os = new Objects\Os(php_uname('s'), php_uname('n'), php_uname('r'), php_uname('v'), php_uname('m'));
		}
		return self::$os;
	}
	
	/**
	 * Check if has a command with a given name.
	 * 
	 * @since 1.0.0
	 * @param string $name <p>The command name to check for.</p>
	 * @throws \Feralygon\Kit\Root\System\Exceptions\InvalidCommandName
	 * @return bool <p>Boolean <samp>true</samp> if has a command with the given name.</p>
	 */
	final public static function hasCommand(string $name) : bool
	{
		return self::memoize(function () use ($name) {
			if (!UText::isIdentifier($name)) {
				throw new Exceptions\InvalidCommandName(['name' => $name]);
			}
			return !empty(self::getOs()->isWindows() ? `where {$name}` : `command -v {$name}`);
		});
	}
	
	
	
	//Final private methods
	/**
	 * Load environment instance.
	 * 
	 * @since 1.0.0
	 * @return \Feralygon\Kit\Root\System\Environment <p>The loaded environment instance.</p>
	 */
	final private static function loadEnvironment() : Environment
	{
		if (!isset(self::$environment)) {
			self::setEnvironment('production');
		}
		return self::$environment;
	}
	
	/**
	 * Build environment instance for a given name.
	 * 
	 * @since 1.0.0
	 * @param string $name <p>The environment name to build for.</p>
	 * @return \Feralygon\Kit\Root\System\Environment|null <p>The built environment instance for the given name or <samp>null</samp> if none was built.</p>
	 */
	final private static function buildEnvironment(string $name) : ?Environment
	{
		switch ($name) {
			case 'development':
				return new Environments\Development();
			case 'staging':
				return new Environments\Staging();
			case 'production':
				return new Environments\Production();
		}
		return null;
	}
}
