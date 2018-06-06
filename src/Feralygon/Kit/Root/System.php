<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Root;

use Feralygon\Kit\Root\System\{
	Structures,
	Environment,
	Environments,
	Exceptions
};
use Feralygon\Kit\Traits as KitTraits;
use Feralygon\Kit\Utilities\{
	Call as UCall,
	Text as UText,
	Type as UType
};

/**
 * This class represents the local system and is used to statically set up the environment, 
 * get local information and launch an application, thus it also holds a global system state with the currently 
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
	use KitTraits\NonInstantiable;
	use KitTraits\Memoization;
	
	
	
	//Private static properties
	/** @var \Feralygon\Kit\Root\System\Environment|null */
	private static $environment = null;
	
	/** @var \Feralygon\Kit\Root\System\Structures\Os|null */
	private static $os = null;
	
	
	
	//Final public static methods
	/**
	 * Get environment instance.
	 * 
	 * @since 1.0.0
	 * @return \Feralygon\Kit\Root\System\Environment
	 * <p>The environment instance.</p>
	 */
	final public static function getEnvironment() : Environment
	{
		return self::loadEnvironment();
	}
	
	/**
	 * Set environment.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Root\System\Environment|string $environment
	 * <p>The environment instance, class or name to set.</p>
	 * @return void
	 */
	final public static function setEnvironment($environment) : void
	{
		//build
		if (is_string($environment)) {
			$instance = self::buildEnvironment($environment);
			if (isset($instance)) {
				$environment = $instance;
			}
		}
		
		//guard
		UCall::guardParameter('environment', $environment, UType::evaluateObject($environment, Environment::class), [
			'hint_message' => "Only an environment instance, class or name is allowed."
		]);
		
		//set
		self::$environment = $environment;
		
		//initialize
		$initializer = (function () : void {
			$this->initialize();
		})->bindTo($environment, $environment);
		$initializer();
	}
	
	/**
	 * Check if is in debug mode.
	 * 
	 * When in debug mode, the system behaves in such a way so that code can be easily debugged, 
	 * by performing additional integrity checks during runtime, at the potential cost of lower performance 
	 * and a higher memory footprint.
	 * 
	 * @since 1.0.0
	 * @return bool
	 * <p>Boolean <code>true</code> if is in debug mode.</p>
	 */
	final public static function isDebug() : bool
	{
		return self::getEnvironment()->isDebug();
	}
	
	/**
	 * Set <samp>php.ini</samp> configuration option with a given name and value.
	 * 
	 * This method is mostly equivalent to the PHP <code>ini_set</code> function, however it holds no effect 
	 * if the package is set to be used as a library, throws an exception instead of failing silently, 
	 * and an integer, float, boolean or <code>null</code> value is also safely accepted.
	 * 
	 * @since 1.0.0
	 * @see https://php.net/manual/en/function.ini-set.php
	 * @param string $name
	 * <p>The name to set with.</p>
	 * @param string|int|float|bool|null $value
	 * <p>The value to set with.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Feralygon\Kit\Root\System\Exceptions\SetIniOption\Failed
	 * @return void|bool
	 * <p>If <var>$no_throw</var> is set to <code>true</code>, 
	 * then boolean <code>true</code> is returned if the configuration option with the given name was successfully set 
	 * with the given value, or boolean <code>false</code> if otherwise.</p>
	 */
	final public static function setIniOption(string $name, $value, bool $no_throw = false)
	{
		//guard
		UCall::guardParameter('value', $value, !isset($value) || is_scalar($value), [
			'hint_message' => "Only a string, integer, float, boolean or null value is allowed."
		]);
		
		//check
		if (Vendor::isLibrary()) {
			if ($no_throw) {
				return false;
			}
			return;
		}
		
		//value
		$ini_value = $value;
		if (!isset($ini_value)) {
			$ini_value = '';
		} elseif (is_bool($ini_value)) {
			$ini_value = $ini_value ? 'On' : 'Off';
		} else {
			$ini_value = (string)$ini_value;
		}
		
		//set
		if (ini_set($name, $ini_value) === false) {
			if ($no_throw) {
				return false;
			}
			throw new Exceptions\SetIniOption\Failed(['name' => $name, 'value' => $value]);
		} elseif ($no_throw) {
			return true;
		}
	}
	
	/**
	 * Set error reporting flags.
	 * 
	 * This method is mostly equivalent to the PHP <code>error_reporting</code> function, 
	 * however it holds no effect if the package is set to be used as a library.
	 * 
	 * @since 1.0.0
	 * @see https://php.net/manual/en/function.error-reporting.php
	 * @see https://php.net/manual/en/errorfunc.constants.php
	 * @param int $flags
	 * <p>The flags to set.</p>
	 * @return void
	 */
	final public static function setErrorReportingFlags(int $flags) : void
	{
		if (!Vendor::isLibrary()) {
			error_reporting($flags);
		}
	}
	
	/**
	 * Get hostname.
	 * 
	 * @since 1.0.0
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Feralygon\Kit\Root\System\Exceptions\HostnameNotSet
	 * @return string|null
	 * <p>The hostname.<br>
	 * If <var>$no_throw</var> is set to <code>true</code>, then <code>null</code> is returned if none is set.</p>
	 */
	final public static function getHostname(bool $no_throw = false) : ?string
	{
		$hostname = gethostname();
		if ($hostname === false) {
			if ($no_throw) {
				return null;
			}
			throw new Exceptions\HostnameNotSet();
		}
		return $hostname;
	}
	
	/**
	 * Get IP address.
	 * 
	 * @since 1.0.0
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Feralygon\Kit\Root\System\Exceptions\IpAddressNotSet
	 * @return string|null
	 * <p>The IP address.<br>
	 * If <var>$no_throw</var> is set to <code>true</code>, then <code>null</code> is returned if none is set.</p>
	 */
	final public static function getIpAddress(bool $no_throw = false) : ?string
	{
		//server
		if (isset($_SERVER['SERVER_ADDR'])) {
			return $_SERVER['SERVER_ADDR'];
		}
		
		//hostname
		$hostname = self::getHostname(true);
		if (isset($hostname)) {
			$ip_address = gethostbyname($hostname);
			if ($ip_address !== $hostname) {
				return $ip_address;
			}
		}
		
		//finish
		if ($no_throw) {
			return null;
		}
		throw new Exceptions\IpAddressNotSet();
	}
	
	/**
	 * Get OS (Operating System) instance.
	 * 
	 * @since 1.0.0
	 * @return \Feralygon\Kit\Root\System\Structures\Os
	 * <p>The OS (Operating System) instance.</p>
	 */
	final public static function getOs() : Structures\Os
	{
		if (!isset(self::$os)) {
			self::$os = new Structures\Os([
				'name' => php_uname('s'),
				'hostname' => php_uname('n'),
				'release' => php_uname('r'),
				'information' => php_uname('v'),
				'architecture' => php_uname('m')
			], true);
		}
		return self::$os;
	}
	
	/**
	 * Check if has command with a given name.
	 * 
	 * @since 1.0.0
	 * @param string $name
	 * <p>The name to check with.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if has command with the given name.</p>
	 */
	final public static function hasCommand(string $name) : bool
	{
		return self::memoize(function () use ($name) {
			UCall::guardParameter('name', $name, UText::isIdentifier($name), [
				'hint_message' => "Only alphanumeric ASCII characters (a-z, A-Z and 0-9) and " . 
					"underscore (_) are allowed, however the first character cannot be a number (0-9).",
				'function_name' => 'hasCommand'
			]);
			return !empty(self::getOs()->isWindows() ? `where {$name}` : `command -v {$name}`);
		});
	}
	
	
	
	//Final private methods
	/**
	 * Load environment instance.
	 * 
	 * @since 1.0.0
	 * @return \Feralygon\Kit\Root\System\Environment
	 * <p>The loaded environment instance.</p>
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
	 * @param string $name
	 * <p>The name to build for.</p>
	 * @return \Feralygon\Kit\Root\System\Environment|null
	 * <p>The built environment instance for the given name or <code>null</code> if none was built.</p>
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
