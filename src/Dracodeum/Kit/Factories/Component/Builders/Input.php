<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Factories\Component\Builders;

use Dracodeum\Kit\Factory\Builder;
use Dracodeum\Kit\Factories\Component\Builder\Interfaces\Input as IBuilder;
use Dracodeum\Kit\Components\Input as Component;

class Input extends Builder implements IBuilder
{
	//Implemented public methods (Dracodeum\Kit\Factories\Component\Builder\Interfaces\Input)
	/** {@inheritdoc} */
	public function build($prototype, array $properties): Component
	{
		return new Component($prototype, $properties);
	}
}
