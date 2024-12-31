<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Utilities\Text\Exceptions;

use Dracodeum\Kit\Utilities\Text\Exception;

/**
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
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Exception\Traits\PropertiesLoader)
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		$this->addProperty('string')->setAsString();
	}
}
