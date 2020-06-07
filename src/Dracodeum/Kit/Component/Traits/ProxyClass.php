<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Component\Traits;

/** This trait defines a method to get the proxy class from a component. */
trait ProxyClass
{
	//Protected static methods
	/**
	 * Get proxy class.
	 * 
	 * @return string|null
	 * <p>The proxy class or <code>null</code> if none is set.</p>
	 */
	protected static function getProxyClass(): ?string
	{
		return null;
	}
}
