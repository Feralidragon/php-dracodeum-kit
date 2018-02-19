<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Utilities\Data\Exceptions;

use Feralygon\Kit\Utilities\Data\Exception;
use Feralygon\Kit\Interfaces\Throwables\Coercive as ICoercive;
use Feralygon\Kit\Utilities\{
	Text as UText,
	Type as UType
};

/**
 * Data utility coercion failed exception class.
 * 
 * This exception is thrown from the data utility whenever the coercion has failed with a given value.
 * 
 * @since 1.0.0
 * @property-read mixed $value <p>The value.</p>
 * @property-read string|null $error_code [default = null] <p>The error code.</p>
 * @property-read string|null $error_message [default = null] <p>The error message.</p>
 */
class CoercionFailed extends Exception implements ICoercive
{
	//Public constants
	/** Null error code. */
	public const ERROR_CODE_NULL = 'NULL';
	
	/** Invalid type error code. */
	public const ERROR_CODE_INVALID_TYPE = 'INVALID_TYPE';
	
	/** Empty error code. */
	public const ERROR_CODE_EMPTY = 'EMPTY';
	
	/** Associative error code. */
	public const ERROR_CODE_ASSOCIATIVE = 'ASSOCIATIVE';
	
	/** Invalid element error code. */
	public const ERROR_CODE_INVALID_ELEMENT = 'INVALID_ELEMENT';
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return $this->isset('error_message')
			? "Coercion failed with value {{value}}, with the following error: {{error_message}}"
			: "Coercion failed with value {{value}}.";
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function buildProperties() : void
	{
		$this->addProperty('value')->setAsRequired();
		$this->addProperty('error_code')
			->setEvaluator(function (&$value) : bool {
				return !isset($value) || (UType::evaluateString($value) && in_array($value, [
					self::ERROR_CODE_NULL,
					self::ERROR_CODE_INVALID_TYPE,
					self::ERROR_CODE_EMPTY,
					self::ERROR_CODE_ASSOCIATIVE,
					self::ERROR_CODE_INVALID_ELEMENT
				], true));
			})
			->setDefaultValue(null)
		;
		$this->addProperty('error_message')->setAsString(false, true)->setDefaultValue(null);
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
		return $this->get('error_code');
	}
	
	/** {@inheritdoc} */
	public function getErrorMessage() : ?string
	{
		return $this->get('error_message');
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
