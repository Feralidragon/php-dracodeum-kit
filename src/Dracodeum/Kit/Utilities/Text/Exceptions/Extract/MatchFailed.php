<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Utilities\Text\Exceptions\Extract;

use Dracodeum\Kit\Utilities\Text\Exceptions\Extract as Exception;

/**
 * @property-read string $string
 * <p>The string.</p>
 * @property-read string $mask
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
