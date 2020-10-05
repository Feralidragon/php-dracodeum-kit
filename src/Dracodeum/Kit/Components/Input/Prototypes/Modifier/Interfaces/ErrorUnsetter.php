<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components\Input\Prototypes\Modifier\Interfaces;

/** This interface defines a method to unset the error from an input modifier prototype. */
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
