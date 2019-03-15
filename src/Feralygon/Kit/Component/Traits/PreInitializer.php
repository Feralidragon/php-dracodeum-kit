<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Component\Traits;

/**
 * This trait defines a method to pre-initialize a component.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Component
 */
trait PreInitializer
{
	//Protected methods
	/**
	 * Pre-initialize.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Prototype|string|null $prototype [reference]
	 * <p>The prototype instance, class or name to pre-initialize with.</p>
	 * @param array $properties [reference]
	 * <p>The properties to pre-initialize with, as <samp>name => value</samp> pairs.</p>
	 * @return void
	 */
	protected function preInitialize(&$prototype, array &$properties): void {}
}
