<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Managers\PropertiesV2\Property\Exceptions;

use Dracodeum\Kit\Managers\PropertiesV2\Property\Exception;
use Dracodeum\Kit\Primitives\{
	Error,
	Text
};
use Dracodeum\Kit\Options\Text as TextOptions;
use Dracodeum\Kit\Enums\Info\Level as EInfoLevel;
use Dracodeum\Kit\Utilities\Text as UText;

class InvalidMetaValue extends Exception
{
	//Public properties
	public string $name;
	public mixed $value;
	public Error $error;
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function produceText()
	{
		return Text::build()
			->setString(
				"Invalid value {{value}} for meta entry {{name}}, in property {{property.getName()}} " . 
					"in class {{property.getMeta().getClass()}}, with the following error: {{error}}"
			)
			->setPlaceholderStringifier('error', function (mixed $value, TextOptions $text_options): string {
				return UText::formatMessage(
					$value->getText()->toString(['info_level' => EInfoLevel::INTERNAL->value]),
					indentation_expression: "   "
				);
			});
	}
}
