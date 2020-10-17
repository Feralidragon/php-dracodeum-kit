<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Primitives\Vector\Exceptions;

use Dracodeum\Kit\Primitives\Vector\Exception;

/**
 * This exception is thrown from a vector whenever a given index is invalid.
 * 
 * @property-read int $index
 * <p>The index.</p>
 * @property-read int|null $max_index [default = null]
 * <p>The maximum allowed index.</p>
 */
class InvalidIndex extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		$message = "Invalid index {{index}} for vector {{vector}}.";
		if ($this->max_index !== null) {
			$message .= "\nHINT: Only up to {{max_index}} is allowed.";
		}
		return $message;
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		//parent
		parent::loadProperties();
		
		//properties
		$this->addProperty('index')->setAsStrictInteger(true);
		$this->addProperty('max_index')->setAsStrictInteger(true, null, true)->setDefaultValue(null);
	}
}
