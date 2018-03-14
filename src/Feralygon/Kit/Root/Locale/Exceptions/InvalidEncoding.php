<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Root\Locale\Exceptions;

use Feralygon\Kit\Root\Locale\Exception;
use Feralygon\Kit\Utilities\{
	Text as UText,
	Type as UType
};

/**
 * This exception is thrown from the locale whenever a given encoding is invalid.
 * 
 * @since 1.0.0
 * @property-read string $encoding
 * <p>The encoding.</p>
 * @property-read string[] $encodings [default = []]
 * <p>The allowed encodings.<br>
 * They cannot be empty.</p>
 */
class InvalidEncoding extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		$message = "Invalid encoding {{encoding}}.";
		if (!empty($this->get('encodings'))) {
			$message .= "\nHINT: Only the following encodings are allowed: {{encodings}}.";
		}
		return $message;
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function buildProperties() : void
	{
		$this->addProperty('encoding')->setAsString()->setAsRequired();
		$this->addProperty('encodings')
			->setAsArray(function (&$key, &$value) : bool {
				return UType::evaluateString($value, true);
			}, true)
			->setDefaultValue([])
		;
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function getPlaceholderValueString(string $placeholder, $value) : string
	{
		if ($placeholder === 'encodings') {
			return UText::stringify($value, null, [
				'quote_strings' => true,
				'non_assoc_mode' => UText::STRING_NONASSOC_MODE_COMMA_LIST_AND
			]);
		}
		return parent::getPlaceholderValueString($placeholder, $value);
	}
}
