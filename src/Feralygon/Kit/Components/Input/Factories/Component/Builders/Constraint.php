<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Components\Input\Factories\Component\Builders;

use Feralygon\Kit\Factory\Builder;
use Feralygon\Kit\Components\Input\Factories\Component\Builder\Interfaces\Constraint as IBuilder;
use Feralygon\Kit\Components\Input\Components\Modifiers\Constraint as Component;

/**
 * This builder is used to build constraint instances.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Components\Input\Factories\Component
 * @see \Feralygon\Kit\Components\Input\Components\Modifiers\Constraint
 * [object]
 */
class Constraint extends Builder implements IBuilder
{
	//Implemented public methods (Feralygon\Kit\Components\Input\Factories\Component\Builder\Interfaces\Constraint)
	/** {@inheritdoc} */
	public function build($prototype, array $properties = []) : Component
	{
		return new Component($prototype, $properties);
	}
}
