<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Component\Exceptions;

use Feralygon\Kit\Core\Component\Exception;
use Feralygon\Kit\Core\Interfaces\Throwables\Coercion as ICoercion;
use Feralygon\Kit\Core\Utilities\{
	Text as UText,
	Type as UType
};

/**
 * Core component coercion failed exception class.
 * 
 * This exception is thrown from a component whenever a coercion has failed with a given value.
 * 
 * @since 1.0.0
 * @property-read mixed $value <p>The value.</p>
 * @property-read string|null $error_code [default = null] <p>The error code.</p>
 * @property-read string|null $error_message [default = null] <p>The error message.</p>
 */
class CoercionFailed extends Exception implements ICoercion
{
	//Public constants
	/** Null error code. */
	public const ERROR_CODE_NULL = 'NULL';
	
	/** Invalid type error code. */
	public const ERROR_CODE_INVALID_TYPE = 'INVALID_TYPE';
	
	/** Build exception error code. */
	public const ERROR_CODE_BUILD_EXCEPTION = 'BUILD_EXCEPTION';
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return $this->isset('error_message')
			? "Coercion failed with value {{value}} using component {{component}}, " . 
				"with the following error: {{error_message}}"
			: "Coercion failed with value {{value}} using component {{component}}.";
	}
	
	
	
	//Overridden public static methods
	/** {@inheritdoc} */
	public static function getRequiredPropertyNames() : array
	{
		return array_merge(parent::getRequiredPropertyNames(), ['value']);
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function evaluateProperty(string $name, &$value) : ?bool
	{
		switch ($name) {
			case 'value':
				return true;
			case 'error_code':
				return !isset($value) || (UType::evaluateString($value) && in_array($value, [
					self::ERROR_CODE_NULL,
					self::ERROR_CODE_INVALID_TYPE,
					self::ERROR_CODE_BUILD_EXCEPTION
				], true));
			case 'error_message':
				return UType::evaluateString($value, false, true);
		}
		return parent::evaluateProperty($name, $value);
	}
	
	/** {@inheritdoc} */
	protected function getPlaceholderValueString(string $placeholder, $value) : string
	{
		if ($placeholder === 'error_message' && is_string($value)) {
			return UText::uncapitalize($value, true);
		}
		return parent::getPlaceholderValueString($placeholder, $value);
	}
}
