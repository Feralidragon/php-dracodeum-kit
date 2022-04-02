<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Root\Loader;

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
	 * @param string $vendor
	 * <p>The vendor to instantiate with.<br>
	 * It is converted to lowercase.</p>
	 * @param string $name
	 * <p>The name to instantiate with.<br>
	 * It is converted to lowercase.</p>
	 * @param string $path
	 * <p>The path to instantiate with.</p>
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
	 * @return string
	 * <p>The vendor.</p>
	 */
	final public function getVendor(): string
	{
		return $this->vendor;
	}
	
	/**
	 * Get name.
	 * 
	 * @return string
	 * <p>The name.</p>
	 */
	final public function getName(): string
	{
		return $this->name;
	}
	
	/**
	 * Get path.
	 * 
	 * @param string|null $class [default = null]
	 * <p>The class to get for.</p>
	 * @return string
	 * <p>The path.</p>
	 */
	final public function getPath(?string $class = null): string
	{
		$path = $this->path;
		if (isset($class)) {
			$path .= '/' . str_replace('\\', '/', $class) . '.php';
		}
		return $path;
	}
}
