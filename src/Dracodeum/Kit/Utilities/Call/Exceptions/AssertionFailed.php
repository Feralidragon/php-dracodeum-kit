<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Utilities\Call\Exceptions;

use Dracodeum\Kit\Utilities\Call\Exception;
use Dracodeum\Kit\Interfaces\Throwables\Assertive as IAssertive;
use Dracodeum\Kit\Utilities\Call as UCall;

/**
 * This exception is thrown from the call utility whenever an assertion on the compatibility of a given function 
 * towards a given template fails.
 * 
 * @property-read string $name [coercive]
 * <p>The name.</p>
 * @property-read callable|array|string $function [strict]
 * <p>The function.</p>
 * @property-read callable|array|string $template [strict]
 * <p>The template.</p>
 * @property-read string $function_signature [readonly] [default = auto]
 * <p>The function signature.<br>
 * It is automatically retrieved from the given <var>$function</var> property above.</p>
 * @property-read string $template_signature [readonly] [default = auto]
 * <p>The template signature.<br>
 * It is automatically retrieved from the given <var>$template</var> property above.</p>
 * @property-read object|string|null $source_object_class [coercive = object or class] [default = null]
 * <p>The source object or class.</p>
 * @property-read string|null $source_function_name [coercive] [default = null]
 * <p>The source function or method name.</p>
 */
class AssertionFailed extends Exception implements IAssertive
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		//message
		$message = "Assertion {{name}} failed with function signature {{function_signature}}.";
		if ($this->source_object_class !== null && $this->source_function_name !== null) {
			$message = is_object($this->source_object_class)
				? "Assertion {{name}} failed in method call {{source_function_name}} " . 
					"in object {{source_object_class}} with function signature {{function_signature}}."
				: "Assertion {{name}} failed in method call {{source_function_name}} " . 
					"in class {{source_object_class}} with function signature {{function_signature}}.";
		} elseif ($this->source_object_class !== null) {
			$message = is_object($this->source_object_class)
				? "Assertion {{name}} failed in object {{source_object_class}} " . 
					"with function signature {{function_signature}}."
				: "Assertion {{name}} failed in class {{source_object_class}} " . 
					"with function signature {{function_signature}}.";
		} elseif ($this->source_function_name !== null) {
			$message = "Assertion {{name}} failed in function call {{source_function_name}} " . 
				"with function signature {{function_signature}}.";
		}
		
		//return
		return "{$message}\nHINT: Only a compatible signature with {{template_signature}} is allowed.";
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Exception\Traits\PropertiesLoader)
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		$this->addProperty('name')->setAsString();
		$this->addProperty('function')
			->addEvaluator(function (&$value): bool {
				return UCall::validate($value, true);
			})
		;
		$this->addProperty('template')
			->addEvaluator(function (&$value): bool {
				return UCall::validate($value, true);
			})
		;
		$this->addProperty('function_signature')
			->setMode('r')
			->setGetter(function () {
				return UCall::signature($this->function);
			})
		;
		$this->addProperty('template_signature')
			->setMode('r')
			->setGetter(function () {
				return UCall::signature($this->template);
			})
		;
		$this->addProperty('source_object_class')->setAsObjectClass(null, true)->setDefaultValue(null);
		$this->addProperty('source_function_name')->setAsString(false, true)->setDefaultValue(null);
	}
}
