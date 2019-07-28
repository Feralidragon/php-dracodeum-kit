<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Utilities\Type\Exceptions;

use Feralygon\Kit\Utilities\Type\Exception;

/**
 * This exception is thrown from the type utility whenever a given class is non-instantiable.
 * 
 * @property-read string $class [coercive = class]
 * <p>The class.</p>
 */
class NonInstantiableClass extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		return "Class {{class}} is non-instantiable.";
	}
	
	
	
	//Implemented protected methods (Feralygon\Kit\Exception\Traits\PropertiesLoader)
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		$this->addProperty('class')->setAsClass();
	}
}
