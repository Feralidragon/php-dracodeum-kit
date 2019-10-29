<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Interfaces;

use Dracodeum\Kit\Options\Text as TextOptions;

/** This interface defines a method to cast an object to a string. */
interface Stringifiable
{
	//Public methods
	/**
	 * Cast this object to a string.
	 * 
	 * @param \Dracodeum\Kit\Options\Text|null $text_options [default = null]
	 * <p>The text options instance to use.</p>
	 * @return string
	 * <p>This object cast to a string.</p>
	 */
	public function toString(?TextOptions $text_options = null): string;
}
