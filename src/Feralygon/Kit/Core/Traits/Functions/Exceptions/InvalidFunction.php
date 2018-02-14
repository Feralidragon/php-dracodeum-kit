<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Traits\Functions\Exceptions;

use Feralygon\Kit\Core\Traits\Functions\Exception;
use Feralygon\Kit\Core\Utilities\Call as UCall;

/**
 * Core functions trait invalid function exception class.
 * 
 * This exception is thrown from an object using the functions trait whenever a given function is invalid.
 * 
 * @since 1.0.0
 * @property-read string $name <p>The function name.</p>
 * @property-read \Closure $function <p>The function.</p>
 * @property-read \Closure $template <p>The template.</p>
 * @property-read string $function_signature [readonly] [default = auto] <p>The function signature.<br>
 * It is automatically retrieved from the given <var>$function</var> property above.</p>
 * @property-read string $template_signature [readonly] [default = auto] <p>The template signature.<br>
 * It is automatically retrieved from the given <var>$template</var> property above.</p>
 */
class InvalidFunction extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Invalid function {{name}} with signature {{function_signature}} in object {{object}}.\n" . 
			"HINT: Only a compatible signature with {{template_signature}} is allowed.";
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function buildProperties() : void
	{
		//parent
		parent::buildProperties();
		
		//properties
		$this->addProperty('name')->setAsString()->setAsRequired();
		$this->addProperty('function')->setAsCallable()->setAsRequired();
		$this->addProperty('template')->setAsCallable()->setAsRequired();
		$this->addProperty('function_signature')
			->setMode('r')
			->setAsString(true)
			->setDefaultGetter(function () {
				return UCall::signature($this->get('function'));
			})
		;
		$this->addProperty('template_signature')
			->setMode('r')
			->setAsString(true)
			->setDefaultGetter(function () {
				return UCall::signature($this->get('template'));
			})
		;
	}
}
