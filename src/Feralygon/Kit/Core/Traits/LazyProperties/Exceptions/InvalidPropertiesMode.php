<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Traits\LazyProperties\Exceptions;

use Feralygon\Kit\Core\Traits\LazyProperties\Exception;
use Feralygon\Kit\Core\Utilities\{
	Data as UData,
	Text as UText,
	Type as UType
};

/**
 * Core lazy properties trait invalid properties mode exception class.
 * 
 * This exception is thrown from an object using the lazy properties trait whenever a given mode is invalid.
 * 
 * @since 1.0.0
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
				"HINT: Only the following modes are allowed: {{modes}}.";
		}
		return $message;
	}
	
	
	
	//Overridden public static methods
	/** {@inheritdoc} */
	public static function getRequiredPropertyNames() : array
	{
		return array_merge(parent::getRequiredPropertyNames(), ['mode']);
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function getDefaultPropertyValue(string $name)
	{
		switch ($name) {
			case 'modes':
				return [];
		}
		return parent::getDefaultPropertyValue($name);
	}
	
	/** {@inheritdoc} */
	protected function evaluateProperty(string $name, &$value) : ?bool
	{
		switch ($name) {
			case 'mode':
				return true;
			case 'modes':
				return UData::evaluate($value, function (&$key, &$value) : bool {
					return UType::evaluateString($value, true);
				}, true);
		}
		return parent::evaluateProperty($name, $value);
	}
	
	/** {@inheritdoc} */
	protected function getPlaceholderValueString(string $placeholder, $value) : string
	{
		if ($placeholder === 'modes') {
			return UText::stringify($value, null, [
				'quote_strings' => true,
				'non_assoc_mode' => UText::STRING_NONASSOC_MODE_COMMA_LIST_AND
			]);
		}
		return parent::getPlaceholderValueString($placeholder, $value);
	}
}
