<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Root\System\Factories\Component\Builders;

use Dracodeum\Kit\Factory\Builder;
use Dracodeum\Kit\Root\System\Factories\Component\Builder\Interfaces\Environment as IBuilder;
use Dracodeum\Kit\Root\System\Components\Environment as Component;

/**
 * This builder is used to build environment instances.
 * 
 * @see \Dracodeum\Kit\Root\System\Components\Environment
 * [object]
 */
class Environment extends Builder implements IBuilder
{
	//Implemented public methods (Dracodeum\Kit\Root\System\Factories\Component\Builder\Interfaces\Environment)
	/** {@inheritdoc} */
	public function build($prototype, array $properties): Component
	{
		return new Component($prototype, $properties);
	}
}
