<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Utilities\Text\Exceptions\Stringify;

use Dracodeum\Kit\Utilities\Text\Exceptions\Stringify as Exception;

/**
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
		return "Unsupported value type {{type}}.";
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Exception\Traits\PropertiesLoader)
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		$this->addProperty('value');
		$this->addProperty('type')
			->setAsString(true)
			->setDefaultGetter(function () {
				return gettype($this->value);
			})
		;
	}
}
