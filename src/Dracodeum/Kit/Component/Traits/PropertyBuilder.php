<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Component\Traits;

use Dracodeum\Kit\Traits\LazyProperties\Property;

trait PropertyBuilder
{
	//Protected methods
	/**
	 * Build a property instance.
	 * 
	 * @param string $name
	 * The name to build with.
	 * 
	 * @return \Dracodeum\Kit\Traits\LazyProperties\Property|null
	 * The built property instance, or `null` if none was built.
	 */
	protected function buildProperty(string $name): ?Property
	{
		return null;
	}
}
