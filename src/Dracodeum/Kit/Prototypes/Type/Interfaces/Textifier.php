<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Prototypes\Type\Interfaces;

interface Textifier
{
	//Public methods
	/**
	 * Textify a given value.
	 * 
	 * @param mixed $value
	 * The value, already validated and normalized, to textify.
	 * 
	 * @return coercible:text|null
	 * The given value textified, or `null` if no textification occurred.
	 */
	public function textify(mixed $value);
}
