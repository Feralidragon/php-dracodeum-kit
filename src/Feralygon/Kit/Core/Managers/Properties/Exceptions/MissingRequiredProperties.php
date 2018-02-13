<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Managers\Properties\Exceptions;

use Feralygon\Kit\Core\Managers\Properties\Exception;
use Feralygon\Kit\Core\Utilities\{
	Text as UText,
	Type as UType
};

/**
 * Core properties manager missing required properties exception class.
 * 
 * This exception is thrown from a properties manager whenever required properties are missing.
 * 
 * @since 1.0.0
 * @property-read string[] $names <p>The property names.</p>
 */
class MissingRequiredProperties extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return count($this->get('names')) === 1
			? "Missing required property {{names}} for properties manager with owner {{manager.getOwner()}}."
			: "Missing required properties {{names}} for properties manager with owner {{manager.getOwner()}}.";
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function buildProperties() : void
	{
		//parent
		parent::buildProperties();
		
		//properties
		$this->addProperty('names')
			->setAsArray(function (&$key, &$value) : bool {
				return UType::evaluateString($value);
			}, true, true)
			->setAsRequired()
		;
	}
	
	/** {@inheritdoc} */
	protected function getPlaceholderValueString(string $placeholder, $value) : string
	{
		if ($placeholder === 'names') {
			return UText::stringify($value, null, [
				'quote_strings' => true,
				'non_assoc_mode' => UText::STRING_NONASSOC_MODE_COMMA_LIST_AND
			]);
		}
		return parent::getPlaceholderValueString($placeholder, $value);
	}
}
