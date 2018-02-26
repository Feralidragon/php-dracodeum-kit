<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Utilities\Text\Exceptions;

/**
 * Text utility <code>stringify</code> method unsupported value type exception class.
 * 
 * This exception is thrown from the text utility <code>stringify</code> method whenever a given value type is unsupported.
 * 
 * @since 1.0.0
 * @property-read mixed $value <p>The value.</p>
 * @property-read string $type <p>The value type.</p>
 */
class StringifyUnsupportedValueType extends Stringify
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Unsupported value type {{type}}.";
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function buildProperties() : void
	{
		$this->addProperty('value')->setAsRequired();
		$this->addProperty('type')->setAsString()->setAsRequired();
	}
}
