<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Root;

use Dracodeum\Kit\Interfaces\Uninstantiable as IUninstantiable;
use Dracodeum\Kit\Traits;
use Dracodeum\Kit\Root\Remote\Exceptions;
use Dracodeum\Kit\Utilities\{
	Call as UCall,
	Text as UText
};

/**
 * This class represents the remote user client and is used to get remote information such as the IP address and agent.
 */
final class Remote implements IUninstantiable
{
	//Traits
	use Traits\Uninstantiable;
	
	
	
	//Private static properties
	/** @var callable[] */
	private static $ip_address_getters = [];
	
	/** @var callable[] */
	private static $agent_getters = [];
	
	
	
	//Final public static methods
	/**
	 * Get IP address.
	 * 
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Root\Remote\Exceptions\IpAddressNotSet
	 * @return string|null
	 * <p>The IP address.<br>
	 * If <var>$no_throw</var> is set to boolean <code>true</code>, 
	 * then <code>null</code> is returned if none is set.</p>
	 */
	final public static function getIpAddress(bool $no_throw = false): ?string
	{
		//getters
		foreach (self::$ip_address_getters as $getter) {
			$ip_address = $getter();
			if (!UText::empty($ip_address)) {
				return $ip_address;
			}
		}
		
		//remote
		if (!UText::empty($_SERVER['REMOTE_ADDR'] ?? null)) {
			return $_SERVER['REMOTE_ADDR'];
		}
		
		//finalize
		if ($no_throw) {
			return null;
		}
		throw new Exceptions\IpAddressNotSet();
	}
	
	/**
	 * Add IP address getter function.
	 * 
	 * @param callable $getter
	 * <p>The getter function to use to get the IP address.<br>
	 * It is expected to be compatible with the following signature:<br>
	 * <br>
	 * <code>function (): ?string</code><br>
	 * <br>
	 * Return: <code><b>string|null</b></code><br>
	 * The IP address or <code>null</code> if none is set.</p>
	 */
	final public static function addIpAddressGetter(callable $getter): void
	{
		UCall::assert('getter', $getter, function (): ?string {});
		self::$ip_address_getters[] = $getter;
	}
	
	/**
	 * Get agent.
	 * 
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Root\Remote\Exceptions\AgentNotSet
	 * @return string|null
	 * <p>The agent.<br>
	 * If <var>$no_throw</var> is set to boolean <code>true</code>, 
	 * then <code>null</code> is returned if none is set.</p>
	 */
	final public static function getAgent(bool $no_throw = false): ?string
	{
		//getters
		foreach (self::$agent_getters as $getter) {
			$agent = $getter();
			if (!UText::empty($agent)) {
				return $agent;
			}
		}
		
		//remote
		if (!UText::empty($_SERVER['HTTP_USER_AGENT'] ?? null)) {
			return $_SERVER['HTTP_USER_AGENT'];
		}
		
		//finalize
		if ($no_throw) {
			return null;
		}
		throw new Exceptions\AgentNotSet();
	}
	
	/**
	 * Add agent getter function.
	 * 
	 * @param callable $getter
	 * <p>The getter function to use to get the agent.<br>
	 * It is expected to be compatible with the following signature:<br>
	 * <br>
	 * <code>function (): ?string</code><br>
	 * <br>
	 * Return: <code><b>string|null</b></code><br>
	 * The agent or <code>null</code> if none is set.</p>
	 */
	final public static function addAgentGetter(callable $getter): void
	{
		UCall::assert('getter', $getter, function (): ?string {});
		self::$agent_getters[] = $getter;
	}
}
