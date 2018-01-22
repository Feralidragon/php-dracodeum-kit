<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Utilities\Text\Exceptions;

use Feralygon\Kit\Core\Utilities\{
	Text as UText,
	Type as UType
};

/**
 * Core text utility mparse method invalid field pattern exception class.
 * 
 * This exception is thrown from the text utility mparse method whenever a given field pattern is invalid.
 * 
 * @since 1.0.0
 * @property-read string $field <p>The field.</p>
 * @property-read mixed $pattern <p>The field pattern.</p>
 */
class MparseInvalidFieldPattern extends Mparse
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Invalid pattern {{pattern}} for field {{field}}.\n" . 
			"HINT: Only a valid regular expression is allowed.";
	}
	
	
	
	//Implemented public static methods
	/** {@inheritdoc} */
	public static function getRequiredPropertyNames() : array
	{
		return ['field', 'pattern'];
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function evaluateProperty(string $name, &$value) : ?bool
	{
		switch ($name) {
			case 'field':
				return UType::evaluateString($value);
			case 'pattern':
				return true;
		}
		return null;
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function getPlaceholderValueString(string $placeholder, $value) : string
	{
		if ($placeholder === 'pattern' && !is_string($value)) {
			return UText::stringify($value, null, ['prepend_type' => true]);
		}
		return parent::getPlaceholderValueString($placeholder, $value);
	}
}
