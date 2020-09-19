<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Prototypes\Input\Interfaces;

use Dracodeum\Kit\Components\Input\Components\Modifier;

/** This interface defines a method to build modifier instances in an input prototype. */
interface ModifierBuilder
{
	//Public methods
	/**
	 * Build modifier instance for a given name with a given set of properties.
	 * 
	 * @param string $name
	 * <p>The name to build for.</p>
	 * @param array $properties
	 * <p>The properties to build with, as a set of <samp>name => value</samp> pairs.<br>
	 * Required properties may also be given as an array of values (<samp>[value1, value2, ...]</samp>), 
	 * in the same order as how these properties were first declared.</p>
	 * @return \Dracodeum\Kit\Components\Input\Components\Modifier|null
	 * <p>The built modifier instance for the given name with the given set of properties 
	 * or <code>null</code> if none was built.</p>
	 */
	public function buildModifier(string $name, array $properties): ?Modifier;
}
