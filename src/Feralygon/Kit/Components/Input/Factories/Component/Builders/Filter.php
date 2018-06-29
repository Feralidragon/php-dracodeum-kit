<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Components\Input\Factories\Component\Builders;

use Feralygon\Kit\Factory\Builder;
use Feralygon\Kit\Components\Input\Factories\Component\Builder\Interfaces\Filter as IBuilder;
use Feralygon\Kit\Components\Input\Components\Modifiers\Filter as Component;

/**
 * This builder is used to build filter instances.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Components\Input\Factories\Component
 * @see \Feralygon\Kit\Components\Input\Components\Modifiers\Filter
 * [object]
 */
class Filter extends Builder implements IBuilder
{
	//Implemented public methods (Feralygon\Kit\Components\Input\Factories\Component\Builder\Interfaces\Filter)
	/** {@inheritdoc} */
	public function build($prototype, array $properties = []): Component
	{
		return new Component($prototype, $properties);
	}
}
