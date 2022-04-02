<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components\Type\Prototypes\Mutator\Interfaces;

interface ExplanationProducer
{
	//Public methods
	/**
	 * Produce explanation.
	 * 
	 * @return coercible:text
	 * The produced explanation.
	 */
	public function produceExplanation();
}
