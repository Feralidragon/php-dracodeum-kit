<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Root\Loader\Objects;

/**
 * Root loader package object.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Root\Loader
 */
final class Package
{
	//Private properties
	/** @var string */
	private $vendor;
	
	/** @var string */
	private $name;
	
	/** @var string */
	private $path;
	
	
	
	//Final public magic methods
	/**
	 * Instantiate class.
	 * 
	 * @since 1.0.0
	 * @param string $vendor <p>The vendor.</p>
	 * @param string $name <p>The name.</p>
	 * @param string $path <p>The path.</p>
	 */
	final public function __construct(string $vendor, string $name, string $path)
	{
		$this->vendor = strtolower($vendor);
		$this->name = strtolower($name);
		$this->path = rtrim(str_replace('\\', '/', $path), '/');
	}
	
	
	
	//Final public methods
	/**
	 * Get vendor.
	 * 
	 * @since 1.0.0
	 * @return string <p>The vendor.</p>
	 */
	final public function getVendor() : string
	{
		return $this->vendor;
	}
	
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
	 * Get path.
	 * 
	 * @since 1.0.0
	 * @return string <p>The path.</p>
	 */
	final public function getPath() : string
	{
		return $this->path;
	}
}
