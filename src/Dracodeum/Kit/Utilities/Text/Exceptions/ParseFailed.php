<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Utilities\Text\Exceptions;

use Dracodeum\Kit\Utilities\Text\Exception;
use Dracodeum\Kit\Utilities\Type as UType;

/**
 * This exception is thrown from the text utility whenever a given string failed to be parsed.
 * 
 * @property-read string $string [coercive]
 * <p>The string.</p>
 * @property-read string[] $fields_patterns [coercive]
 * <p>The fields patterns.</p>
 * @property-read string|null $key [coercive] [default = null]
 * <p>The key.</p>
 */
class ParseFailed extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		return $this->isset('key')
			? "Parse failed for key {{key}} with string {{string}} against fields patterns {{fields_patterns}}."
			: "Parse failed with string {{string}} against fields patterns {{fields_patterns}}.";
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Exception\Traits\PropertiesLoader)
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		$this->addProperty('string')->setAsString();
		$this->addProperty('fields_patterns')
			->setAsArray(function (&$key, &$value): bool {
				return UType::evaluateString($value);
			})
		;
		$this->addProperty('key')->setAsString(false, true)->setDefaultValue(null);
	}
}