<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Utilities\Text\Exceptions;

use Feralygon\Kit\Core\Utilities\Text as UText;

/**
 * Core text utility mparse method invalid string exception class.
 * 
 * This exception is thrown from the text utility mparse method whenever a given string is invalid.
 * 
 * @since 1.0.0
 * @property-read mixed $string <p>The string.</p>
 */
class MparseInvalidString extends Mparse
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Invalid string {{string}}.";
	}
	
	
	
	//Implemented public static methods
	/** {@inheritdoc} */
	public static function getRequiredPropertyNames() : array
	{
		return ['string'];
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function evaluateProperty(string $name, &$value) : ?bool
	{
		switch ($name) {
			case 'string':
				return true;
		}
		return null;
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function getPlaceholderValueString(string $placeholder, $value) : string
	{
		if ($placeholder === 'string' && !is_string($value)) {
			return UText::stringify($value, null, ['prepend_type' => true]);
		}
		return parent::getPlaceholderValueString($placeholder, $value);
	}
}
