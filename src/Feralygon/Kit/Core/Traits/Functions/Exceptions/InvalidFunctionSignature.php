<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Traits\Functions\Exceptions;

use Feralygon\Kit\Core\Traits\Functions\Exception;

/**
 * Core functions trait invalid function signature exception class.
 * 
 * This exception is thrown from an object using the functions trait whenever the signature 
 * from a given function is invalid.
 * 
 * @since 1.0.0
 * @property-read string $name <p>The function name.</p>
 * @property-read \Closure $function <p>The function.</p>
 * @property-read \Closure $template <p>The template.</p>
 * @property-read string $function_signature <p>The function signature.</p>
 * @property-read string $template_signature <p>The template signature.</p>
 */
class InvalidFunctionSignature extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Invalid signature {{function_signature}} for function {{name}} in object {{object}}.\n" . 
			"HINT: Only the following signature is allowed: {{template_signature}}.";
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function loadProperties() : void
	{
		//parent
		parent::loadProperties();
		
		//properties
		$this->addStringProperty('name', true);
		$this->addCallableProperty('function', true);
		$this->addCallableProperty('template', true);
		$this->addStringProperty('function_signature', true, true);
		$this->addStringProperty('template_signature', true, true);
	}
}
