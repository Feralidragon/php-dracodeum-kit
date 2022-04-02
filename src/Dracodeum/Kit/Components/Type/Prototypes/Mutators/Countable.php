<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components\Type\Prototypes\Mutators;

use Dracodeum\Kit\Components\Type\Prototypes\Mutator as Prototype;
use Dracodeum\Kit\Components\Type\Prototypes\Mutator\Interfaces\Validator as IValidator;

/** @see https://www.php.net/manual/en/class.countable.php */
abstract class Countable extends Prototype implements IValidator
{
	//Implemented public methods (Dracodeum\Kit\Components\Type\Prototypes\Mutator\Interfaces\Validator)
	/** {@inheritdoc} */
	public function validate(mixed $value): bool
	{
		return is_countable($value);
	}
}
