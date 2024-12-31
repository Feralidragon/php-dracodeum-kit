<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Utilities\Type\Exceptions\Phpfy;

use Dracodeum\Kit\Utilities\Type\Exceptions\Phpfy as Exception;

/**
 * @property-read mixed $value
 * <p>The value.</p>
 * @property-read string $type [default = auto]
 * <p>The type.</p>
 * @property-read string|null $hint_message [default = null]
 * <p>The hint message.</p>
 */
class UnsupportedValueType extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		$message = "Unsupported value type {{type}} given as {{value}}.";
		if ($this->hint_message !== null) {
			$message .= "\nHINT: {{hint_message}}";
		}
		return $message;
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
		$this->addProperty('hint_message')->setAsString(false, true)->setDefaultValue(null);
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function getPlaceholderValueString(string $placeholder, $value): string
	{
		if ($placeholder === 'hint_message' && is_string($value)) {
			return $value;
		}
		return parent::getPlaceholderValueString($placeholder, $value);
	}
}
