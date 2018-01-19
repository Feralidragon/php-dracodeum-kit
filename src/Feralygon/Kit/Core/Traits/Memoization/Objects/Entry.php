<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Traits\Memoization\Objects;

/**
 * Core memoization trait entry object class.
 * 
 * @since 1.0.0
 * @internal
 * @see \Feralygon\Kit\Core\Traits\Memoization
 */
final class Entry
{
	//Public properties
	/** @var mixed */
	public $value;
	
	/** @var int|null */
	public $expire = null;
	
	
	
	//Final public magic methods
	/**
	 * Instantiate class.
	 * 
	 * @since 1.0.0
	 * @see https://en.wikipedia.org/wiki/Unix_time
	 * @param mixed $value <p>The value.</p>
	 * @param int|null $expire [default = null] <p>The expiration timestamp, in seconds (Unix timestamp).<br>
	 * If not set, the entry will never expire.</p>
	 */
	final public function __construct($value, ?int $expire = null)
	{
		$this->value = $value;
		$this->expire = $expire;
	}
}
