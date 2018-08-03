<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Prototype\Traits;

/**
 * This trait defines a method to load required property names in a prototype.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Prototype
 */
trait RequiredPropertyNamesLoader
{
	//Protected methods
	/**
	 * Load required property names.
	 * 
	 * All the required property names loaded here must be given during instantiation.
	 * 
	 * @since 1.0.0
	 * @return void
	 */
	protected function loadRequiredPropertyNames(): void {}
}
