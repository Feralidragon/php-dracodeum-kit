<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Prototypes\Input\Prototypes\Modifier\Interfaces;

/**
 * Input modifier prototype name interface.
 * 
 * This interface defines a method to retrieve the name from an input modifier prototype.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Prototypes\Input\Prototypes\Modifier
 */
interface Name
{
	//Public methods
	/**
	 * Get name.
	 * 
	 * The returning name must be a canonical string, which uniquely identifies this modifier within an input.
	 * 
	 * @since 1.0.0
	 * @return string <p>The name.</p>
	 */
	public function getName() : string;
}
