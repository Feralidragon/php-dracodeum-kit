<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces;

/** This interface defines a method to get the name from an input modifier prototype. */
interface Name
{
	//Public methods
	/**
	 * Get name.
	 * 
	 * The returning name must be a canonical string, which uniquely identifies this modifier within an input.
	 * 
	 * @return string
	 * <p>The name.</p>
	 */
	public function getName(): string;
}
