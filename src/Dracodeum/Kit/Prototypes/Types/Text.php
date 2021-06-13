<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Prototypes\Types;

use Dracodeum\Kit\Prototypes\Type as Prototype;
use Dracodeum\Kit\Prototypes\Type\Interfaces\Textifier as ITextifier;
use Dracodeum\Kit\Interfaces\Stringable as IStringable;
use Stringable as IPhpStringable;
use Dracodeum\Kit\Primitives\{
	Error,
	Text as TextPrimitive
};
use Dracodeum\Kit\Enumerations\InfoLevel as EInfoLevel;

/**
 * This prototype represents a text.
 * 
 * Only the following types of values are allowed to be coerced into a text:
 * - a string;
 * - an instance;
 * - a stringable object, as an object implementing either the PHP `Stringable` interface or 
 * the `Dracodeum\Kit\Interfaces\Stringable` interface.
 * 
 * @see https://www.php.net/manual/en/class.stringable.php
 * @see \Dracodeum\Kit\Interfaces\Stringable
 */
class Text extends Prototype implements ITextifier
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function process(mixed &$value, $context): ?Error
	{
		//process
		$text = null;
		if (is_string($value)) {
			$text = TextPrimitive::build($value);
		} elseif ($value instanceof TextPrimitive) {
			$text = $value;
		} elseif ($value instanceof IStringable) {
			$text = TextPrimitive::build($value->toString());
		} elseif ($value instanceof IPhpStringable) {
			$text = TextPrimitive::build((string)$value);
		}
		
		//error
		if ($text === null) {
			$error_text = TextPrimitive::build()
				->setString("Only a text is allowed.")
				->setString(
					"Only the following types of values are allowed to be coerced into a text:\n" . 
						" - a string;\n" . 
						" - an instance;\n" . 
						" - a stringable object, as an object implementing either the PHP \"Stringable\" interface " . 
						"or the \"Dracodeum\\Kit\\Interfaces\\Stringable\" interface.",
					EInfoLevel::INTERNAL
				)
			;
			return Error::build(text: $error_text);
		}
		
		//finalize
		$value = $text;
		
		//return
		return null;
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Prototypes\Type\Interfaces\Textifier)
	/** {@inheritdoc} */
	public function textify(mixed $value)
	{
		return $value;
	}
}
