<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Prototypes\Type\Interfaces;

/** This interface defines a set of methods to produce information, namely a label and a description. */
interface InformationProducer
{
	//Public methods
	/**
	 * Produce label.
	 * 
	 * @param enum<\Dracodeum\Kit\Components\Type\Enumerations\Context> $context
	 * <p>The context to produce for.</p>
	 * @return coercible:text
	 * <p>The produced label.</p>
	 */
	public function produceLabel($context);
	
	/**
	 * Produce description.
	 * 
	 * @param enum<\Dracodeum\Kit\Components\Type\Enumerations\Context> $context
	 * <p>The context to produce for.</p>
	 * @return coercible:text
	 * <p>The produced description.</p>
	 */
	public function produceDescription($context);
}
