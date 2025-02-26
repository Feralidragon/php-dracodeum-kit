<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components\Input\Exceptions;

use Dracodeum\Kit\Components\Input\Exception;
use Dracodeum\Kit\Interfaces\Throwables\Coercive as ICoercive;
use Dracodeum\Kit\Utilities\Text as UText;

/**
 * @property-read mixed $value
 * <p>The value.</p>
 * @property-read string $error_message
 * <p>The error message.</p>
 */
class ValueCoercionFailed extends Exception implements ICoercive
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		return "Value coercion failed with value {{value}} using component {{component}} " . 
			"(with prototype {{prototype}}), with the following error: {{error_message}}";
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Interfaces\Throwables\Coercive)
	/** {@inheritdoc} */
	public function getValue()
	{
		return $this->value;
	}
	
	/** {@inheritdoc} */
	public function getErrorCode(): ?string
	{
		return null;
	}
	
	/** {@inheritdoc} */
	public function getErrorMessage(): ?string
	{
		return $this->error_message;
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		//parent
		parent::loadProperties();
		
		//properties
		$this->addProperty('value');
		$this->addProperty('error_message')->setAsString();
	}
	
	/** {@inheritdoc} */
	protected function getPlaceholderValueString(string $placeholder, $value): string
	{
		if ($placeholder === 'error_message') {
			return UText::formatMessage($value, true);
		}
		return parent::getPlaceholderValueString($placeholder, $value);
	}
}
