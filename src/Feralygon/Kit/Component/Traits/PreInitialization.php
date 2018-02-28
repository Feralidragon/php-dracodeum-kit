<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Component\Traits;

/**
 * Component pre-initialization trait.
 * 
 * This trait defines a method to pre-initialize a component.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Component
 */
trait PreInitialization
{
	//Protected methods
	/**
	 * Pre-initialize.
	 * 
	 * This method is called before any other initialization, but after the prototype initialization.
	 * 
	 * @since 1.0.0
	 * @return void
	 */
	protected function preInitialize() : void {}
}
