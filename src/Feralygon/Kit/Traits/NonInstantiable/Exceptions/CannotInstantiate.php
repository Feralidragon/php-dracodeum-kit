<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Traits\NonInstantiable\Exceptions;

use Feralygon\Kit\Traits\NonInstantiable\Exception;

/**
 * This exception is thrown from a class using the non-instantiable trait whenever the instantiation 
 * of a class is attempted.
 * 
 * @since 1.0.0
 * @property-read string $class <p>The class.</p>
 */
class CannotInstantiate extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Cannot instantiate class {{class}}.";
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function buildProperties() : void
	{
		$this->addProperty('class')->setAsClass()->setAsRequired();
	}
}
