<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Factories\Options\Builders;

use Feralygon\Kit\Factory\Builder;
use Feralygon\Kit\Factories\Options\Builder\Interfaces\Text as IBuilder;
use Feralygon\Kit\Options\Text as Options;

/**
 * This builder is used to build text instances.
 * 
 * @see \Feralygon\Kit\Options\Text
 * [object]
 */
class Text extends Builder implements IBuilder
{
	//Implemented public methods (Feralygon\Kit\Factories\Options\Builder\Interfaces\Text)
	/** {@inheritdoc} */
	public function build(array $properties): Options
	{
		return new Options($properties);
	}
}
