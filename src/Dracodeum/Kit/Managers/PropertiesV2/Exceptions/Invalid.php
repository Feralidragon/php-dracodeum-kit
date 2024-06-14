<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Managers\PropertiesV2\Exceptions;

use Dracodeum\Kit\Managers\PropertiesV2\Exception;
use Dracodeum\Kit\Managers\PropertiesV2\Attributes\Property\{
	coercive,
	mutator
};
use Dracodeum\Kit\Primitives\{
	Error,
	Text
};
use Dracodeum\Kit\Options\Text as TextOptions;
use Dracodeum\Kit\Enums\Info\Level as EInfoLevel;
use Dracodeum\Kit\Utilities\{
	Data as UData,
	Text as UText
};

class Invalid extends Exception
{
	//Public properties
	/**
	 * @var array<string,mixed>
	 * The values, as a set of `name => value` pairs.
	 */
	#[coercive('array<string,mixed>'), mutator('non_empty')]
	public array $values;
	
	/**
	 * @var array<string,\Dracodeum\Kit\Primitives\Error>
	 * The error instances, as a set of `name => error` pairs.
	 */
	#[coercive('array<string,' . Error::class . '>'), mutator('non_empty')]
	public array $errors;
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function produceText()
	{
		return Text::build()
			->setString(
				"Invalid value given for the following property in {{manager.getOwner()}}: {{values}}, " . 
				"with the following error: {{errors}}"
			)
			->setPluralString(
				"Invalid values given for the following properties in {{manager.getOwner()}}:\n" . 
				"{{values}}\n" . 
				"with the following errors:\n" . 
				"{{errors}}"
			)
			->setPluralNumber(count($this->values))
			->setPlaceholderStringifier('values', function (mixed $value, TextOptions $text_options): string {
				//initialize
				$stringifier = $this->getStringifier();
				
				//single
				if (count($value) === 1) {
					return UData::kfirst($value) . " = " . $stringifier(UData::first($value), $text_options);
				}
				
				//multiple
				$strings = [];
				foreach ($value as $name => $v) {
					$strings[] = "{$name} = " . $stringifier($v, $text_options) . ";";
				}
				return UText::mbulletify($strings, options: ['bullet' => '*', 'merge' => true]);
			})
			->setPlaceholderStringifier('errors', function (mixed $value, TextOptions $text_options): string {
				//single
				if (count($value) === 1) {
					return UText::formatMessage(
						UData::first($value)->getText()->toString(['info_level' => EInfoLevel::INTERNAL->value]),
						indentation_expression: "   "
					);
				}
				
				//multiple
				$strings = [];
				foreach ($value as $name => $v) {
					$strings[] = "{$name}: " . UText::formatMessage(
						$v->getText()->toString(['info_level' => EInfoLevel::INTERNAL->value]),
						indentation_expression: "   "
					);
				}
				return UText::mbulletify($strings, options: ['bullet' => '-', 'merge' => true]);
			})
		;
	}
}
