<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Entity\Traits;

/** This trait defines a method to perform processing after an entity instance is deleted. */
trait PostDeleteProcessor
{
	//Protected static methods
	/**
	 * Perform processing after an instance with a given ID and set of scope values is deleted.
	 * 
	 * @param mixed $id
	 * <p>The ID to perform processing with.</p>
	 * @param array $scope_values
	 * <p>The scope values to perform processing with, as <samp>name => value</samp> pairs.</p>
	 * @return void
	 */
	protected static function processPostDelete($id, array $scope_values): void {}
}
