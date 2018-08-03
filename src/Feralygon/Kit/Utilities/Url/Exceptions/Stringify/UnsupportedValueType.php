<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Utilities\Url\Exceptions\Stringify;

use Feralygon\Kit\Utilities\Url\Exceptions\Stringify as Exception;

/**
 * This exception is thrown from the URL utility <code>stringify</code> method whenever a given value type 
 * is unsupported.
 * 
 * @since 1.0.0
 * @property-read mixed $value
 * <p>The value.</p>
 * @property-read string $type [default = auto]
 * <p>The type.</p>
 */
class UnsupportedValueType extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		return "Unsupported value type {{type}} given as {{value}}.";
	}
	
	
	
	//Implemented protected methods (Feralygon\Kit\Exception\Traits\PropertiesLoader)
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		$this->addProperty('value');
		$this->addProperty('type')
			->setAsString(true)
			->setDefaultGetter(function () {
				return gettype($this->get('value'));
			})
		;
	}
}
