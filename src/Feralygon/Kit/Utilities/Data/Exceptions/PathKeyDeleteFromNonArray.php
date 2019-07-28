<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Utilities\Data\Exceptions;

use Feralygon\Kit\Utilities\Data\Exception;

/**
 * This exception is thrown from the data utility whenever there is an attempt to delete a given path key 
 * from a non-array value.
 * 
 * @property-read array $array [coercive]
 * <p>The array.</p>
 * @property-read string $path [coercive]
 * <p>The path.</p>
 * @property-read string $key [coercive]
 * <p>The key.</p>
 * @property-read mixed $value
 * <p>The value.</p>
 */
class PathKeyDeleteFromNonArray extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		return "Cannot delete key {{key}} using the path {{path}} " . 
			"since a non-array value was found as {{value}} in {{array}}.";
	}
	
	
	
	//Implemented protected methods (Feralygon\Kit\Exception\Traits\PropertiesLoader)
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		$this->addProperty('array')->setAsArray();
		$this->addProperty('path')->setAsString();
		$this->addProperty('key')->setAsString();
		$this->addProperty('value');
	}
}
