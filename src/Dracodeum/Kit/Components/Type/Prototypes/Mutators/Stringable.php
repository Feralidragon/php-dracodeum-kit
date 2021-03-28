<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components\Type\Prototypes\Mutators;

use Dracodeum\Kit\Components\Type\Prototypes\Mutator as Prototype;
use Dracodeum\Kit\Components\Type\Prototypes\Mutator\Interfaces\Validator as IValidator;

abstract class Stringable extends Prototype implements IValidator
{
	//Implemented public methods (Dracodeum\Kit\Components\Type\Prototypes\Mutator\Interfaces\Validator)
	/** {@inheritdoc} */
	public function validate(mixed $value): bool
	{
		return is_string($value);
	}
}
