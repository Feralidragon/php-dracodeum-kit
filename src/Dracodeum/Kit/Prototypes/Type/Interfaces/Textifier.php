<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Prototypes\Type\Interfaces;

/** This interface defines a method to textify a given value. */
interface Textifier
{
	//Public methods
	/**
	 * Textify a given value.
	 * 
	 * @param mixed $value
	 * <p>The value, already validated and normalized, to textify.</p>
	 * @return coercible:text
	 * <p>The given value textified.</p>
	 */
	public function textify(mixed $value);
}
