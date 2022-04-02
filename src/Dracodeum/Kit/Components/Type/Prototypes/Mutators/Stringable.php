<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components\Type\Prototypes\Mutators;

use Dracodeum\Kit\Components\Type\Prototypes\Mutator as Prototype;
use Dracodeum\Kit\Components\Type\Prototypes\Mutator\Interfaces\Validator as IValidator;
use Stringable as IStringable;

/** @see https://www.php.net/manual/en/class.stringable.php */
abstract class Stringable extends Prototype implements IValidator
{
	//Implemented public methods (Dracodeum\Kit\Components\Type\Prototypes\Mutator\Interfaces\Validator)
	/** {@inheritdoc} */
	public function validate(mixed $value): bool
	{
		return is_string($value) || $value instanceof IStringable;
	}
}
