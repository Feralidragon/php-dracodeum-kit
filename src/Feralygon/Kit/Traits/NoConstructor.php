<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Traits;

/**
 * This trait removes the ability to implement a custom constructor for a class.
 * 
 * @since 1.0.0
 */
trait NoConstructor
{
	//Final public magic methods
	/**
	 * Prevent class from implementing its own constructor.
	 * 
	 * @since 1.0.0
	 */
	final public function __construct() {}
}
