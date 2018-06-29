<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Interfaces\Throwables;

/**
 * This interface defines a set of methods to get the value, error code and message from a coercive throwable.
 * 
 * @since 1.0.0
 */
interface Coercive extends \Throwable
{
	//Public methods
	/**
	 * Get value.
	 * 
	 * @since 1.0.0
	 * @return mixed
	 * <p>The value.</p>
	 */
	public function getValue();
	
	/**
	 * Get error code.
	 * 
	 * @since 1.0.0
	 * @return string|null
	 * <p>The error code or <code>null</code> if none is set.</p>
	 */
	public function getErrorCode(): ?string;
	
	/**
	 * Get error message.
	 * 
	 * @since 1.0.0
	 * @return string|null
	 * <p>The error message or <code>null</code> if none is set.</p>
	 */
	public function getErrorMessage(): ?string;
}
