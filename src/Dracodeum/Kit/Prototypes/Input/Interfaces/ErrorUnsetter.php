<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Prototypes\Input\Interfaces;

/** This interface defines a method to unset the error from an input prototype. */
interface ErrorUnsetter
{
	//Public methods
	/**
	 * Unset error.
	 * 
	 * @return void
	 */
	public function unsetError(): void;
}
