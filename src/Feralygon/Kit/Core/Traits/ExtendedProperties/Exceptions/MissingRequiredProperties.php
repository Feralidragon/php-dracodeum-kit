<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Traits\ExtendedProperties\Exceptions;

use Feralygon\Kit\Core\Traits\ExtendedProperties\Exception;
use Feralygon\Kit\Core\Utilities\{
	Text as UText,
	Type as UType
};

/**
 * Core extended properties trait missing required properties exception class.
 * 
 * This exception is thrown from an object using the extended properties trait whenever required properties are missing.
 * 
 * @since 1.0.0
 * @property-read object $object <p>The object.</p>
 * @property-read string[] $names <p>The property names.</p>
 */
class MissingRequiredProperties extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return count($this->get('names')) === 1
			? "Missing required property {{names}} for object {{object}}."
			: "Missing required properties {{names}} for object {{object}}.";
	}
	
	
	
	//Implemented public static methods
	/** {@inheritdoc} */
	public static function getRequiredPropertyNames() : array
	{
		return ['object', 'names'];
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function evaluateProperty(string $name, &$value) : ?bool
	{
		switch ($name) {
			case 'object':
				return is_object($value);
			case 'names':
				if (is_array($value) && !empty($value)) {
					foreach ($value as &$v) {
						if (!UType::evaluateString($v) || !UText::isIdentifier($v)) {
							return false;
						}
					}
					return true;
				}
				return false;
		}
		return null;
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function getPlaceholderValueString(string $placeholder, $value) : string
	{
		if ($placeholder === 'names') {
			return UText::stringify($value, null, ['flags' => UText::STRING_NONASSOC_CONJUNCTION_AND]);
		}
		return parent::getPlaceholderValueString($placeholder, $value);
	}
}
