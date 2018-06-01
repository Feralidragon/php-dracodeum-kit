<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Component\Traits;

use Feralygon\Kit\Traits\LazyProperties\Property;

/**
 * This trait defines a method to build properties in a component.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Component
 */
trait Properties
{
	//Protected methods
	/**
	 * Build property instance with a given name.
	 * 
	 * @since 1.0.0
	 * @param string $name
	 * <p>The name to build with.</p>
	 * @return \Feralygon\Kit\Traits\LazyProperties\Property|null
	 * <p>The built property instance with the given name or <code>null</code> if none was built.</p>
	 */
	protected function buildProperty(string $name) : ?Property
	{
		return null;
	}
}
