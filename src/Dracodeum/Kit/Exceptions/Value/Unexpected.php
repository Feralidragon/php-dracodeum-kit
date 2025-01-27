<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Exceptions\Value;

use Dracodeum\Kit\Exceptions\Value as Exception;
use Dracodeum\Kit\Primitives\Text;
use Dracodeum\Kit\Options\Text as TextOptions;
use Dracodeum\Kit\Utilities\Text as UText;

class Unexpected extends Exception
{
	//Public properties
	public string $name;
	public ?string $error_message = null;
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function produceText()
	{
		return Text::build()
			->setString(
				$this->error_message !== null
					? "Unexpected value {{value}} from {{name}}: {{error_message}}"
					: "Unexpected value {{value}} from {{name}}."
			)
			->setPlaceholderStringifier('error_message', function (mixed $value, TextOptions $text_options): string {
				return UText::formatMessage($value, indentation_expression: "   ");
			});
	}
}
