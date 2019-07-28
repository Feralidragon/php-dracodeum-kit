<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Utilities\Call\Exceptions;

use Feralygon\Kit\Utilities\Call\Exception;

/**
 * Call utility <code>guard</code> methods exception.
 * 
 * @property-read string $function_name [coercive]
 * <p>The function or method name.</p>
 * @property-read object|string|null $object_class [coercive = object or class] [default = null]
 * <p>The object or class.</p>
 * @property-read string|null $error_message [coercive] [default = null]
 * <p>The error message.</p>
 * @property-read string|null $hint_message [coercive] [default = null]
 * <p>The hint message.</p>
 */
abstract class Guard extends Exception
{
	//Implemented protected methods (Feralygon\Kit\Exception\Traits\PropertiesLoader)
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
