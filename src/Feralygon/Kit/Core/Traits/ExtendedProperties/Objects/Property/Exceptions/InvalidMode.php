<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Traits\ExtendedProperties\Objects\Property\Exceptions;

use Feralygon\Kit\Core\Traits\ExtendedProperties\Objects\Property;
use Feralygon\Kit\Core\Traits\ExtendedProperties\Objects\Property\Exception;
use Feralygon\Kit\Core\Utilities\{
	Text as UText,
	Type as UType
};

/**
 * Core extended properties trait property object invalid mode exception class.
 * 
 * This exception is thrown from an extended properties trait property object whenever a given mode is invalid.
 * 
 * @since 1.0.0
 * @property-read \Feralygon\Kit\Core\Traits\ExtendedProperties\Objects\Property $property <p>The property instance.</p>
 * @property-read mixed $mode <p>The mode.</p>
 * @property-read string[] $modes [default = []] <p>The allowed modes.</p>
 */
class InvalidMode extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		$message = "Invalid mode {{mode}} for property {{property}}.";
		if (!empty($this->get('modes'))) {
			$message .= "\n" . 
				"HINT: Only one of the following modes is allowed: {{modes}}.";
		}
		return $message;
	}
	
	
	
	//Implemented public static methods
	/** {@inheritdoc} */
	public static function getRequiredPropertyNames() : array
	{
		return ['property', 'mode'];
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function evaluateProperty(string $name, &$value) : ?bool
	{
		switch ($name) {
			case 'property':
				return is_object($value) && UType::isA($value, Property::class);
			case 'mode':
				return true;
			case 'modes':
				$value = $value ?? [];
				if (is_array($value)) {
					foreach ($value as &$v) {
						if (!UType::evaluateString($v)) {
							return false;
						}
					}
					unset($v);
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
		if ($placeholder === 'modes') {
			return UText::stringify($value, null, ['flags' => UText::STRING_NONASSOC_CONJUNCTION_OR]);
		}
		return parent::getPlaceholderValueString($placeholder, $value);
	}
}
