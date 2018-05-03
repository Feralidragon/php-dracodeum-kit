<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Utilities\Type\Exceptions;

use Feralygon\Kit\Utilities\Type\Exception;
use Feralygon\Kit\Interfaces\Throwables\Coercive as ICoercive;
use Feralygon\Kit\Utilities\Text as UText;

/**
 * This exception is thrown from the type utility whenever the coercion into an object or class 
 * has failed with a given value.
 * 
 * @since 1.0.0
 * @property-read mixed $value
 * <p>The value.</p>
 * @property-read string|null $error_code [default = null]
 * <p>The error code.</p>
 * @property-read string|null $error_message [default = null]
 * <p>The error message.</p>
 */
class ObjectClassCoercionFailed extends Exception implements ICoercive
{
	//Public constants
	/** Null error code. */
	public const ERROR_CODE_NULL = 'NULL';
	
	/** Invalid class error code. */
	public const ERROR_CODE_INVALID_CLASS = 'INVALID_CLASS';
	
	/** Invalid error code. */
	public const ERROR_CODE_INVALID = 'INVALID';
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return $this->isset('error_message')
			? "Object or class coercion failed with value {{value}}, with the following error: {{error_message}}"
			: "Object or class coercion failed with value {{value}}.";
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Interfaces\Throwables\Coercive)
	/** {@inheritdoc} */
	public function getValue()
	{
		return $this->get('value');
	}
	
	/** {@inheritdoc} */
	public function getErrorCode() : ?string
	{
		return $this->get('error_code');
	}
	
	/** {@inheritdoc} */
	public function getErrorMessage() : ?string
	{
		return $this->get('error_message');
	}
	
	
	
	//Implemented protected methods (Feralygon\Kit\Exception\Traits\Properties)
	/** {@inheritdoc} */
	protected function loadProperties() : void
	{
		$this->addProperty('value')->setAsRequired();
		$this->addProperty('error_code')
			->setAsString(true, true)
			->addEvaluator(function (&$value) : bool {
				return !isset($value) || in_array($value, [
					self::ERROR_CODE_NULL,
					self::ERROR_CODE_INVALID_CLASS,
					self::ERROR_CODE_INVALID
				], true);
			})
			->setDefaultValue(null)
		;
		$this->addProperty('error_message')->setAsString(false, true)->setDefaultValue(null);
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function getPlaceholderValueString(string $placeholder, $value) : string
	{
		if ($placeholder === 'error_message' && is_string($value)) {
			return UText::uncapitalize($value, true);
		}
		return parent::getPlaceholderValueString($placeholder, $value);
	}
}
