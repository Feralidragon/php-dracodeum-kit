<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Components\Input\Exceptions;

use Feralygon\Kit\Components\Input\Exception;
use Feralygon\Kit\Interfaces\Throwables\Coercive as ICoercive;
use Feralygon\Kit\Utilities\Text as UText;

/**
 * Input component value coercion failed exception class.
 * 
 * This exception is thrown from an input whenever a value coercion has failed with a given value and prototype.
 * 
 * @since 1.0.0
 * @property-read mixed $value <p>The value.</p>
 * @property-read string $error_message <p>The error message.</p>
 */
class ValueCoercionFailed extends Exception implements ICoercive
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Value coercion failed with value {{value}} using component {{component}} " . 
			"(with prototype {{prototype}}), with the following error: {{error_message}}";
	}
	
	
	
	//Implemented public methods (coercive throwable interface)
	/** {@inheritdoc} */
	public function getValue()
	{
		return $this->get('value');
	}
	
	/** {@inheritdoc} */
	public function getErrorCode() : ?string
	{
		return null;
	}
	
	/** {@inheritdoc} */
	public function getErrorMessage() : ?string
	{
		return $this->get('error_message');
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function buildProperties() : void
	{
		//parent
		parent::buildProperties();
		
		//properties
		$this->addProperty('value')->setAsRequired();
		$this->addProperty('error_message')->setAsString()->setAsRequired();
	}
	
	/** {@inheritdoc} */
	protected function getPlaceholderValueString(string $placeholder, $value) : string
	{
		if ($placeholder === 'error_message') {
			return UText::uncapitalize($value, true);
		}
		return parent::getPlaceholderValueString($placeholder, $value);
	}
}