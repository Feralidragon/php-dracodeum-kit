<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Component\Traits;

use Feralygon\Kit\Prototype;

/**
 * Component default prototype trait.
 * 
 * This trait defines a method to build a default prototype in a component.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Component
 */
trait DefaultPrototype
{
	//Protected methods
	/**
	 * Build default prototype instance.
	 * 
	 * The returning prototype instance is used if no prototype is given during instantiation.<br>
	 * If none is built, the base prototype class is used instead.
	 * 
	 * @since 1.0.0
	 * @param array $properties [default = []] <p>The prototype properties to use, 
	 * as <samp>name => value</samp> pairs.</p>
	 * @return \Feralygon\Kit\Prototype|null <p>The built default prototype instance 
	 * or <code>null</code> if none was built.</p>
	 */
	protected function buildDefaultPrototype(array $properties = []) : ?Prototype
	{
		return null;
	}
}
