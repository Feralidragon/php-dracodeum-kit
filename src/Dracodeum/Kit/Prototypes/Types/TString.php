<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Prototypes\Types;

use Dracodeum\Kit\Prototypes\Type as Prototype;
use Dracodeum\Kit\Prototypes\Type\Interfaces\MutatorProducer as IMutatorProducer;
use Dracodeum\Kit\Interfaces\Stringable as IStringable;
use Stringable as IPhpStringable;
use Dracodeum\Kit\Primitives\{
	Error,
	Text
};
use Dracodeum\Kit\Components\Type\Prototypes\Mutators\Stringables as StringableMutators;
use Dracodeum\Kit\Traits\LazyProperties\Property;
use Dracodeum\Kit\Enumerations\{
	InfoLevel as EInfoLevel,
	TextCase as ETextCase
};
use Dracodeum\Kit\Root\Locale;

/**
 * This prototype represents a string.
 * 
 * Only the following types of values are allowed to be coerced into a string:
 * - a string, integer or float;
 * - a stringable object, as an object implementing either the PHP `Stringable` interface or 
 * the `Dracodeum\Kit\Interfaces\Stringable` interface.
 * 
 * @property-write bool $unicode [writeonce] [transient] [default = false]  
 * Set as a Unicode string.
 * 
 * @see https://www.php.net/manual/en/class.stringable.php
 * @see \Dracodeum\Kit\Interfaces\Stringable
 */
class TString extends Prototype implements IMutatorProducer
{
	//Protected properties
	protected bool $unicode = false;
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function process(mixed &$value, $context, bool $strict): ?Error
	{
		//process
		$string = null;
		if (is_string($value)) {
			$string = $value;
		} elseif ($strict) {
			return Error::build(
				text: Text::build("Only a string of characters is strictly allowed.")->setAsLocalized(self::class)
			);
		} elseif ($value instanceof IStringable) {
			$string = $value->toString();
		} elseif (is_int($value) || is_float($value) || $value instanceof IPhpStringable) {
			$string = (string)$value;
		} else {
			$text = Text::build()
				->setString("Only a string of characters is allowed.")
				->setString(
					"Only the following types of values are allowed to be coerced into a string:\n" . 
						" - a string, integer or float;\n" . 
						" - a stringable object, as an object implementing either the PHP \"Stringable\" interface " . 
						"or the \"Dracodeum\\Kit\\Interfaces\\Stringable\" interface.",
					EInfoLevel::INTERNAL
				)
				->setAsLocalized(self::class)
			;
			return Error::build(text: $text);
		}
		
		//unicode
		if ($this->unicode) {
			//detect
			$encoding = mb_detect_encoding($string, ['ASCII', 'UTF-8', 'ISO-8859-1'], true);
			if ($encoding === false) {
				$encoding = mb_detect_encoding($string);
				if ($encoding === false) {
					return Error::build(
						text: Text::build("Unknown encoding used in the given string.")->setAsLocalized(self::class)
					);
				}
			}
			
			//convert
			$locale_encoding = Locale::getEncoding();
			if ($encoding !== $locale_encoding) {
				$string = mb_convert_encoding($string, $locale_encoding, $encoding);
			}
			
			//bom
			if ($locale_encoding === 'UTF-8') {
				$string = preg_replace('/^\xEF\xBB\xBF/', '', $string);
			}
		}
		
		//finalize
		$value = $string;
		
		//return
		return null;
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Prototypes\Type\Interfaces\MutatorProducer)
	/** {@inheritdoc} */
	public function produceMutator(string $name, array $properties)
	{
		return match ($name) {
			'length' => new StringableMutators\Length($properties + ['unicode' => $this->unicode]),
			'length_range' => new StringableMutators\LengthRange($properties + ['unicode' => $this->unicode]),
			'min_length' => new StringableMutators\MinLength($properties + ['unicode' => $this->unicode]),
			'max_length' => new StringableMutators\MaxLength($properties + ['unicode' => $this->unicode]),
			'truncate' => new StringableMutators\Truncate($properties + ['unicode' => $this->unicode]),
			'non_empty' => new StringableMutators\NonEmpty($properties + ['unicode' => $this->unicode]),
			'non_empty_iws'
				=> new StringableMutators\NonEmpty(
					['ignore_whitespace' => true] + $properties + ['unicode' => $this->unicode]
				),
			'trim' => StringableMutators\Trim::class,
			'empty_null' => StringableMutators\EmptyNull::class,
			'lowercase', 'lower' => new StringableMutators\Lowercase($properties + ['unicode' => $this->unicode]),
			'uppercase', 'upper' => new StringableMutators\Uppercase($properties + ['unicode' => $this->unicode]),
			'to_lowercase', 'to_lower' => new StringableMutators\ToLowercase(
				$properties + ['unicode' => $this->unicode]
			),
			'to_uppercase', 'to_upper' => new StringableMutators\ToUppercase(
				$properties + ['unicode' => $this->unicode]
			),
			'hexadecimal' => StringableMutators\Hexadecimal::class,
			'base64' => StringableMutators\Base64::class,
			'alphabetical', 'alphabetic'
				=> new StringableMutators\Alphabetical($properties + ['unicode' => $this->unicode]),
			'lower_alphabetical', 'lower_alphabetic'
				=> new StringableMutators\Alphabetical(
					['case' => ETextCase::LOWER] + $properties + ['unicode' => $this->unicode]
				),
			'upper_alphabetical', 'upper_alphabetic'
				=> new StringableMutators\Alphabetical(
					['case' => ETextCase::UPPER] + $properties + ['unicode' => $this->unicode]
				),
			'numerical', 'numeric' => new StringableMutators\Numerical($properties + ['unicode' => $this->unicode]),
			'alphanumerical', 'alphanumeric'
				=> new StringableMutators\Alphanumerical($properties + ['unicode' => $this->unicode]),
			'lower_alphanumerical', 'lower_alphanumeric'
				=> new StringableMutators\Alphanumerical(
					['case' => ETextCase::LOWER] + $properties + ['unicode' => $this->unicode]
				),
			'upper_alphanumerical', 'upper_alphanumeric'
				=> new StringableMutators\Alphanumerical(
					['case' => ETextCase::UPPER] + $properties + ['unicode' => $this->unicode]
				),
			'identifier' => StringableMutators\Identifier::class,
			'xidentifier' => new StringableMutators\Identifier(['extended' => true] + $properties),
			'lower_identifier' => new StringableMutators\Identifier(['case' => ETextCase::LOWER] + $properties),
			'upper_identifier' => new StringableMutators\Identifier(['case' => ETextCase::UPPER] + $properties),
			'lower_xidentifier' => new StringableMutators\Identifier(
				['case' => ETextCase::LOWER, 'extended' => true] + $properties
			),
			'upper_xidentifier' => new StringableMutators\Identifier(
				['case' => ETextCase::UPPER, 'extended' => true] + $properties
			),
			'wildcards' => StringableMutators\Wildcards::class,
			'iwildcards' => new StringableMutators\Wildcards(['insensitive' => true] + $properties),
			'non_wildcards' => new StringableMutators\Wildcards(['negate' => true] + $properties),
			'non_iwildcards'
				=> new StringableMutators\Wildcards(['negate' => true, 'insensitive' => true] + $properties),
			default => null
		};
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Prototype\Traits\PropertyBuilder)
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		return match ($name) {
			'unicode' => $this->createProperty()->setMode('w--')->setAsBoolean()->bind(self::class),
			default => null
		};
	}
}
