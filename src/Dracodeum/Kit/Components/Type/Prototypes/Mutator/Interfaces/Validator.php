<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components\Type\Prototypes\Mutator\Interfaces;

interface Validator
{
	//Public methods
	/**
	 * Validate a given value.
	 * 
	 * @param mixed $value
	 * The value to validate.
	 * 
	 * @return bool
	 * Boolean `true` if the given value is valid.
	 */
	public function validate(mixed $value): bool;
}
