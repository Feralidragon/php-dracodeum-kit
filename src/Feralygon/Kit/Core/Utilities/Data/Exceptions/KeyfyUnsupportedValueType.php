<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Utilities\Data\Exceptions;

/**
 * Core data utility <code>keyfy</code> method unsupported value type exception class.
 * 
 * This exception is thrown from the data utility <code>keyfy</code> method whenever a given value type is unsupported.
 * 
 * @since 1.0.0
 * @property-read mixed $value <p>The value.</p>
 * @property-read string $type <p>The value type.</p>
 */
class KeyfyUnsupportedValueType extends Keyfy
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Unsupported value type {{type}}.";
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function loadProperties() : void
	{
		$this->addMixedProperty('value', true);
		$this->addStringProperty('type', true, true);
	}
}
