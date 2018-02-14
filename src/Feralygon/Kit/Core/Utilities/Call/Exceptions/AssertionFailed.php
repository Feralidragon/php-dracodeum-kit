<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Utilities\Call\Exceptions;

use Feralygon\Kit\Core\Utilities\Call\Exception;
use Feralygon\Kit\Core\Interfaces\Throwables\Assertion as IAssertion;
use Feralygon\Kit\Core\Utilities\Call as UCall;

/**
 * Core call utility assertion failed exception class.
 * 
 * This exception is thrown from the call utility whenever an assertion on the compatibility of a given function 
 * towards a given template has failed.
 * 
 * @since 1.0.0
 * @property-read string $name <p>The name.</p>
 * @property-read \Closure $function <p>The function.</p>
 * @property-read \Closure $template <p>The template.</p>
 * @property-read string $function_signature [readonly] [default = auto] <p>The function signature.<br>
 * It is automatically retrieved from the given <var>$function</var> property above.</p>
 * @property-read string $template_signature [readonly] [default = auto] <p>The template signature.<br>
 * It is automatically retrieved from the given <var>$template</var> property above.</p>
 * @property-read object|string|null $object_class [default = null] <p>The object or class.</p>
 */
class AssertionFailed extends Exception implements IAssertion
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
			$message = "Assertion {{name}} failed with function signature {{function_signature}}.";
		}
		
		//return
		return "{$message}\n" . 
			"HINT: Only a compatible signature with {{template_signature}} is allowed.";
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function buildProperties() : void
	{
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
		$this->addProperty('object_class')->setAsObjectClass(null, true)->setDefaultValue(null);
	}
}
