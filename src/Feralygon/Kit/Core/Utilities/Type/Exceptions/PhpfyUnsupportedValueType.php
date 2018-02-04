<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Utilities\Type\Exceptions;

use Feralygon\Kit\Core\Utilities\Type as UType;

/**
 * Core type utility <code>phpfy</code> method unsupported value type exception class.
 * 
 * This exception is thrown from the type utility <code>phpfy</code> method whenever a given value type is unsupported.
 * 
 * @since 1.0.0
 * @property-read mixed $value <p>The value.</p>
 * @property-read string $type <p>The value type.</p>
 */
class PhpfyUnsupportedValueType extends Phpfy
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Unsupported value type {{type}}.";
	}
	
	
	
	//Implemented public static methods
	/** {@inheritdoc} */
	public static function getRequiredPropertyNames() : array
	{
		return ['value', 'type'];
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function evaluateProperty(string $name, &$value) : ?bool
	{
		switch ($name) {
			case 'value':
				return true;
			case 'type':
				return UType::evaluateString($value, true);
		}
		return null;
	}
}
