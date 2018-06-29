<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Interfaces;

use Feralygon\Kit\Options\Text as TextOptions;

/**
 * This interface defines a method to cast an object to a string.
 * 
 * @since 1.0.0
 */
interface Stringifiable
{
	//Public methods
	/**
	 * Cast this object to a string.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Options\Text|null $text_options [default = null]
	 * <p>The text options instance to use.</p>
	 * @return string
	 * <p>This object cast to a string.</p>
	 */
	public function toString(?TextOptions $text_options = null): string;
}
