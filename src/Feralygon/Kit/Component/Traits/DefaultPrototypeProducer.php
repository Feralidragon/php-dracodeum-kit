<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Component\Traits;

/** 
 * This trait defines a method to produce the default prototype in a component.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Component
 */
trait DefaultPrototypeProducer
{
	//Protected methods
	/**
	 * Produce default prototype.
	 * 
	 * The returning prototype is used if none is given during instantiation.<br>
	 * If none is produced, then the base prototype class is used instead.
	 * 
	 * @since 1.0.0
	 * @param array $properties [default = []]
	 * <p>The properties to produce with, as <samp>name => value</samp> pairs.</p>
	 * @return \Feralygon\Kit\Prototype|string|null
	 * <p>The produced default prototype instance or class, or <code>null</code> if none was produced.</p>
	 */
	protected function produceDefaultPrototype(array $properties = [])
	{
		return null;
	}
}
