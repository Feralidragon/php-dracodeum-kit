<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components\Type\Exceptions;

use Dracodeum\Kit\Components\Type\Exception;
use Dracodeum\Kit\Managers\PropertiesV2\Attributes\Property\coercive;
use Dracodeum\Kit\Components\Type\Enumerations\Context as EContext;
use Dracodeum\Kit\Enums\Info\Level as EInfoLevel;
use Dracodeum\Kit\Options\Text as TextOptions;
use Dracodeum\Kit\Primitives\{
	Text,
	Error
};
use Dracodeum\Kit\Utilities\Text as UText;

abstract class ProcessFailed extends Exception
{
	//Public properties
	public mixed $value;
	
	#[coercive('enum', EContext::class)]
	public string $context;
	
	public ?Error $error = null;
	
	
	
	//Abstract protected methods
	/**
	 * Get label.
	 * 
	 * @return string
	 * The label.
	 */
	abstract protected function getLabel(): string;
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function produceText()
	{
		//build
		$text = Text::build(
			"{$this->getLabel()} failed with value {{value}} within context {{context}} " . 
			"using component {{component}} (with prototype {{component.getPrototypeClass()}})" . 
			($this->error !== null ? ", with the following error: {{error}}" : ".")
		);
		
		//prepare
		$text
			->setPlaceholderStringifier('context', function (mixed $value, TextOptions $text_options): string {
				return EContext::getName($value);
			})
			->setPlaceholderStringifier('error', function (mixed $value, TextOptions $text_options): string {
				return UText::formatMessage(
					$value->getText()->toString(['info_level' => EInfoLevel::INTERNAL->value]), true
				);
			})
		;
		
		//return
		return $text;
	}
}
