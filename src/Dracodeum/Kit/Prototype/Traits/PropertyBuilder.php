<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Prototype\Traits;

use Dracodeum\Kit\Traits\LazyProperties\Property;

/** This trait defines a method to build properties in a prototype. */
trait PropertyBuilder
{
	//Protected methods
	/**
	 * Build property instance with a given name.
	 * 
	 * @param string $name
	 * <p>The name to build with.</p>
	 * @return \Dracodeum\Kit\Traits\LazyProperties\Property|null
	 * <p>The built property instance with the given name or <code>null</code> if none was built.</p>
	 */
	protected function buildProperty(string $name): ?Property
	{
		return null;
	}
}
