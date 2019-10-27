<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Prototypes\Inputs\Text\Constraints;

use Feralygon\Kit\Components\Input\Prototypes\Modifiers\Constraint;
use Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\{
	Subtype as ISubtype,
	Information as IInformation
};
use Feralygon\Kit\Options\Text as TextOptions;
use Feralygon\Kit\Utilities\{
	Text as UText,
	Type as UType
};

/** This constraint prototype restricts a given text input value to hexadecimal characters. */
class Hexadecimal extends Constraint implements ISubtype, IInformation
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getName(): string
	{
		return 'hexadecimal';
	}
	
	/** {@inheritdoc} */
	public function checkValue($value): bool
	{
		return UType::evaluateString($value) && preg_match('/^[\da-f]*$/i', $value);
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\Subtype)
	/** {@inheritdoc} */
	public function getSubtype(): string
	{
		return 'text';
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\Information)
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options): string
	{
		return UText::localize("Hexadecimal characters only", self::class, $text_options);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options): string
	{
		/**
		 * @placeholder digits.num0 The numeric "0" digit character.
		 * @placeholder digits.num9 The numeric "9" digit character.
		 * @placeholder letters.a The lowercase "a" letter character.
		 * @placeholder letters.f The lowercase "f" letter character.
		 * @placeholder letters.A The uppercase "A" letter character.
		 * @placeholder letters.F The uppercase "F" letter character.
		 * @example Only hexadecimal characters (0-9, a-f and A-F) are allowed.
		 */
		return UText::localize(
			"Only hexadecimal characters ({{digits.num0}}-{{digits.num9}}, " . 
				"{{letters.a}}-{{letters.f}} and {{letters.A}}-{{letters.F}}) are allowed.",
			self::class, $text_options, [
				'parameters' => [
					'letters' => ['a' => 'a', 'f' => 'f', 'A' => 'A', 'F' => 'F'],
					'digits' => ['num0' => '0', 'num9' => '9']
				]
			]
		);
	}
}
