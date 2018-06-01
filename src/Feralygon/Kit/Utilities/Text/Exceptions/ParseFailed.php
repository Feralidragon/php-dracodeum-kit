<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Utilities\Text\Exceptions;

use Feralygon\Kit\Utilities\Text\Exception;
use Feralygon\Kit\Utilities\Type as UType;

/**
 * This exception is thrown from the text utility whenever a given string failed to be parsed.
 * 
 * @since 1.0.0
 * @property-read string $string
 * <p>The string.</p>
 * @property-read string[] $fields_patterns
 * <p>The fields patterns.</p>
 * @property-read string|null $key [default = null]
 * <p>The key.</p>
 */
class ParseFailed extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return $this->isset('key')
			? "Parse failed for key {{key}} with string {{string}} against fields patterns {{fields_patterns}}."
			: "Parse failed with string {{string}} against fields patterns {{fields_patterns}}.";
	}
	
	
	
	//Implemented protected methods (Feralygon\Kit\Exception\Traits\Properties)
	/** {@inheritdoc} */
	protected function loadProperties() : void
	{
		$this->addProperty('string')->setAsString();
		$this->addProperty('fields_patterns')
			->setAsArray(function (&$key, &$value) : bool {
				return UType::evaluateString($value);
			})
		;
		$this->addProperty('key')->setAsString(false, true)->setDefaultValue(null);
	}
}
