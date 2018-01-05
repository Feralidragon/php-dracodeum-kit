<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Options\Exceptions;

use Feralygon\Kit\Core\Options;
use Feralygon\Kit\Core\Options\Exception;
use Feralygon\Kit\Core\Utilities\Type as UType;

/**
 * Core options load failed exception class.
 * 
 * This exception is thrown from an options instance whenever given options fail to be loaded.
 * 
 * @since 1.0.0
 * @property-read string $class <p>The options class.</p>
 * @property-read mixed $options <p>The options.</p>
 */
class LoadFailed extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Load failed as invalid options have been passed as {{options}} for class {{class}}.\n" . 
			"HINT: Only a null value, an options instance or associative array are allowed.";
	}
	
	
	
	//Implemented public static methods
	/** {@inheritdoc} */
	public static function getRequiredPropertyNames() : array
	{
		return ['class', 'options'];
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function evaluateProperty(string $name, &$value) : ?bool
	{
		switch ($name) {
			case 'class':
				return UType::evaluateClass($value, Options::class);
			case 'options':
				return true;
		}
		return null;
	}
}
