<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Traits\Properties\Exceptions;

use Feralygon\Kit\Core\Traits\Properties\Exception;
use Feralygon\Kit\Core\Utilities\{
	Text as UText,
	Type as UType
};

/**
 * Core properties trait invalid properties mode exception class.
 * 
 * This exception is thrown from an object using the properties trait whenever a given mode is invalid.
 * 
 * @since 1.0.0
 * @property-read object $object <p>The object.</p>
 * @property-read mixed $mode <p>The mode.</p>
 * @property-read string[] $modes [default = []] <p>The allowed modes.</p>
 */
class InvalidPropertiesMode extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		$message = "Invalid properties mode {{mode}} in object {{object}}.";
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
		return ['object', 'mode'];
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function evaluateProperty(string $name, &$value) : ?bool
	{
		switch ($name) {
			case 'object':
				return is_object($value);
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
