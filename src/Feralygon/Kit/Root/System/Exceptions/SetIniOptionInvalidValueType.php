<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Root\System\Exceptions;

/**
 * This exception is thrown from the system <code>setIniOption</code> method whenever a given value type is invalid.
 * 
 * @since 1.0.0
 * @property-read string $name
 * <p>The name.</p>
 * @property-read mixed $value
 * <p>The value.</p>
 * @property-read string $type
 * <p>The value type.</p>
 */
class SetIniOptionInvalidValueType extends SetIniOption
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Invalid option value type {{type}} for {{name}}.\n" . 
			"HINT: Only a string, integer, float, boolean or null value is allowed.";
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function buildProperties() : void
	{
		$this->addProperty('name')->setAsString()->setAsRequired();
		$this->addProperty('value')->setAsRequired();
		$this->addProperty('type')->setAsString()->setAsRequired();
	}
}
