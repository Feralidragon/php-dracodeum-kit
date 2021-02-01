<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Component\Traits;

trait ProxyClass
{
	//Protected static methods
	/**
	 * Get proxy class.
	 * 
	 * @return string|null
	 * The proxy class, or `null` if none is set.
	 */
	protected static function getProxyClass(): ?string
	{
		return null;
	}
}
