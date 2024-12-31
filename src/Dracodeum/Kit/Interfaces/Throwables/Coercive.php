<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Interfaces\Throwables;

/** This interface defines a set of methods to get the value, error code and message from a coercive throwable. */
interface Coercive extends \Throwable
{
	//Public methods
	/**
	 * Get value.
	 * 
	 * @return mixed
	 * <p>The value.</p>
	 */
	public function getValue();
	
	/**
	 * Get error code.
	 * 
	 * @return string|null
	 * <p>The error code or <code>null</code> if none is set.</p>
	 */
	public function getErrorCode(): ?string;
	
	/**
	 * Get error message.
	 * 
	 * @return string|null
	 * <p>The error message or <code>null</code> if none is set.</p>
	 */
	public function getErrorMessage(): ?string;
}
