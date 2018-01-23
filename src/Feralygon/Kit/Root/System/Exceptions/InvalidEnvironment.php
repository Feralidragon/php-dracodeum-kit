<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Root\System\Exceptions;

use Feralygon\Kit\Root\System\Exception;
use Feralygon\Kit\Core\Utilities\Text as UText;

/**
 * Root system invalid environment exception class.
 * 
 * This exception is thrown from the system whenever a given environment is invalid.
 * 
 * @since 1.0.0
 * @property-read mixed $environment <p>The environment.</p>
 */
class InvalidEnvironment extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Invalid environment {{environment}}.\n" . 
			"HINT: Only an environment instance, class or name is allowed.";
	}
	
	
	
	//Implemented public static methods
	/** {@inheritdoc} */
	public static function getRequiredPropertyNames() : array
	{
		return ['environment'];
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function evaluateProperty(string $name, &$value) : ?bool
	{
		switch ($name) {
			case 'environment':
				return true;
		}
		return null;
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function getPlaceholderValueString(string $placeholder, $value) : string
	{
		if ($placeholder === 'environment' && !is_string($value)) {
			return UText::stringify($value, null, ['prepend_type' => true]);
		}
		return parent::getPlaceholderValueString($placeholder, $value);
	}
}
