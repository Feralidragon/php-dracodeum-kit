<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Root\System\Factories\Component\Builders;

use Feralygon\Kit\Factory\Builder;
use Feralygon\Kit\Root\System\Factories\Component\Builder\Interfaces\Environment as IBuilder;
use Feralygon\Kit\Root\System\Components\Environment as Component;

/**
 * This builder is used to build environment instances.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Root\System\Factories\Component
 * @see \Feralygon\Kit\Root\System\Components\Environment
 * [object]
 */
class Environment extends Builder implements IBuilder
{
	//Implemented public methods (Feralygon\Kit\Root\System\Factories\Component\Builder\Interfaces\Environment)
	/** {@inheritdoc} */
	public function build($prototype, array $properties): Component
	{
		return new Component($prototype, $properties);
	}
}
