<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Utilities\Data\Exceptions;

use Feralygon\Kit\Utilities\Data\Exception;

/**
 * This exception is thrown from the data utility whenever a given path is not found.
 * 
 * @property-read array $array [coercive]
 * <p>The array.</p>
 * @property-read string $path [coercive]
 * <p>The path.</p>
 */
class PathNotFound extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		return "Path {{path}} not found in {{array}}.";
	}
	
	
	
	//Implemented protected methods (Feralygon\Kit\Exception\Traits\PropertiesLoader)
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		$this->addProperty('array')->setAsArray();
		$this->addProperty('path')->setAsString();
	}
}
