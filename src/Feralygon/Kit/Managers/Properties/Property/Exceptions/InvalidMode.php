<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Managers\Properties\Property\Exceptions;

use Feralygon\Kit\Managers\Properties\Property\Exception;
use Feralygon\Kit\Utilities\{
	Text as UText,
	Type as UType
};

/**
 * This exception is thrown from a property whenever a given mode is invalid.
 * 
 * @since 1.0.0
 * @property-read string $mode
 * <p>The mode.</p>
 * @property-read string[] $modes
 * <p>The allowed modes.<br>
 * They cannot be empty.</p>
 */
class InvalidMode extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Invalid mode {{mode}} for property {{property.getName()}} in properties manager " . 
			"with owner {{property.getManager().getOwner()}}.\n" . (
			count($this->get('modes')) === 1
				? "HINT: Only the following mode is allowed: {{modes}}."
				: "HINT: Only the following modes are allowed: {{modes}}."
		);
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function loadProperties() : void
	{
		//parent
		parent::loadProperties();
		
		//properties
		$this->addProperty('mode')->setAsString();
		$this->addProperty('modes')
			->setAsArray(function (&$key, &$value) : bool {
				return UType::evaluateString($value, true);
			}, true, true)
		;
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
