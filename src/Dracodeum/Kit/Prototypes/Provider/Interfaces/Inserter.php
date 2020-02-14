<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Prototypes\Provider\Interfaces;

/** This interface defines a method to insert a resource in a provider prototype. */
interface Inserter
{
	//Public methods
	/**
	 * Insert a resource with a given name, UID (unique identifier) and scope with a given set of values.
	 * 
	 * @param string $name
	 * <p>The name to insert with.</p>
	 * @param mixed $uid [reference]
	 * <p>The UID (unique identifier) to insert with.<br>
	 * It may be modified during insertion into a new one, such as when it is meant to be automatically generated.</p>
	 * @param array $values
	 * <p>The values to insert with, as <samp>name => value</samp> pairs.</p>
	 * @param string|null $scope
	 * <p>The scope to insert with.</p>
	 * @return array
	 * <p>The inserted values of the resource with the given name, UID (unique identifier) and scope, 
	 * as <samp>name => value</samp> pairs.</p>
	 */
	public function insert(string $name, &$uid, array $values, ?string $scope): array;
}
