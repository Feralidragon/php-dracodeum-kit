<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Traits\Exception;

use Feralygon\Kit\Utilities\Text as UText;

/**
 * This trait implements the <code>Feralygon\Kit\Interfaces\Throwables\Coercive</code> interface 
 * and adds some properties, namely <var>$value</var>, <var>$error_code</var> and <var>$error_message</var>, 
 * to be used exclusively by <code>Feralygon\Kit\Exception</code> classes.
 * 
 * @since 1.0.0
 * @property-read mixed $value
 * <p>The value.</p>
 * @property-read string|null $error_code [default = null]
 * <p>The error code.</p>
 * @property-read string|null $error_message [default = null]
 * <p>The error message.</p>
 * @see \Feralygon\Kit\Exception
 * @see \Feralygon\Kit\Interfaces\Throwables\Coercive
 */
trait Coercive
{
	//Public constants
	/**
	 * Define the error code constants as public constants here.
	 * Their names must all be prefixed with "ERROR_CODE_" in order to be recognized as such.
	 * Example:
	 * 	public const ERROR_CODE_CODE1 = 'CODE1';
	 * 	public const ERROR_CODE_CODE2 = 'CODE2';
	 *  ...
	 */
	
	
	
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
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function loadProperties() : void
	{
		//parent
		parent::loadProperties();
		
		//error codes
		$error_codes = [];
		foreach ((new \ReflectionClass($this))->getConstants() as $name => $value) {
			if (preg_match('/^ERROR_CODE_/', $name)) {
				$error_codes[] = $value;
			}
		}
		
		//properties
		$this->addProperty('value')->setAsRequired();
		$this->addProperty('error_code')
			->setAsString(true, true)
			->addEvaluator(function (&$value) use ($error_codes) : bool {
				return !isset($value) || in_array($value, $error_codes, true);
			})
			->setDefaultValue(null)
		;
		$this->addProperty('error_message')->setAsString(false, true)->setDefaultValue(null);
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
