<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Root;

use Feralygon\Kit\Root\System\{
	Immutables,
	Environment,
	Environments,
	Exceptions
};
use Feralygon\Kit\Core\Traits as CoreTraits;
use Feralygon\Kit\Core\Utilities\{
	Text as UText,
	Type as UType
};

/**
 * Root system class.
 * 
 * This class represents the local system and is used to statically set up the environment, 
 * retrieve local information and launch an application, thus it also holds a global system state with the currently 
 * active system related objects, such as the operating system, main process, environment and running application, 
 * all of which can be accessed statically from anywhere through this class.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Root\System\Environments\Development 
 * [environment, name = 'development']
 * @see \Feralygon\Kit\Root\System\Environments\Staging 
 * [environment, name = 'staging']
 * @see \Feralygon\Kit\Root\System\Environments\Production 
 * [environment, name = 'production']
 */
final class System
{
	//Traits
	use CoreTraits\NonInstantiable;
	use CoreTraits\Memoization;
	
	
	
	//Private static properties
	/** @var \Feralygon\Kit\Root\System\Environment */
	private static $environment;
	
	/** @var \Feralygon\Kit\Root\System\Immutables\Os */
	private static $os;
	
	
	
	//Final public static methods
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
	 * @param \Feralygon\Kit\Root\System\Environment|string $environment <p>The environment instance, 
	 * class or name to set.</p>
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
		if (!Vendor::isLibrary()) {
			ini_set('display_errors', $environment->canDisplayErrors());
			error_reporting($environment->getErrorReportingFlags());
		}
		self::$environment = $environment;
	}
	
	/**
	 * Set <samp>php.ini</samp> configuration option with a given name with a given value.
	 * 
	 * This method is mostly equivalent to the PHP core <code>ini_set</code> function, however it holds no effect 
	 * if the package is set to be used as a library, throws an exception instead of failing silently, 
	 * and an integer, float, boolean or <samp>null</samp> value is also safely accepted.
	 * 
	 * @since 1.0.0
	 * @see https://www.php.net/manual/en/function.ini-set.php
	 * @param string $name <p>The <samp>php.ini</samp> configuration option name to set for.</p>
	 * @param string|int|float|bool|null $value <p>The <samp>php.ini</samp> configuration option value to set.</p>
	 * @throws \Feralygon\Kit\Root\System\Exceptions\SetIniOptionInvalidValueType
	 * @throws \Feralygon\Kit\Root\System\Exceptions\SetIniOptionFailed
	 * @return void
	 */
	final public static function setIniOption(string $name, $value) : void
	{
		//validate
		if (isset($value) && !is_scalar($value)) {
			throw new Exceptions\SetIniOptionInvalidValueType([
				'name' => $name,
				'value' => $value,
				'type' => gettype($value)
			]);
		} elseif (Vendor::isLibrary()) {
			return;
		}
		
		//value
		$ini_value = $value;
		if (!isset($ini_value)) {
			$ini_value = '';
		} elseif (is_bool($ini_value)) {
			$ini_value = $ini_value ? '1' : '0';
		} else {
			$ini_value = (string)$ini_value;
		}
		
		//set
		if (ini_set($name, $ini_value) === false) {
			throw new Exceptions\SetIniOptionFailed(['name' => $name, 'value' => $value]);
		}
	}
	
	/**
	 * Get hostname.
	 * 
	 * @since 1.0.0
	 * @return string|null <p>The hostname or <code>null</code> if none is set.</p>
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
	 * @return string|null <p>The IP address or <code>null</code> if none is set.</p>
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
	 * @return \Feralygon\Kit\Root\System\Immutables\Os <p>The OS (Operating System) instance.</p>
	 */
	final public static function getOs() : Immutables\Os
	{
		if (!isset(self::$os)) {
			self::$os = new Immutables\Os([
				'name' => php_uname('s'),
				'hostname' => php_uname('n'),
				'release' => php_uname('r'),
				'information' => php_uname('v'),
				'architecture' => php_uname('m')
			]);
		}
		return self::$os;
	}
	
	/**
	 * Check if has a command with a given name.
	 * 
	 * @since 1.0.0
	 * @param string $name <p>The command name to check for.</p>
	 * @throws \Feralygon\Kit\Root\System\Exceptions\InvalidCommandName
	 * @return bool <p>Boolean <code>true</code> if has a command with the given name.</p>
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
	 * @return \Feralygon\Kit\Root\System\Environment|null <p>The built environment instance for the given name 
	 * or <code>null</code> if none was built.</p>
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
