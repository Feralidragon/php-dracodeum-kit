<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Prototypes\Input\Interfaces;

/**
 * This interface defines a method to unset the error from an input prototype.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Prototypes\Input
 */
interface ErrorUnset
{
	//Public methods
	/**
	 * Unset error.
	 * 
	 * @since 1.0.0
	 * @return void
	 */
	public function unsetError(): void;
}
