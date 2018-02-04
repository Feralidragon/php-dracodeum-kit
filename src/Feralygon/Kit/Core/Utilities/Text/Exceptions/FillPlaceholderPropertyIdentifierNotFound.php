<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Utilities\Text\Exceptions;

use Feralygon\Kit\Core\Utilities\Type as UType;

/**
 * Core text utility <code>fill</code> method placeholder property identifier not found exception class.
 * 
 * This exception is thrown from the text utility <code>fill</code> method whenever a given placeholder property identifier 
 * is not found.
 * 
 * @since 1.0.0
 * @property-read string $placeholder <p>The placeholder.</p>
 * @property-read string $identifier <p>The property identifier.</p>
 */
class FillPlaceholderPropertyIdentifierNotFound extends Fill
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Placeholder {{placeholder}} property identifier {{identifier}} not found.";
	}
	
	
	
	//Implemented public static methods
	/** {@inheritdoc} */
	public static function getRequiredPropertyNames() : array
	{
		return ['placeholder', 'identifier'];
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function evaluateProperty(string $name, &$value) : ?bool
	{
		switch ($name) {
			case 'placeholder':
				//no break
			case 'identifier':
				return UType::evaluateString($value);
		}
		return null;
	}
}
