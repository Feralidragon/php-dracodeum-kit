<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Factories\Component\Builders;

use Feralygon\Kit\Factory\Builder;
use Feralygon\Kit\Factories\Component\Builder\Interfaces\Input as IBuilder;
use Feralygon\Kit\Components\Input as Component;

/**
 * This builder is used to build input instances.
 * 
 * @see \Feralygon\Kit\Components\Input
 * [object]
 */
class Input extends Builder implements IBuilder
{
	//Implemented public methods (Feralygon\Kit\Factories\Component\Builder\Interfaces\Input)
	/** {@inheritdoc} */
	public function build($prototype, array $properties): Component
	{
		return new Component($prototype, $properties);
	}
}
