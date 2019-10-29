<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components\Input\Factories\Component\Builders;

use Dracodeum\Kit\Factory\Builder;
use Dracodeum\Kit\Components\Input\Factories\Component\Builder\Interfaces\Constraint as IBuilder;
use Dracodeum\Kit\Components\Input\Components\Modifiers\Constraint as Component;

/**
 * This builder is used to build constraint instances.
 * 
 * @see \Dracodeum\Kit\Components\Input\Components\Modifiers\Constraint
 * [object]
 */
class Constraint extends Builder implements IBuilder
{
	//Implemented public methods (Dracodeum\Kit\Components\Input\Factories\Component\Builder\Interfaces\Constraint)
	/** {@inheritdoc} */
	public function build($prototype, array $properties): Component
	{
		return new Component($prototype, $properties);
	}
}
