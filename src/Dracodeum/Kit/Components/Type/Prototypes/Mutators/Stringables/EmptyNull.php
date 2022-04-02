<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components\Type\Prototypes\Mutators\Stringables;

use Dracodeum\Kit\Components\Type\Prototypes\Mutators\Stringable as Prototype;

/** This prototype sets a given stringable value to `null` if it's empty. */
class EmptyNull extends Prototype
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function process(mixed &$value)
	{
		if ((string)$value === '') {
			$value = null;
		}
	}
}
