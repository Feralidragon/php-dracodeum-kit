<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Prototype\Interfaces;

use Feralygon\Kit\Core\Traits\ExtendedProperties\Objects\Property;

/**
 * Core prototype properties interface.
 * 
 * This interface defines a set of methods to build and retrieve properties from a prototype.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Core\Prototype
 */
interface Properties
{
	//Public methods
	/**
	 * Build property instance for a given name.
	 * 
	 * @since 1.0.0
	 * @param string $name <p>The property name to build for.</p>
	 * @return \Feralygon\Kit\Core\Traits\ExtendedProperties\Objects\Property|null <p>The built property instance for the given name or <samp>null</samp> if none was built.</p>
	 */
	public function buildProperty(string $name) : ?Property;
	
	
	
	//Public static methods
	/**
	 * Get required property names.
	 * 
	 * All the required properties returned here must be given during instantiation.
	 * 
	 * @since 1.0.0
	 * @return string[] <p>The required property names.</p>
	 */
	public static function getRequiredPropertyNames() : array;
}
