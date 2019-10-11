<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Prototypes\Input\Interfaces;

use Feralygon\Kit\Options\Text as TextOptions;

/** This interface defines a method to stringify a value in an input prototype. */
interface ValueStringifier
{
	//Public methods
	/**
	 * Generate a string from a given value.
	 * 
	 * @param mixed $value
	 * <p>The value to generate a string from.</p>
	 * @param \Feralygon\Kit\Options\Text $text_options
	 * <p>The text options instance to use.</p>
	 * @return string
	 * <p>The generated string from the given value.</p>
	 */
	public function stringifyValue($value, TextOptions $text_options): string;
}
