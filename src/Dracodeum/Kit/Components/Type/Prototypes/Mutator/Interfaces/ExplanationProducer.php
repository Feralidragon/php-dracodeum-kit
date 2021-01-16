<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components\Type\Prototypes\Mutator\Interfaces;

/** This interface defines a method to produce an explanation. */
interface ExplanationProducer
{
	//Public methods
	/**
	 * Produce explanation.
	 * 
	 * @return coercible:text
	 * <p>The produced explanation.</p>
	 */
	public function produceExplanation();
}
