<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components\Type\Prototypes\Mutators\Stringable;

use Dracodeum\Kit\Components\Type\Prototypes\Mutator as Prototype;

/** This prototype sets a given stringable value to `null` if it's empty. */
class EmptyNull extends Prototype
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function process(mixed &$value)
	{
		if ($value === '') {
			$value = null;
		}
	}
}
