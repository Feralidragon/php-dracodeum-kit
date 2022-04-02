<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Interfaces\Throwables;

use Throwable as IThrowable;

/** This interface defines a set of methods to get the value and error message from a coercive throwable. */
interface CoercivePhp8 extends IThrowable
{
	//Public methods
	/**
	 * Get value.
	 * 
	 * @return mixed
	 * <p>The value.</p>
	 */
	public function getValue(): mixed;
	
	/**
	 * Get error message.
	 * 
	 * @return string|null
	 * <p>The error message or <code>null</code> if none is set.</p>
	 */
	public function getErrorMessage(): ?string;
}
