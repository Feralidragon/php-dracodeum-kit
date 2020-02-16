<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Prototypes\Store\Interfaces;

/** This interface defines a method to return a resource in a store prototype. */
interface Returner
{
	//Public methods
	/**
	 * Return a resource with a given name, UID (unique identifier) and scope.
	 * 
	 * @param string $name
	 * <p>The name to return with.</p>
	 * @param mixed $uid
	 * <p>The UID (unique identifier) to return with.</p>
	 * @param string|null $scope
	 * <p>The scope to return with.</p>
	 * @param bool $readonly
	 * <p>Perform the query as a read-only operation.</p>
	 * @return array|null
	 * <p>The resource with the given name, UID (unique identifier) and scope, as <samp>name => value</samp> pairs, 
	 * or <code>null</code> if none is set.</p>
	 */
	public function return(string $name, $uid, ?string $scope, bool $readonly): ?array;
}
