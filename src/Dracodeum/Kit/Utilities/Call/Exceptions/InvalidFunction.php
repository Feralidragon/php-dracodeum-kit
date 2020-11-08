<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Utilities\Call\Exceptions;

use Dracodeum\Kit\Utilities\Call\Exception;

/**
 * @property-read mixed $function
 * <p>The function.</p>
 */
class InvalidFunction extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		return "Invalid function {{function}}.\n" . 
			"HINT: Only the following types and formats are considered valid functions:\n" . 
			" - a callable;\n" . 
			" - an array with exactly 2 elements, with the first element being an interface name, " . 
			"class name or instance, and the second element being a method name, such as \"['Class', 'method']\";\n" . 
			" - a string as a function or method name, with a method being composed of an interface or " . 
			"class name and method name, delimited by \"::\" or \"->\", such as \"Class::method\".\n" . 
			"\n" . 
			"All types of methods are considered valid, regardless of their visibility, " . 
			"including protected and private ones.";
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Exception\Traits\PropertiesLoader)
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		$this->addProperty('function');
	}
}
