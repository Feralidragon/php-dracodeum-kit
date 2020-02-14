<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Factories\Component\Builders;

use Dracodeum\Kit\Factory\Builder;
use Dracodeum\Kit\Factories\Component\Builder\Interfaces\Provider as IBuilder;
use Dracodeum\Kit\Components\Provider as Component;

/**
 * This builder is used to build provider instances.
 * 
 * @see \Dracodeum\Kit\Components\Provider
 * [object]
 */
class Provider extends Builder implements IBuilder
{
	//Implemented public methods (Dracodeum\Kit\Factories\Component\Builder\Interfaces\Provider)
	/** {@inheritdoc} */
	public function build($prototype, array $properties): Component
	{
		return new Component($prototype, $properties);
	}
}
