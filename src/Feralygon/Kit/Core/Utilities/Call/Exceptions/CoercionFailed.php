<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Utilities\Call\Exceptions;

use Feralygon\Kit\Core\Utilities\Call\Exception;
use Feralygon\Kit\Core\Interfaces\Throwables\Coercion as ICoercion;
use Feralygon\Kit\Core\Utilities\{
	Call as UCall,
	Type as UType
};

/**
 * Core call utility coercion failed exception class.
 * 
 * This exception is thrown from the call utility whenever the coercion has failed with a given value.
 * 
 * @since 1.0.0
 * @property-read mixed $value <p>The value.</p>
 * @property-read \Closure|null $template [default = null] <p>The template.</p>
 * @property-read string|null $template_signature [default = auto] <p>The template signature.<br>
 * If not set, it is automatically generated from the given <var>$template</var> property above.</p>
 */
class CoercionFailed extends Exception implements ICoercion
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Coercion failed with value {{value}}.\n" . 
			($this->isset('template_signature') ? "HINT: Only a callable with the following signature is allowed: {{template_signature}}." : "HINT: Only a callable is allowed.");
	}
	
	
	
	//Implemented public static methods
	/** {@inheritdoc} */
	public static function getRequiredPropertyNames() : array
	{
		return ['value'];
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function evaluateProperty(string $name, &$value) : ?bool
	{
		switch ($name) {
			case 'value':
				return true;
			case 'template':
				return UCall::evaluate($value, null, true);
			case 'template_signature':
				if (!isset($value) && $this->isset('template')) {
					$value = UCall::signature($this->get('template'));
					return true;
				}
				return UType::evaluateString($value, true);
		}
		return null;
	}
}
