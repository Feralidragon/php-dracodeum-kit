<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Prototype\Interfaces;

/**
 * Core prototype initialization interface.
 * 
 * This interface defines a method to initialize a prototype.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Core\Prototype
 */
interface Initialization
{
	//Public methods
	/**
	 * Initialize.
	 * 
	 * @since 1.0.0
	 * @return void
	 */
	public function initialize() : void;
}
