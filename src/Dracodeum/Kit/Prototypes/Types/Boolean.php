<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Prototypes\Types;

use Dracodeum\Kit\Prototypes\Type as Prototype;
use Dracodeum\Kit\Prototypes\Type\Interfaces\Textifier as ITextifier;
use Dracodeum\Kit\Components\Type\Enumerations\Context as EContext;
use Dracodeum\Kit\Primitives\{
	Error,
	Text
};
use Dracodeum\Kit\Options\Text as TextOptions;
use Dracodeum\Kit\Enumerations\InfoLevel as EInfoLevel;
use Dracodeum\Kit\Utilities\Text as UText;

/**
 * This prototype represents a boolean.
 * 
 * All types of values are directly cast to a boolean when the internal context is used.
 * 
 * Otherwise, when any other context is used, only the following types of values are allowed to be coerced into a 
 * boolean:
 * - a boolean;
 * - an integer, with `0` as boolean `false`, and `1` as boolean `true`;
 * - a string, with `"0"`, `"f"`, `"false"`, `"off"` or `"no"` as boolean `false`, 
 * and `"1"`, `"t"`, `"true"`, `"on"` or `"yes"` as boolean `true`.
 */
class Boolean extends Prototype implements ITextifier
{
	//Private constants
	/** Strings recognized as `true`. */
	private const STRINGS_TRUE = ['1', 't', 'true', 'on', 'yes'];
	
	/** Strings recognized as `false`. */
	private const STRINGS_FALSE = ['0', 'f', 'false', 'off', 'no'];
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function process(mixed &$value, $context): ?Error
	{
		//process
		if (is_bool($value)) {
			return null;
		} elseif ($context === EContext::INTERNAL) {
			$value = (bool)$value;
			return null;
		} elseif ($value === 1) {
			$value = true;
			return null;
		} elseif ($value === 0) {
			$value = false;
			return null;
		} elseif (is_string($value)) {
			$v = strtolower($value);
			if (in_array($v, self::STRINGS_TRUE, true)) {
				$value = true;
				return null;
			} elseif (in_array($v, self::STRINGS_FALSE, true)) {
				$value = false;
				return null;
			}
		}
		
		//error
		$values_stringifier = function (mixed $value, TextOptions $text_options): string {
			return UText::commify($value, $text_options, 'or', true);
		};
		$text = Text::build()
			->setString(
				"Only a boolean is allowed, which may be given as {{values.true}} as boolean true, " . 
				"and {{values.false}} as boolean false."
			)
			->setParameter('values', ['true' => self::STRINGS_TRUE, 'false' => self::STRINGS_FALSE])
			->setPlaceholderStringifier('values.true', $values_stringifier)
			->setPlaceholderStringifier('values.false', $values_stringifier)
			->setAsLocalized(self::class)
		;
		return Error::build(text: $text);
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Prototypes\Type\Interfaces\Textifier)
	/** {@inheritdoc} */
	public function textify(mixed $value)
	{
		return match ($value) {
			false => Text::build("no")->setString("false", EInfoLevel::TECHNICAL)->setAsLocalized(self::class),
			true => Text::build("yes")->setString("true", EInfoLevel::TECHNICAL)->setAsLocalized(self::class)
		};
	}
}
