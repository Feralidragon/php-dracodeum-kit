<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Managers\Properties\Objects\Property\Exceptions;

use Feralygon\Kit\Core\Managers\Properties\Objects\Property\Exception;
use Feralygon\Kit\Core\Utilities\{
	Text as UText,
	Type as UType
};

/**
 * Core properties manager property object invalid mode exception class.
 * 
 * This exception is thrown from a property object whenever a given mode is invalid.
 * 
 * @since 1.0.0
 * @property-read string $mode <p>The mode.</p>
 * @property-read string[] $modes <p>The allowed modes.</p>
 */
class InvalidMode extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		$message = "Invalid mode {{mode}} for property {{property}}.\n" . 
			"HINT: Only the following modes are allowed: {{modes}}.";
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function loadProperties() : void
	{
		//parent
		parent::loadProperties();
		
		//properties
		$this->addStringProperty('mode', true);
		$this->addArrayProperty('modes', true, function (&$key, &$value) : bool {
			return UType::evaluateString($value, true);
		}, true, true);
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
