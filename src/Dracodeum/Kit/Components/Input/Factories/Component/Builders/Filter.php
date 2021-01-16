<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components\Input\Factories\Component\Builders;

use Dracodeum\Kit\Factory\Builder;
use Dracodeum\Kit\Components\Input\Factories\Component\Builder\Interfaces\Filter as IBuilder;
use Dracodeum\Kit\Components\Input\Components\Modifiers\Filter as Component;

class Filter extends Builder implements IBuilder
{
	//Implemented public methods (Dracodeum\Kit\Components\Input\Factories\Component\Builder\Interfaces\Filter)
	/** {@inheritdoc} */
	public function build($prototype, array $properties): Component
	{
		return new Component($prototype, $properties);
	}
}
