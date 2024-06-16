<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Exceptions\Argument;

use Dracodeum\Kit\Exceptions\Argument as Exception;
use Dracodeum\Kit\Primitives\Text;
use Dracodeum\Kit\Options\Text as TextOptions;
use Dracodeum\Kit\Utilities\Text as UText;

class Invalid extends Exception
{
	//Public properties
	public mixed $value;
	public ?string $error_message = null;
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function produceText()
	{
		return Text::build()
			->setString(
				$this->error_message !== null
					? "Invalid argument value {{value}} given in {{name}}: {{error_message}}"
					: "Invalid argument value {{value}} given in {{name}}."
			)
			->setPlaceholderStringifier('error_message', function (mixed $value, TextOptions $text_options): string {
				return UText::formatMessage($value, indentation_expression: "   ");
			});
	}
}
