<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Utilities\Text\Exceptions;

use Dracodeum\Kit\Utilities\Text\Exception;
use Dracodeum\Kit\Utilities\Type as UType;

/**
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
	public function getDefaultMessage(): string
	{
		return $this->key !== null
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
