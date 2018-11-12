<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Component\Traits;

/**
 * This trait defines a method to produce a proxy in a component.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Component
 */
trait ProxyProducer
{
	//Protected methods
	/**
	 * Produce proxy.
	 * 
	 * @since 1.0.0
	 * @return \Feralygon\Kit\Component\Proxy|string|null
	 * <p>The produced proxy instance or class or <code>null</code> if none was produced.</p>
	 */
	protected function produceProxy()
	{
		return null;
	}
}
