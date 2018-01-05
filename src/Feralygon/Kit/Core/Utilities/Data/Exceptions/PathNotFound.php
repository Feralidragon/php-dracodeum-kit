<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Utilities\Data\Exceptions;

use Feralygon\Kit\Core\Utilities\Data\Exception;
use Feralygon\Kit\Core\Utilities\Type as UType;

/**
 * Core data utility path not found exception class.
 * 
 * This exception is thrown from the data utility whenever a given path is not found.
 * 
 * @since 1.0.0
 * @property-read string $path <p>The path.</p>
 */
class PathNotFound extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Path {{path}} not found.";
	}
	
	
	
	//Implemented public static methods
	/** {@inheritdoc} */
	public static function getRequiredPropertyNames() : array
	{
		return ['path'];
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function evaluateProperty(string $name, &$value) : ?bool
	{
		switch ($name) {
			case 'path':
				return UType::evaluateString($value);
		}
		return null;
	}
}
