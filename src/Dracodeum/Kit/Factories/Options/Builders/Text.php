<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Factories\Options\Builders;

use Dracodeum\Kit\Factory\Builder;
use Dracodeum\Kit\Factories\Options\Builder\Interfaces\Text as IBuilder;
use Dracodeum\Kit\Options\Text as Options;

/**
 * This builder is used to build text instances.
 * 
 * @see \Dracodeum\Kit\Options\Text
 * [object]
 */
class Text extends Builder implements IBuilder
{
	//Implemented public methods (Dracodeum\Kit\Factories\Options\Builder\Interfaces\Text)
	/** {@inheritdoc} */
	public function build(array $properties): Options
	{
		return new Options($properties);
	}
}
