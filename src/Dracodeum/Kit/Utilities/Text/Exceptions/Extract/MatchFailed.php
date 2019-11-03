<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Utilities\Text\Exceptions\Extract;

use Dracodeum\Kit\Utilities\Text\Exceptions\Extract as Exception;

/**
 * This exception is thrown from the text utility <code>extract</code> method whenever a given string fails 
 * to match against a given mask.
 * 
 * @property-read string $string [coercive]
 * <p>The string.</p>
 * @property-read string $mask [coercive]
 * <p>The mask.</p>
 */
class MatchFailed extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		return "Match failed with string {{string}} against mask {{mask}}.";
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Exception\Traits\PropertiesLoader)
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		$this->addProperty('string')->setAsString();
		$this->addProperty('mask')->setAsString();
	}
}