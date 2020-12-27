<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Prototypes\Type;

/** This interface defines a contract as a method to be implemented by any component set to use a type prototype. */
interface Contract
{
	//Public methods
	/**
	 * Get context.
	 * 
	 * @return enum:value(Dracodeum\Kit\Components\Type\Enumerations\Context)
	 * <p>The context.</p>
	 */
	public function getContext();
}
