<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core;

use Feralygon\Kit\Core\Traits\ExtendedLazyProperties\Objects\Property;

/**
 * Core structure class.
 * 
 * This class is the base to be extended from when creating a structure.<br>
 * <br>
 * A structure is a simple object which represents and stores multiple properties of multiple types.<br>
 * Each and every single one of its properties is lazy-loaded and is validated and sanitized, 
 * guaranteeing its type and integrity, and may be retrieved and modified directly just like any public object property.
 * 
 * @since 1.0.0
 * @see https://en.wikipedia.org/wiki/Struct_(C_programming_language)
 */
abstract class Structure implements \ArrayAccess
{
	//Traits
	use Traits\ExtendedLazyProperties\ArrayAccess;
	
	
	
	//Final public magic methods
	/**
	 * Instantiate class.
	 * 
	 * @since 1.0.0
	 * @param array $properties [default = []] <p>The properties, as <samp>name => value</samp> pairs.</p>
	 */
	final public function __construct(array $properties = [])
	{
		$this->initializeProperties(
			$properties, \Closure::fromCallable([$this, 'buildProperty']), $this->getRequiredPropertyNames()
		);
	}
	
	
	
	//Abstract public static methods
	/**
	 * Get required property names.
	 * 
	 * All the required properties returned here must be given during instantiation.
	 * 
	 * @since 1.0.0
	 * @return string[] <p>The required property names.</p>
	 */
	abstract public static function getRequiredPropertyNames() : array;
	
	
	
	//Abstract protected methods
	/**
	 * Build property instance for a given name.
	 * 
	 * @since 1.0.0
	 * @param string $name <p>The property name to build for.</p>
	 * @return \Feralygon\Kit\Core\Traits\ExtendedLazyProperties\Objects\Property|null 
	 * <p>The built property instance for the given name or <code>null</code> if none was built.</p>
	 */
	abstract protected function buildProperty(string $name) : ?Property;
}
