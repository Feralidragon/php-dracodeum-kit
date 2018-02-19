<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Prototypes\Inputs\Text\Prototypes\Modifiers\Constraints;

use Feralygon\Kit\Prototypes\Input\Prototypes\Modifiers\Constraint;
use Feralygon\Kit\Prototypes\Input\Prototypes\Modifier\Interfaces\{
	Name as IName,
	Information as IInformation
};
use Feralygon\Kit\Options\Text as TextOptions;
use Feralygon\Kit\Enumerations\InfoScope as EInfoScope;
use Feralygon\Kit\Utilities\Text as UText;

/**
 * Text input hexadecimal constraint modifier prototype class.
 * 
 * This constraint prototype restricts a text or string to hexadecimal characters.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Prototypes\Inputs\Text
 */
class Hexadecimal extends Constraint implements IName, IInformation
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function checkValue($value) : bool
	{
		return (bool)preg_match('/^[\da-f]*$/i', $value);
	}
	
	
	
	//Implemented public methods (input modifier prototype name interface)
	/** {@inheritdoc} */
	public function getName() : string
	{
		return 'constraints.hexadecimal';
	}
	
	
	
	//Implemented public methods (input modifier prototype information interface)
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options) : string
	{
		return UText::localize("Hexadecimal characters only", self::class, $text_options);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options) : string
	{
		//end-user
		if ($text_options->info_scope === EInfoScope::ENDUSER) {
			/**
			 * @placeholder digits.num0 The numeric "0" digit character.
			 * @placeholder digits.num9 The numeric "9" digit character.
			 * @placeholder letters.a The lowercase "a" letter character.
			 * @placeholder letters.f The lowercase "f" letter character.
			 * @placeholder letters.A The uppercase "A" letter character.
			 * @placeholder letters.F The uppercase "F" letter character.
			 * @tags end-user
			 * @example Only hexadecimal characters (0 to 9, a to f and A to F) are allowed.
			 */
			return UText::localize(
				"Only hexadecimal characters ({{digits.num0}} to {{digits.num9}}, " . 
					"{{letters.a}} to {{letters.f}} and {{letters.A}} to {{letters.F}}) are allowed.", 
				self::class, $text_options, [
					'parameters' => [
						'letters' => ['a' => 'a', 'f' => 'f', 'A' => 'A', 'F' => 'F'],
						'digits' => ['num0' => '0', 'num9' => '9']
					]
				]
			);
		}
		
		//non-end-user
		/**
		 * @placeholder digits.num0 The numeric "0" digit character.
		 * @placeholder digits.num9 The numeric "9" digit character.
		 * @placeholder letters.a The lowercase "a" letter character.
		 * @placeholder letters.f The lowercase "f" letter character.
		 * @placeholder letters.A The uppercase "A" letter character.
		 * @placeholder letters.F The uppercase "F" letter character.
		 * @tags non-end-user
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
