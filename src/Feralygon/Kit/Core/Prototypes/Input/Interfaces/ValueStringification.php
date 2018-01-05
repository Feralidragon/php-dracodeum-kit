<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Prototypes\Input\Interfaces;

use Feralygon\Kit\Core\Options\Text as TextOptions;

/**
 * Core input prototype value stringification interface.
 * 
 * This interface defines a method to stringify a value in an input prototype.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Core\Prototypes\Input
 */
interface ValueStringification
{
	//Public methods
	/**
	 * Generate a string from a given value.
	 * 
	 * @since 1.0.0
	 * @param mixed $value <p>The value to generate a string from.</p>
	 * @param \Feralygon\Kit\Core\Options\Text $text_options <p>The text options instance to use.</p>
	 * @return string <p>The generated string from the given value.</p>
	 */
	public function stringifyValue($value, TextOptions $text_options) : string;
}
