<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Prototypes\Input\Interfaces;

use Feralygon\Kit\Core\Components\Input\Components\Modifier;

/**
 * Core input prototype modifiers interface.
 * 
 * This interface defines a method to build modifiers for an input prototype.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Core\Prototypes\Input
 */
interface Modifiers
{
	//Public methods
	/**
	 * Build modifier instance for a given name.
	 * 
	 * @since 1.0.0
	 * @param string $name <p>The modifier name to build for.</p>
	 * @param array $prototype_properties [default = []] <p>The modifier prototype properties to use, as <samp>name => value</samp> pairs.</p>
	 * @param array $properties [default = []] <p>The modifier properties to use, as <samp>name => value</samp> pairs.</p>
	 * @return \Feralygon\Kit\Core\Components\Input\Components\Modifier|null <p>The built modifier instance for the given name or <code>null</code> if none was built.</p>
	 */
	public function buildModifier(string $name, array $prototype_properties = [], array $properties = []) : ?Modifier;
}
