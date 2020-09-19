<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Component\Traits;

/** This trait defines a method to pre-initialize a component. */
trait PreInitializer
{
	//Protected methods
	/**
	 * Pre-initialize.
	 * 
	 * @param \Dracodeum\Kit\Prototype|string|null $prototype [reference]
	 * <p>The prototype instance, class or name to pre-initialize with.</p>
	 * @param array $properties [reference]
	 * <p>The properties to pre-initialize with, as a set of <samp>name => value</samp> pairs.</p>
	 * @return void
	 */
	protected function preInitialize(&$prototype, array &$properties): void {}
}
