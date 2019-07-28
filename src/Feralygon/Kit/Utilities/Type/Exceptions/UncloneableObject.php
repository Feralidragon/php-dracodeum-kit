<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Utilities\Type\Exceptions;

use Feralygon\Kit\Utilities\Type\Exception;

/**
 * This exception is thrown from the type utility whenever a given object is uncloneable.
 * 
 * @property-read object $object [strict]
 * <p>The object.</p>
 */
class UncloneableObject extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		return "Object {{object}} is uncloneable.";
	}
	
	
	
	//Implemented protected methods (Feralygon\Kit\Exception\Traits\PropertiesLoader)
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		$this->addProperty('object')->setAsStrictObject();
	}
}
