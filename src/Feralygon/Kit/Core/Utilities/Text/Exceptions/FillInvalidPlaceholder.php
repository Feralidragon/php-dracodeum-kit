<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Utilities\Text\Exceptions;

use Feralygon\Kit\Core\Utilities\Type as UType;

/**
 * Core text utility <code>fill</code> method invalid placeholder exception class.
 * 
 * This exception is thrown from the text utility <code>fill</code> method whenever a given placeholder is invalid.
 * 
 * @since 1.0.0
 * @property-read string $placeholder <p>The placeholder.</p>
 */
class FillInvalidPlaceholder extends Fill
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Invalid placeholder {{placeholder}}.";
	}
	
	
	
	//Implemented public static methods
	/** {@inheritdoc} */
	public static function getRequiredPropertyNames() : array
	{
		return ['placeholder'];
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function evaluateProperty(string $name, &$value) : ?bool
	{
		switch ($name) {
			case 'placeholder':
				return UType::evaluateString($value);
		}
		return null;
	}
}
