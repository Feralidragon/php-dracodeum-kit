<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Utilities\Text\Exceptions;

/**
 * Core text utility <code>mparse</code> method invalid delimiter pattern exception class.
 * 
 * This exception is thrown from the text utility <code>mparse</code> method whenever a given delimiter pattern is invalid.
 * 
 * @since 1.0.0
 * @property-read string $pattern <p>The delimiter pattern.</p>
 */
class MparseInvalidDelimiterPattern extends Mparse
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Invalid delimiter pattern {{pattern}}.\n" . 
			"HINT: Only a valid regular expression is allowed.";
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function buildProperties() : void
	{
		$this->addProperty('pattern')->setAsString()->setAsRequired();
	}
}
