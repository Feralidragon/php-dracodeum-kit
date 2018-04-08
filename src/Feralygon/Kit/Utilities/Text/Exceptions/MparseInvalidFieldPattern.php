<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Utilities\Text\Exceptions;

/**
 * This exception is thrown from the text utility <code>mparse</code> method whenever a given field pattern is invalid.
 * 
 * @since 1.0.0
 * @property-read string $field
 * <p>The field.</p>
 * @property-read mixed $pattern
 * <p>The pattern.</p>
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
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function buildProperties() : void
	{
		$this->addProperty('field')->setAsString()->setAsRequired();
		$this->addProperty('pattern')->setAsRequired();
	}
}
