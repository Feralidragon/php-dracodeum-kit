<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Traits\Memoization\Exceptions;

use Feralygon\Kit\Core\Traits\Memoization\Exception;
use Feralygon\Kit\Core\Utilities\Type as UType;

/**
 * Core memoization trait no memoized value found exception class.
 * 
 * This exception is thrown from a class or object using the memoization trait whenever no memoized value 
 * is found at a given key.
 * 
 * @since 1.0.0
 * @property-read string $key <p>The key.</p>
 * @property-read string $namespace [default = ''] <p>The namespace.</p>
 */
class NoMemoizedValueFound extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return $this->get('namespace') !== ''
			? "No memoized value found at key {{key}} in namespace {{namespace}}."
			: "No memoized value found at key {{key}}.";
	}
	
	
	
	//Implemented public static methods
	/** {@inheritdoc} */
	public static function getRequiredPropertyNames() : array
	{
		return ['key'];
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function evaluateProperty(string $name, &$value) : ?bool
	{
		switch ($name) {
			case 'key':
				return UType::evaluateString($value, true);
			case 'namespace':
				$value = $value ?? '';
				return UType::evaluateString($value);
		}
		return null;
	}
}
