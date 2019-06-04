<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Utilities\Text\Exceptions;

use Feralygon\Kit\Utilities\Text\Exception;

/**
 * This exception is thrown from the text utility whenever a given placeholder is invalid.
 * 
 * @since 1.0.0
 * @property-read string $placeholder [coercive]
 * <p>The placeholder.</p>
 * @property-read string|null $string [coercive] [default = null]
 * <p>The string.</p>
 */
class InvalidPlaceholder extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		return $this->isset('string')
			? "Invalid placeholder {{placeholder}} in string {{string}}."
			: "Invalid placeholder {{placeholder}}.";
	}
	
	
	
	//Implemented protected methods (Feralygon\Kit\Exception\Traits\PropertiesLoader)
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		$this->addProperty('placeholder')->setAsString();
		$this->addProperty('string')->setAsString(false, true)->setDefaultValue(null);
	}
}
