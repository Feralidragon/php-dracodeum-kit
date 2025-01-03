<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Utilities\Call\Exceptions;

use Dracodeum\Kit\Utilities\Call\Exception;

/**
 * Call utility <code>halt</code> methods exception.
 * 
 * @property-read string $function_name
 * <p>The function or method name.</p>
 * @property-read object|string|null $object_class [default = null]
 * <p>The object or class.</p>
 * @property-read string|null $error_message [default = null]
 * <p>The error message.</p>
 * @property-read string|null $hint_message [default = null]
 * <p>The hint message.</p>
 */
abstract class Halt extends Exception
{
	//Implemented protected methods (Dracodeum\Kit\Exception\Traits\PropertiesLoader)
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		$this->addProperty('function_name')->setAsString();
		$this->addProperty('object_class')->setAsObjectClass(null, true)->setDefaultValue(null);
		$this->addProperty('error_message')->setAsString(false, true)->setDefaultValue(null);
		$this->addProperty('hint_message')->setAsString(false, true)->setDefaultValue(null);
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function getPlaceholderValueString(string $placeholder, $value): string
	{
		if (($placeholder === 'error_message' || $placeholder === 'hint_message') && is_string($value)) {
			return $value;
		}
		return parent::getPlaceholderValueString($placeholder, $value);
	}
}
