<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Prototypes\Provider\Interfaces;

/** This interface defines a method to delete a resource in a provider prototype. */
interface Deleter
{
	//Public methods
	/**
	 * Delete a resource with a given name, UID (unique identifier) and scope.
	 * 
	 * @param string $name
	 * <p>The name to delete with.</p>
	 * @param mixed $uid
	 * <p>The UID (unique identifier) to delete with.</p>
	 * @param string|null $scope
	 * <p>The scope to delete with.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the resource with the given name, UID (unique identifier) and scope was 
	 * deleted.</p>
	 */
	public function delete(string $name, $uid, ?string $scope): bool;
}
