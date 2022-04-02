<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Utilities\Text\Exceptions;

use Dracodeum\Kit\Utilities\Text\Exception;

/**
 * @property-read string $placeholder
 * <p>The placeholder.</p>
 * @property-read string|null $string [default = null]
 * <p>The string.</p>
 */
class InvalidPlaceholder extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		return "Invalid placeholder {{placeholder}}" . ($this->string !== null ? " in string {{string}}." : ".");
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Exception\Traits\PropertiesLoader)
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		$this->addProperty('placeholder')->setAsString();
		$this->addProperty('string')->setAsString(false, true)->setDefaultValue(null);
	}
}
