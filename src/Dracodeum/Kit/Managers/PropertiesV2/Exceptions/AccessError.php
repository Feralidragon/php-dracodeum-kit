<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Managers\PropertiesV2\Exceptions;

use Dracodeum\Kit\Managers\PropertiesV2\Exception;
use Dracodeum\Kit\Attributes\Property\{
	Coercive,
	Mutator
};
use Dracodeum\Kit\Options\Text as TextOptions;
use Dracodeum\Kit\Primitives\Text;
use Dracodeum\Kit\Utilities\Text as UText;

abstract class AccessError extends Exception
{
	//Public properties
	/** @var string[] */
	#[Coercive('string[]'), Mutator('non_empty')]
	public array $names;
	
	
	
	//Abstract protected methods
	/**
	 * Get string.
	 * 
	 * @return string
	 * The string.
	 */
	abstract protected function getString(): string;
	
	/**
	 * Get plural string.
	 * 
	 * @return string
	 * The plural string.
	 */
	abstract protected function getPluralString(): string;
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function produceText()
	{
		return Text::build()
			->setString($this->getString())
			->setPluralString($this->getPluralString())
			->setPluralNumber(count($this->names))
			->setPlaceholderStringifier('names', function (mixed $value, TextOptions $text_options): string {
				return UText::commify($value, $text_options, 'and', true);
			})
		;
	}
}
