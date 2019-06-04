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
 * This exception is thrown from the type utility whenever the coercion into an integer fails with a given value.
 * 
 * @since 1.0.0
 * @property-read mixed $value
 * <p>The value.</p>
 * @property-read bool $unsigned [coercive] [default = false]
 * <p>Set as unsigned.</p>
 * @property-read int|null $bits [coercive] [default = null]
 * <p>The number of bits.</p>
 * @property-read string|null $error_code [coercive] [default = null]
 * <p>The error code.</p>
 * @property-read string|null $error_message [coercive] [default = null]
 * <p>The error message.</p>
 */
class IntegerCoercionFailed extends Exception implements ICoercive
{
	//Public constants
	/** Null error code. */
	public const ERROR_CODE_NULL = 'NULL';
	
	/** Unsigned error code. */
	public const ERROR_CODE_UNSIGNED = 'UNSIGNED';
	
	/** Bits error code. */
	public const ERROR_CODE_BITS = 'BITS';
	
	/** Invalid error code. */
	public const ERROR_CODE_INVALID = 'INVALID';
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		return $this->isset('error_message')
			? "Integer coercion failed with value {{value}}, with the following error: {{error_message}}"
			: "Integer coercion failed with value {{value}}.";
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Interfaces\Throwables\Coercive)
	/** {@inheritdoc} */
	public function getValue()
	{
		return $this->get('value');
	}
	
	/** {@inheritdoc} */
	public function getErrorCode(): ?string
	{
		return $this->get('error_code');
	}
	
	/** {@inheritdoc} */
	public function getErrorMessage(): ?string
	{
		return $this->get('error_message');
	}
	
	
	
	//Implemented protected methods (Feralygon\Kit\Exception\Traits\PropertiesLoader)
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		$this->addProperty('value');
		$this->addProperty('unsigned')->setAsBoolean()->setDefaultValue(false);
		$this->addProperty('bits')->setAsInteger(false, null, true)->setDefaultValue(null);
		$this->addProperty('error_code')
			->setAsString(true, true)
			->addEvaluator(function (&$value): bool {
				return !isset($value) || in_array($value, [
					self::ERROR_CODE_NULL,
					self::ERROR_CODE_UNSIGNED,
					self::ERROR_CODE_BITS,
					self::ERROR_CODE_INVALID
				], true);
			})
			->setDefaultValue(null)
		;
		$this->addProperty('error_message')->setAsString(false, true)->setDefaultValue(null);
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function getPlaceholderValueString(string $placeholder, $value): string
	{
		if ($placeholder === 'error_message' && is_string($value)) {
			return UText::formatMessage($value, true);
		}
		return parent::getPlaceholderValueString($placeholder, $value);
	}
}
