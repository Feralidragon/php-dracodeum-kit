<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Prototypes\Types;

use Dracodeum\Kit\Prototypes\Type as Prototype;
use Dracodeum\Kit\Prototypes\Type\Interfaces\Textifier as ITextifier;
use Dracodeum\Kit\Enumeration as KitEnumeration;
use Dracodeum\Kit\Components\Type\Enumerations\Context as EContext;
use Dracodeum\Kit\Primitives\{
	Error,
	Text
};
use Dracodeum\Kit\Options\Text as TextOptions;
use Dracodeum\Kit\Traits\LazyProperties\Property;
use Dracodeum\Kit\Utilities\Text as UText;

/**
 * This prototype represents an enumeration.
 * 
 * Only the following types of values are allowed to be coerced into an enumeration:
 * - an integer or string as an enumeration element value (when the internal context is used);
 * - a string as an enumeration element name.
 * 
 * @property-write string $enumeration [writeonce] [transient] [strict = class]  
 * The enumeration class to use.
 * 
 * @see \Dracodeum\Kit\Enumeration
 */
class Enumeration extends Prototype implements ITextifier
{
	//Protected properties
	protected string $enumeration;
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function process(mixed &$value, $context): ?Error
	{
		//initialize
		$enumeration = $this->enumeration;
		
		//process
		$enum_value = null;
		if ($context !== EContext::INTERNAL) {
			if (is_string($value) && $enumeration::hasName($value)) {
				$enum_value = $enumeration::getValue($value);
			} else {
				$text = Text::build()->setAsLocalized(self::class);
				$enum_names = $enumeration::getNames();
				if ($enum_names) {
					$text
						->setString("Only {{names}} is allowed.")
						->setPluralString("Only one of the following is allowed: {{names}}.")
						->setPluralNumber(count($enum_names))
						->setParameter('names', $enum_names)
						->setPlaceholderStringifier(
							'names', function (mixed $value, TextOptions $text_options): string {
								return UText::commify($value, $text_options, 'or', true);
							}
						)
					;
				} else {
					$text->setString("No values are allowed.");
				}
				return Error::build(text: $text);
			}
		} elseif (
			((is_int($value) || is_string($value)) && $enumeration::hasValue($value)) || 
			(is_string($value) && $enumeration::hasName($value))
		) {
			$enum_value = $enumeration::getValue($value);
		}
		
		//error
		if ($enum_value === null) {
			$text = Text::build("Only a value or name of the {{enumeration}} enumeration is allowed.")
				->setParameter('enumeration', $enumeration)
			;
			return Error::build(text: $text);
		}
		
		//finalize
		$value = $enum_value;
		
		//return
		return null;
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Prototypes\Type\Interfaces\Textifier)
	/** {@inheritdoc} */
	public function textify(mixed $value)
	{
		return $this->enumeration::getLabel($value);
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Prototype\Traits\PropertiesInitializer)
	/** {@inheritdoc} */
	protected function initializeProperties(): void
	{
		$this->addRequiredPropertyName('enumeration');
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Prototype\Traits\PropertyBuilder)
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		return match ($name) {
			'enumeration'
				=> $this->createProperty()
					->setMode('w--')
					->setAsStrictClass(KitEnumeration::class)
					->bind(self::class)
				,
			default => null
		};
	}
}
