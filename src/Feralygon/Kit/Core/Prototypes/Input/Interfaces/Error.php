<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Prototypes\Input\Interfaces;

/**
 * Core input prototype error interface.
 * 
 * This interface defines a method to unset an error from an input prototype.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Core\Prototypes\Input
 */
interface Error
{
	//Public methods
	/**
	 * Unset error.
	 * 
	 * @since 1.0.0
	 * @return void
	 */
	public function unsetError() : void;
}
