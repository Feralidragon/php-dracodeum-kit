<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
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
	 * @return coercible:text
	 * The given value textified.
	 */
	public function textify(mixed $value);
}
