<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Prototypes\Type\Interfaces;

interface InformationProducer
{
	//Public methods
	/**
	 * Produce label.
	 * 
	 * @param enum<\Dracodeum\Kit\Components\Type\Enumerations\Context> $context
	 * The context to produce for.
	 * 
	 * @return coercible:text
	 * The produced label.
	 */
	public function produceLabel($context);
	
	/**
	 * Produce description.
	 * 
	 * @param enum<\Dracodeum\Kit\Components\Type\Enumerations\Context> $context
	 * The context to produce for.
	 * 
	 * @return coercible:text
	 * The produced description.
	 */
	public function produceDescription($context);
}
