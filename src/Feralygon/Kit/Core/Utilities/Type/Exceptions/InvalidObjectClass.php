<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Utilities\Type\Exceptions;

use Feralygon\Kit\Core\Utilities\Type\Exception;
use Feralygon\Kit\Core\Utilities\Text as UText;

/**
 * Core type utility invalid object or class exception class.
 * 
 * This exception is thrown from the type utility whenever a given object or class is invalid.
 * 
 * @since 1.0.0
 * @property-read mixed $object_class <p>The object or class.</p>
 */
class InvalidObjectClass extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Invalid object or class {{object_class}}.";
	}
	
	
	
	//Implemented public static methods
	/** {@inheritdoc} */
	public static function getRequiredPropertyNames() : array
	{
		return ['object_class'];
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function evaluateProperty(string $name, &$value) : ?bool
	{
		switch ($name) {
			case 'object_class':
				return true;
		}
		return null;
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function getPlaceholderValueString(string $placeholder, $value) : string
	{
		if ($placeholder === 'object_class' && !is_string($value)) {
			return UText::stringify($value, null, ['prepend_type' => true]);
		}
		return parent::getPlaceholderValueString($placeholder, $value);
	}
}
