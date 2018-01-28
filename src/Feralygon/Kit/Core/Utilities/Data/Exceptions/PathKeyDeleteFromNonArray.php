<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Utilities\Data\Exceptions;

use Feralygon\Kit\Core\Utilities\Data\Exception;
use Feralygon\Kit\Core\Utilities\Type as UType;

/**
 * Core data utility path key delete from non-array exception class.
 * 
 * This exception is thrown from the data utility whenever there is an attempt to delete a given path key 
 * from a non-array value.
 * 
 * @since 1.0.0
 * @property-read string $path <p>The path.</p>
 * @property-read string $key <p>The key.</p>
 * @property-read mixed $value <p>The value.</p>
 */
class PathKeyDeleteFromNonArray extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Cannot delete key {{key}} using the path {{path}} since a non-array value was found as {{value}}.";
	}
	
	
	
	//Implemented public static methods
	/** {@inheritdoc} */
	public static function getRequiredPropertyNames() : array
	{
		return ['path', 'key', 'value'];
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function evaluateProperty(string $name, &$value) : ?bool
	{
		switch ($name) {
			case 'path':
				//no break
			case 'key':
				return UType::evaluateString($value);
			case 'value':
				return true;
		}
		return null;
	}
}
