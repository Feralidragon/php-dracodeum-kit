<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Utilities\Call\Exceptions;

use Feralygon\Kit\Core\Utilities\Call\Exception;
use Feralygon\Kit\Core\Interfaces\Throwables\Assertion as IAssertion;
use Feralygon\Kit\Core\Utilities\{
	Call as UCall,
	Type as UType
};

/**
 * Core call utility signature assertion failed exception class.
 * 
 * This exception is thrown from the call utility whenever the signature assertion between a given function 
 * and a given template has failed.
 * 
 * @since 1.0.0
 * @property-read string $name <p>The name.</p>
 * @property-read \Closure $function <p>The function.</p>
 * @property-read \Closure $template <p>The template.</p>
 * @property-read string $function_signature <p>The function signature.</p>
 * @property-read string $template_signature <p>The template signature.</p>
 * @property-read object|string|null $object_class [default = null] <p>The object or class.</p>
 */
class SignatureAssertionFailed extends Exception implements IAssertion
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		//message
		$message = "";
		if ($this->isset('object_class')) {
			$message = is_object($this->get('object_class'))
				? "Assertion {{name}} failed in object {{object_class}} with function signature {{function_signature}}."
				: "Assertion {{name}} failed in class {{object_class}} with function signature {{function_signature}}.";
		} else {
			$message = "Assertion failed with function signature {{function_signature}}.";
		}
		
		//return
		return "{$message}\n" . 
			"HINT: Only the following signature is allowed: {{template_signature}}.";
	}
	
	
	
	//Implemented public static methods
	/** {@inheritdoc} */
	public static function getRequiredPropertyNames() : array
	{
		return ['name', 'function', 'template', 'function_signature', 'template_signature'];
	}
	
	
	
	//Implemented protected methods
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
			case 'function_signature':
				//no break
			case 'template_signature':
				return UType::evaluateString($value);
			case 'object_class':
				return UType::evaluateObjectClass($value, null, true);
		}
		return null;
	}
}
