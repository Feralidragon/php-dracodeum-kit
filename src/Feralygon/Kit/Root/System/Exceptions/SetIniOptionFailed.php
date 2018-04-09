<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Root\System\Exceptions;

/**
 * This exception is thrown from the system <code>setIniOption</code> method whenever it has failed.
 * 
 * @since 1.0.0
 * @property-read string $name
 * <p>The name.</p>
 * @property-read mixed $value
 * <p>The value.</p>
 */
class SetIniOptionFailed extends SetIniOption
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Option {{name}} has failed to be set as {{value}}.\n" . 
			"HINT: Only valid existing options can be set.";
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function loadProperties() : void
	{
		$this->addProperty('name')->setAsString()->setAsRequired();
		$this->addProperty('value')->setAsRequired();
	}
}
