<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Prototypes\Types;

use Dracodeum\Kit\Prototypes\Type as Prototype;
use Dracodeum\Kit\Components\Type\Enumerations\Context as EContext;
use Dracodeum\Kit\Primitives\{
	Error,
	Text
};
use Dracodeum\Kit\Options\Text as TextOptions;
use Dracodeum\Kit\Utilities\Text as UText;

/**
 * This type prototype represents a boolean.
 * 
 * All types of values are directly cast to a boolean when the internal context is used.<br>
 * <br>
 * Otherwise, when any other context is used, only the following types of values may be converted to a boolean:<br>
 * &nbsp; &#8226; &nbsp; an integer, with <code>0</code> as boolean <code>false</code>, 
 * and <code>1</code> as boolean <code>true</code>;<br>
 * &nbsp; &#8226; &nbsp; a string, with <code>"0"</code>, <code>"f"</code>, <code>"false"</code>, 
 * <code>"off"</code> or <code>"no"</code> as boolean <code>false</code>, 
 * and <code>"1"</code>, <code>"t"</code>, <code>"true"</code>, 
 * <code>"on"</code> or <code>"yes"</code> as boolean <code>true</code>.
 */
class Boolean extends Prototype
{
	//Private constants
	/** Strings recognized as <code>false</code>. */
	private const STRINGS_FALSE = ['0', 'f', 'false', 'off', 'no'];
	
	/** Strings recognized as <code>true</code>. */
	private const STRINGS_TRUE = ['1', 't', 'true', 'on', 'yes'];
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function process(mixed &$value): ?Error
	{
		//process
		if (is_bool($value)) {
			return null;
		} elseif ($this->getContext() === EContext::INTERNAL) {
			$value = (bool)$value;
			return null;
		} elseif ($value === 0) {
			$value = false;
			return null;
		} elseif ($value === 1) {
			$value = true;
			return null;
		} elseif (is_string($value)) {
			$v = strtolower($value);
			if (in_array($v, self::STRINGS_FALSE, true)) {
				$value = false;
				return null;
			} elseif (in_array($v, self::STRINGS_TRUE, true)) {
				$value = true;
				return null;
			}
		}
		
		//error
		$values_stringifier = function (mixed $value, TextOptions $text_options): string {
			return UText::commify($value, $text_options, 'or', true);
		};
		$text = Text::build()
			->setString(
				"Only a boolean is allowed, which may be given as {{values.false}} as boolean false, " . 
				"and {{values.true}} as boolean true."
			)
			->setParameter('values', ['false' => self::STRINGS_FALSE, 'true' => self::STRINGS_TRUE])
			->setPlaceholderStringifier('values.false', $values_stringifier)
			->setPlaceholderStringifier('values.true', $values_stringifier)
			->setAsLocalized(self::class)
		;
		return Error::build(text: $text);
	}
}
