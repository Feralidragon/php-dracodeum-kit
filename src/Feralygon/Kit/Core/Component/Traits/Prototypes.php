<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Component\Traits;

use Feralygon\Kit\Core\Prototype;

/**
 * Core component prototypes trait.
 * 
 * This trait defines a method to build prototypes for a component.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Core\Component
 */
trait Prototypes
{
	//Protected methods
	/**
	 * Build prototype instance for a given name.
	 * 
	 * @since 1.0.0
	 * @param string $name <p>The prototype name to build for.</p>
	 * @param array $properties [default = []] <p>The prototype properties to use, as <samp>name => value</samp> pairs.</p>
	 * @return \Feralygon\Kit\Core\Prototype|null <p>The built prototype instance for the given name or <code>null</code> if none was built.</p>
	 */
	protected function buildPrototype(string $name, array $properties = []) : ?Prototype
	{
		return null;
	}
}
