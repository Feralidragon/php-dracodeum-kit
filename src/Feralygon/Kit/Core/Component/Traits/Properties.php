<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Component\Traits;

use Feralygon\Kit\Core\Traits\LazyProperties\Objects\Property;

/**
 * Core component properties trait.
 * 
 * This trait defines a set of methods to build and retrieve properties from a component.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Core\Component
 */
trait Properties
{
	//Public static methods
	/**
	 * Get required property names.
	 * 
	 * All the required properties returned here must be given during instantiation.
	 * 
	 * @since 1.0.0
	 * @return string[] <p>The required property names.</p>
	 */
	public static function getRequiredPropertyNames() : array
	{
		return [];
	}
	
	
	
	//Protected methods
	/**
	 * Build property instance for a given name.
	 * 
	 * @since 1.0.0
	 * @param string $name <p>The property name to build for.</p>
	 * @return \Feralygon\Kit\Core\Traits\LazyProperties\Objects\Property|null 
	 * <p>The built property instance for the given name or <code>null</code> if none was built.</p>
	 */
	protected function buildProperty(string $name) : ?Property
	{
		return null;
	}
}
