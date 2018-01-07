<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Traits\Functions\Exceptions;

use Feralygon\Kit\Core\Traits\Functions\Exception;
use Feralygon\Kit\Core\Utilities\{
	Call as UCall,
	Type as UType
};

/**
 * Core functions trait invalid function signature exception class.
 * 
 * This exception is thrown from an object using the functions trait whenever the signature from a given function is invalid.
 * 
 * @since 1.0.0
 * @property-read string $name <p>The function name.</p>
 * @property-read \Closure $function <p>The function.</p>
 * @property-read \Closure $template <p>The function template.</p>
 * @property-read string $signature <p>The function signature.</p>
 * @property-read string $template_signature <p>The function template signature.</p>
 */
class InvalidFunctionSignature extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Invalid signature {{signature}} for function {{name}} in object {{object}}.\n" . 
			"HINT: Only the following signature is allowed: {{template_signature}}.";
	}
	
	
	
	//Overridden public static methods
	/** {@inheritdoc} */
	public static function getRequiredPropertyNames() : array
	{
		return array_merge(parent::getRequiredPropertyNames(), ['name', 'function', 'template', 'signature', 'template_signature']);
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function evaluateProperty(string $name, &$value) : ?bool
	{
		switch ($name) {
			case 'name':
				return UType::evaluateString($value);
			case 'function':
				//no break
			case 'template':
				return UCall::evaluate($value);
			case 'signature':
				//no break
			case 'template_signature':
				return UType::evaluateString($value);
		}
		return parent::evaluateProperty($name, $value);
	}
}
