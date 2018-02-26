<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Managers\Properties\Exceptions;

use Feralygon\Kit\Managers\Properties\Exception;
use Feralygon\Kit\Utilities\{
	Text as UText,
	Type as UType
};

/**
 * Properties manager invalid mode exception class.
 * 
 * This exception is thrown from a properties manager whenever a given mode is invalid.
 * 
 * @since 1.0.0
 * @property-read string $mode <p>The mode.</p>
 * @property-read string[] $modes <p>The allowed modes.<br>
 * They cannot be empty.</p>
 */
class InvalidMode extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Invalid mode {{mode}} for properties manager with owner {{manager.getOwner()}}.\n" . (
			count($this->get('modes')) === 1
				? "HINT: Only the following mode is allowed: {{modes}}."
				: "HINT: Only the following modes are allowed: {{modes}}."
		);
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function buildProperties() : void
	{
		//parent
		parent::buildProperties();
		
		//properties
		$this->addProperty('mode')->setAsString()->setAsRequired();
		$this->addProperty('modes')
			->setAsArray(function (&$key, &$value) : bool {
				return UType::evaluateString($value, true);
			}, true, true)
			->setAsRequired()
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
