<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Utilities\Data\Exceptions;

use Feralygon\Kit\Utilities\Data\Exception;

/**
 * This exception is thrown from the data utility whenever there is an attempt to set a given path key 
 * into a non-array value.
 * 
 * @since 1.0.0
 * @property-read array $array
 * <p>The array.</p>
 * @property-read string $path
 * <p>The path.</p>
 * @property-read string $key
 * <p>The key.</p>
 * @property-read mixed $value
 * <p>The value.</p>
 */
class PathKeySetIntoNonArray extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Cannot set value at key {{key}} using the path {{path}} " . 
			"since a non-array value was found as {{value}} in {{array}}.";
	}
	
	
	
	//Implemented protected methods (Feralygon\Kit\Exception\Traits\Properties)
	/** {@inheritdoc} */
	protected function loadProperties() : void
	{
		$this->addProperty('array')->setAsArray();
		$this->addProperty('path')->setAsString();
		$this->addProperty('key')->setAsString();
		$this->addProperty('value');
	}
}
