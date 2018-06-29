<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Managers\Memoization\Store;

/**
 * @since 1.0.0
 * @internal
 * @see \Feralygon\Kit\Managers\Memoization\Store
 */
final class Key
{
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
	 * @since 1.0.0
	 * @param string $name
	 * <p>The name.</p>
	 * @param mixed $value
	 * <p>The value.</p>
	 * @param float|null $expiry [default = null]
	 * <p>The expiry, as an Unix timestamp, with microseconds.</p>
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
	 * @since 1.0.0
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
	 * @since 1.0.0
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
	 * @since 1.0.0
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
	 * @since 1.0.0
	 * @return float|null
	 * <p>The expiry, as an Unix timestamp, with microseconds, or <code>null</code> if none is set.</p>
	 */
	final public function getExpiry(): ?float
	{
		return $this->expiry;
	}
}
