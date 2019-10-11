<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces;

use Feralygon\Kit\Options\Text as TextOptions;

/** This interface defines a method to get the string from an input modifier prototype. */
interface Stringification
{
	//Public methods
	/**
	 * Get string.
	 * 
	 * The returning string is meant to represent the full set of properties which defines how a value is evaluated.
	 * 
	 * @param \Feralygon\Kit\Options\Text $text_options
	 * <p>The text options instance to use.</p>
	 * @return string
	 * <p>The string.</p>
	 */
	public function getString(TextOptions $text_options): string;
}
