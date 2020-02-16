<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Prototypes\Store\Interfaces;

/** This interface defines a method to check if a resource exists in a store prototype. */
interface Checker
{
	//Public methods
	/**
	 * Check if a resource with a given name, UID (unique identifier) and scope exists.
	 * 
	 * @param string $name
	 * <p>The name to check with.</p>
	 * @param mixed $uid
	 * <p>The UID (unique identifier) to check with.</p>
	 * @param string|null $scope
	 * <p>The scope to check with.</p>
	 * @param bool $readonly
	 * <p>Perform the query as a read-only operation.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if a resource with the given name, UID (unique identifier) and scope exists.</p>
	 */
	public function exists(string $name, $uid, ?string $scope, bool $readonly): bool;
}
