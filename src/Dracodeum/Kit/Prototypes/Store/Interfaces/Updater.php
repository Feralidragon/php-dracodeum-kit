<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Prototypes\Store\Interfaces;

/** This interface defines a method to update a resource in a store prototype. */
interface Updater
{
	//Public methods
	/**
	 * Update a resource with a given name, UID (unique identifier) and scope with a given set of values.
	 * 
	 * @param string $name
	 * <p>The name to update with.</p>
	 * @param mixed $uid
	 * <p>The UID (unique identifier) to update with.</p>
	 * @param array $values
	 * <p>The values to update with, as <samp>name => value</samp> pairs.</p>
	 * @param string|null $scope
	 * <p>The scope to update with.</p>
	 * @return array|null
	 * <p>The updated values of the resource with the given name, UID (unique identifier) and scope, 
	 * as <samp>name => value</samp> pairs, or <code>null</code> if the resource does not exist.</p>
	 */
	public function update(string $name, $uid, array $values, ?string $scope): ?array;
}
