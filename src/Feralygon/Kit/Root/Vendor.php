<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Root;

use Feralygon\Kit\Traits as KitTraits;

/**
 * This class represents the vendor package and is used to set up how the overall package is meant to work and be used, 
 * such as setting the package to work as just a library instead of the main application framework.
 * 
 * @since 1.0.0
 */
final class Vendor
{
	//Traits
	use KitTraits\NonInstantiable;
	
	
	
	//Private static properties
	/** @var bool */
	private static $library = false;
	
	
	
	//Final public static methods
	/**
	 * Use as library.
	 * 
	 * When set to be used as a library, the package will only work as so, thus it won't modify any global PHP settings 
	 * (such as ones through <code>ini_set</code> calls) which might be used and set by other scripts, frameworks 
	 * or any other systems being used instead.
	 * 
	 * @since 1.0.0
	 * @return void
	 */
	final public static function useAsLibrary() : void
	{
		self::$library = true;
	}
	
	/**
	 * Check if is library.
	 * 
	 * @since 1.0.0
	 * @return bool <p>Boolean <code>true</code> if is a library.</p>
	 */
	final public static function isLibrary() : bool
	{
		return self::$library;
	}
}
