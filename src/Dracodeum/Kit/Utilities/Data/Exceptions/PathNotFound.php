<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Utilities\Data\Exceptions;

use Dracodeum\Kit\Utilities\Data\Exception;

/**
 * @property-read array $array
 * <p>The array.</p>
 * @property-read string $path
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
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Exception\Traits\PropertiesLoader)
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		$this->addProperty('array')->setAsArray();
		$this->addProperty('path')->setAsString();
	}
}
