<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Managers\Memoization\Store;

use Dracodeum\Kit\Interfaces\Cloneable as ICloneable;
use Dracodeum\Kit\Traits;

/** @internal */
final class Key implements ICloneable
{
	//Traits
	use Traits\Cloneable;
	
	
	
	//Private properties	
	/** @var string */
	private $name;
	
	/** @var mixed */
	private $value;
	
	/** @var float|null */
	private $expiry = null;
	
	
	
	//Final public magic methods
	/**
	 * Instantiate class.
	 * 
	 * @param string $name
	 * <p>The name to instantiate with.</p>
	 * @param mixed $value
	 * <p>The value to instantiate with.</p>
	 * @param float|null $expiry [default = null]
	 * <p>The expiry to instantiate with, as a Unix timestamp, with microseconds.</p>
	 */
	final public function __construct(string $name, $value, ?float $expiry = null)
	{
		$this->name = $name;
		$this->value = $value;
		$this->expiry = $expiry;
	}
	
	
	
	//Final public methods
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
	 * Get value.
	 * 
	 * @return mixed
	 * <p>The value.</p>
	 */
	final public function getValue()
	{
		return $this->value;
	}
	
	/**
	 * Check if has expiry.
	 * 
	 * @return bool
	 * <p>Boolean <code>true</code> if has expiry.</p>
	 */
	final public function hasExpiry(): bool
	{
		return isset($this->expiry);
	}
	
	/**
	 * Get expiry.
	 * 
	 * @return float|null
	 * <p>The expiry, as a Unix timestamp, with microseconds, or <code>null</code> if none is set.</p>
	 */
	final public function getExpiry(): ?float
	{
		return $this->expiry;
	}
}
