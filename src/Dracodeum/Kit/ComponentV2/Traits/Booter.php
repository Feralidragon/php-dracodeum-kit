<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\ComponentV2\Traits;

trait Booter
{
	//Protected static methods
	/**
	 * Boot.
	 * 
	 * NOTE: Do **NOT** use `parent` to call the parent class version of this method when overridden in a subclass.  
	 *   
	 * During the booting process, all class versions of the `boot` method implementation are automatically called and 
	 * executed in an ordered and controlled fashion, and calling `parent::boot()` at any of them may lead to errors, 
	 * namely an exception being thrown.
	 */
	protected static function boot(): void {}
}
