<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Factories\Component\Builders;

use Dracodeum\Kit\Factory\Builder;
use Dracodeum\Kit\Factories\Component\Builder\Interfaces\Store as IBuilder;
use Dracodeum\Kit\Components\Store as Component;

/**
 * This builder is used to build store instances.
 * 
 * @see \Dracodeum\Kit\Components\Store
 * [object]
 */
class Store extends Builder implements IBuilder
{
	//Implemented public methods (Dracodeum\Kit\Factories\Component\Builder\Interfaces\Store)
	/** {@inheritdoc} */
	public function build($prototype, array $properties): Component
	{
		return new Component($prototype, $properties);
	}
}
