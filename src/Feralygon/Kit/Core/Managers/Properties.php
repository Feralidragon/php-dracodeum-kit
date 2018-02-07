<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Managers;

use Feralygon\Kit\Core\Managers\Properties\Exceptions;

/**
 * Core properties manager class.
 * 
 * This manager handles and stores a separate set of properties.
 * 
 * //TODO: more complete description
 * 
 * @since 1.0.0
 */
class Properties
{
	//Public constants
	/** Allowed modes. */
	public const MODES = ['rw', 'r', 'w', 'w-'];
	
	
	
	//Private properties
	/** @var object */
	private $owner;
	
	/** @var bool */
	private $lazy = false;
	
	/** @var string|null */
	private $mode = null;
	
	/** @var bool */
	private $initialized = false;
	
	/** @var bool[] */
	private $required_map = [];
	
	
	
	//Final public magic methods
	/**
	 * Instantiate class.
	 * 
	 * @since 1.0.0
	 * @param object $owner <p>The owner object.</p>
	 * @param bool $lazy [default = false] <p>Use lazy-loading, so that each property is only loaded on access.<br>
	 * <br>
	 * NOTE: With lazy-loading, the existence of each property becomes unknown ahead of time, 
	 * therefore when retrieving a list of all properties, only a list of the currently loaded ones is returned.</p>
	 * @param string|null $mode [default = null] <p>The read and write mode to set for all properties, 
	 * which, if set, must be one the following:<br>
	 * &nbsp; &#8226; &nbsp; <samp>rw</samp> : Allow all properties to be both read from 
	 * and written to (read-write).<br>
	 * &nbsp; &#8226; &nbsp; <samp>r</samp> : Allow all properties to be only read from (read-only).<br>
	 * &nbsp; &#8226; &nbsp; <samp>w</samp> : Allow all properties to be only written to (write-only).<br>
	 * &nbsp; &#8226; &nbsp; <samp>w-</samp> : Allow all properties to be only written to, 
	 * and only once during instantiation (write-once).
	 * </p>
	 * @throws \Feralygon\Kit\Core\Managers\Properties\Exceptions\InvalidOwner
	 * @throws \Feralygon\Kit\Core\Managers\Properties\Exceptions\InvalidMode
	 */
	final public function __construct($owner, bool $lazy = false, ?string $mode = null)
	{
		//owner
		if (!is_object($owner)) {
			throw new Exceptions\InvalidOwner(['manager' => $this, 'owner' => $owner]);
		}
		$this->owner = $owner;
		
		//lazy
		$this->lazy = $lazy;
		
		//mode
		if (isset($mode) && !in_array($mode, self::MODES, true)) {
			throw new Exceptions\InvalidMode(['manager' => $this, 'mode' => $mode, 'modes' => self::MODES]);
		}
		$this->mode = $mode;
	}
	
	
	
	//Final public methods
	/**
	 * Add a given set of required property names.
	 * 
	 * The properties, corresponding to the given required property names added here, 
	 * must be given during initialization.<br>
	 * This method can only be called before initialization.
	 * 
	 * @since 1.0.0
	 * @param string[] $names <p>The required property names to add.</p>
	 * @throws \Feralygon\Kit\Core\Managers\Properties\Exceptions\AlreadyInitialized
	 * @return $this <p>This instance, for chaining purposes.</p>
	 */
	final public function addRequiredPropertyNames(array $names) : Properties
	{
		if ($this->initialized) {
			throw new Exceptions\AlreadyInitialized(['manager' => $this]);
		}
		$this->required_map += array_fill_keys($names, true);
		return $this;
	}
	
	/**
	 * Check if is initialized.
	 * 
	 * @since 1.0.0
	 * @return bool <p>Boolean <code>true</code> if is initialized.</p>
	 */
	final public function isInitialized() : bool
	{
		return $this->initialized;
	}
	
	/**
	 * Initialize with a given set of properties.
	 * 
	 * @since 1.0.0
	 * @param array $properties <p>The properties to initialize with, as <samp>name => value</samp> pairs.</p>
	 * @return $this <p>This instance, for chaining purposes.</p>
	 */
	final public function initialize(array $properties) : Properties
	{
		
		//TODO
		
		$this->initialized = true;
		
		//TODO
		
		return $this;
	}
}
