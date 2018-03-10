<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Root\System;

use Feralygon\Kit\Traits as KitTraits;

/**
 * This class is the base to be extended from when creating a system environment.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Root\System
 */
abstract class Environment
{
	//Traits
	use KitTraits\NoConstructor;
	
	
	
	//Abstract public methods
	/**
	 * Get name.
	 * 
	 * The returning name defines an unique canonical identifier for this environment, 
	 * to be used to select which configuration profile to use.
	 * 
	 * @since 1.0.0
	 * @return string <p>The name.</p>
	 */
	abstract public function getName() : string;
	
	/**
	 * Check if is debug.
	 * 
	 * In a debug environment, the system behaves in such a way so that code can be easily debugged, 
	 * by performing additional integrity checks during runtime, at the potential cost of lower performance 
	 * and a higher memory footprint.
	 * 
	 * @since 1.0.0
	 * @return bool <p>Boolean <code>true</code> if is debug.</p>
	 */
	abstract public function isDebug() : bool;
	
	
	
	//Abstract protected methods
	/**
	 * Initialize.
	 * 
	 * @since 1.0.0
	 * @return void
	 */
	abstract protected function initialize() : void;
}
