<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Utilities\Call\Exceptions;

use Feralygon\Kit\Core\Utilities\Call\Exception;
use Feralygon\Kit\Core\Interfaces\Throwables\Assertion as IAssertion;

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
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function loadProperties() : void
	{
		$this->addStringProperty('name', true);
		$this->addCallableProperty('function', true);
		$this->addCallableProperty('template', true);
		$this->addStringProperty('function_signature', true);
		$this->addStringProperty('template_signature', true);
		$this->addObjectClassProperty('object_class', false, null, true);
	}
}
