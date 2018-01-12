<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Prototype\Interfaces;

/**
 * Core prototype functions interface.
 * 
 * This interface defines a method to retrieve function templates from a prototype.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Core\Prototype
 */
interface Functions
{
	//Public methods
	/**
	 * Get function template for a given name.
	 * 
	 * @since 1.0.0
	 * @param string $name <p>The function name to get for.</p>
	 * @return callable|null <p>The function template for the given name or <code>null</code> if none exists.</p>
	 */
	public function getFunctionTemplate(string $name) : ?callable;
}
