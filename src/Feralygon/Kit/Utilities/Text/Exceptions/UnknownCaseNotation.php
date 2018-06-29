<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Utilities\Text\Exceptions;

use Feralygon\Kit\Utilities\Text\Exception;

/**
 * This exception is thrown from the text utility whenever the case notation of a given string is unknown.
 * 
 * @since 1.0.0
 * @property-read string $string
 * <p>The string.</p>
 */
class UnknownCaseNotation extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		return "Unknown case notation of string {{string}}.";
	}
	
	
	
	//Implemented protected methods (Feralygon\Kit\Exception\Traits\Properties)
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		$this->addProperty('string')->setAsString();
	}
}
