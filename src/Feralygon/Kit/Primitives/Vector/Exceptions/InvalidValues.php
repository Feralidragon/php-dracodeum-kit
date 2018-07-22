<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Primitives\Vector\Exceptions;

use Feralygon\Kit\Primitives\Vector\Exception;

/**
 * This exception is thrown from a vector whenever a given set of values is invalid.
 * 
 * @since 1.0.0
 * @property-read array $values
 * <p>The values.</p>
 */
class InvalidValues extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		return "Invalid values {{values}} for vector {{vector}}.\n" . 
			"HINT: Only a non-associative array is allowed.";
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		//parent
		parent::loadProperties();
		
		//properties
		$this->addProperty('values')->setAsStrictArray();
	}
}
