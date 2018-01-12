<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Root\System\Objects;

/**
 * Root system OS (Operating System) object.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Root\System
 */
final class Os
{
	//Private properties
	/** @var string */
	private $name;
	
	/** @var string */
	private $hostname;
	
	/** @var string */
	private $release;
	
	/** @var string */
	private $information;
	
	/** @var string */
	private $architecture;
	
	
	
	//Final public magic methods
	/**
	 * Instantiate class.
	 * 
	 * @since 1.0.0
	 * @param string $name <p>The name.</p>
	 * @param string $hostname <p>The hostname.</p>
	 * @param string $release <p>The release.</p>
	 * @param string $information <p>The information.</p>
	 * @param string $architecture <p>The architecture.</p>
	 */
	final public function __construct(string $name, string $hostname, string $release, string $information, string $architecture)
	{
		$this->name = $name;
		$this->hostname = $hostname;
		$this->release = $release;
		$this->information = $information;
		$this->architecture = $architecture;
	}
	
	
	
	//Final public methods
	/**
	 * Get name.
	 * 
	 * @since 1.0.0
	 * @return string <p>The name.</p>
	 */
	final public function getName() : string
	{
		return $this->name;
	}
	
	/**
	 * Get hostname.
	 * 
	 * @since 1.0.0
	 * @return string <p>The hostname.</p>
	 */
	final public function getHostname() : string
	{
		return $this->hostname;
	}
	
	/**
	 * Get release.
	 * 
	 * @since 1.0.0
	 * @return string <p>The release.</p>
	 */
	final public function getRelease() : string
	{
		return $this->release;
	}
	
	/**
	 * Get information.
	 * 
	 * @since 1.0.0
	 * @return string <p>The information.</p>
	 */
	final public function getInformation() : string
	{
		return $this->information;
	}
	
	/**
	 * Get architecture.
	 * 
	 * @since 1.0.0
	 * @return string <p>The architecture.</p>
	 */
	final public function getArchitecture() : string
	{
		return $this->architecture;
	}
	
	/**
	 * Check if is Linux.
	 * 
	 * @since 1.0.0
	 * @return bool <p>Boolean <code>true</code> if is Linux.</p>
	 */
	final public function isLinux() : bool
	{
		return $this->name === 'Linux';
	}
	
	/**
	 * Check if is Windows.
	 * 
	 * @since 1.0.0
	 * @return bool <p>Boolean <code>true</code> if is Windows.</p>
	 */
	final public function isWindows() : bool
	{
		return strtoupper(substr($this->name, 0, 3)) === 'WIN';
	}
	
	/**
	 * Check if is Unix.
	 * 
	 * @since 1.0.0
	 * @return bool <p>Boolean <code>true</code> if is Unix.</p>
	 */
	final public function isUnix() : bool
	{
		return !$this->isWindows();
	}
}
