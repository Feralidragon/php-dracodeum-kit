<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Interfaces;

/** This interface defines a method to cast an object to a string. */
interface Stringable
{
	//Public methods
	/**
	 * Cast this object to a string.
	 * 
	 * @param coercible<\Dracodeum\Kit\Options\Text>|null $text_options [default = null]
	 * <p>The text options to use.</p>
	 * @return string
	 * <p>This object cast to a string.</p>
	 */
	public function toString($text_options = null): string;
}
