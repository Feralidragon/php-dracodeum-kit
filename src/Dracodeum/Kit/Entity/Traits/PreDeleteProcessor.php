<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Entity\Traits;

/** This trait defines a method to perform processing before an entity instance is deleted. */
trait PreDeleteProcessor
{
	//Protected static methods
	/**
	 * Perform processing before an instance with a given ID and set of scope values is deleted.
	 * 
	 * @param int|float|string|null $id
	 * <p>The ID to perform processing with.</p>
	 * @param array $scope_values
	 * <p>The scope values to perform processing with, as <samp>name => value</samp> pairs.</p>
	 * @return void
	 */
	protected static function processPreDelete($id, array $scope_values): void {}
}
