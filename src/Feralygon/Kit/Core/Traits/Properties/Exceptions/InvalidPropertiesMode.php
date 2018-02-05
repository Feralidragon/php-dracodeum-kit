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
 * @property-read string $mode <p>The mode.</p>
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
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function loadProperties() : void
	{
		//parent
		parent::loadProperties();
		
		//properties
		$this->addStringProperty('mode', true);
		$this->addArrayProperty('modes', false, function (&$key, &$value) : bool {
			return UType::evaluateString($value, true);
		}, true);
		
		//defaults
		$this->setPropertyDefaultValue('modes', []);
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
