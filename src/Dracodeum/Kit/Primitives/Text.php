<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Primitives;

use Dracodeum\Kit\Primitive;
use Dracodeum\Kit\Interfaces\{
	Stringable as IStringable,
	StringInstantiable as IStringInstantiable
};
use JsonSerializable as IJsonSerializable;
use Dracodeum\Kit\Traits;
use Dracodeum\Kit\Primitives\Text\Exceptions;
use Dracodeum\Kit\Enums\Info\Level as EInfoLevel;
use Dracodeum\Kit\Options\Text as TextOptions;
use Dracodeum\Kit\Utilities\{
	Byte as UByte,
	Call as UCall,
	Text as UText,
	Type as UType
};

/**
 * This primitive represents a text.
 * 
 * This is a simple object which represents and stores a string or set of strings, each to be shown to an end-user or 
 * developer, or to be shown or logged internally, depending on the requested and assigned info level of each string.
 * 
 * Each string may also be dynamic and parameterized, by:
 * - having placeholders to be replaced with parameters;
 * - having a plural form;
 * - supporting localization, to be translated to different languages.
 */
final class Text extends Primitive implements IStringable, IStringInstantiable, IJsonSerializable
{
	//Traits
	use Traits\Stringable;
	
	
	
	//Private constants
	/** Quoted placeholder flag. */
	private const FLAG_PLACEHOLDER_QUOTED = 0x1;
	
	
	
	//Private properties
	/** @var array<int,string> */
	private array $strings = [];
	
	/** @var array<int,string> */
	private array $plural_strings = [];
	
	private float $plural_number = 1.0;
	
	private ?string $plural_number_placeholder = null;
	
	/** @var array<string,mixed> */
	private array $parameters = [];
	
	private ?object $object = null;
	
	/** @var callable|null */
	private $stringifier = null;
	
	/** @var array<string,int> */
	private array $placeholders_flags = [];
	
	/** @var array<string,callable> */
	private array $placeholders_stringifiers = [];
	
	private bool $localized = false;
	
	/** @var \Dracodeum\Kit\Primitives\Text[] */
	private array $texts = [];
	
	/** @var callable|null */
	private $texts_strings_stringifier = null;
	
	
	
	//Final public magic methods
	/**
	 * Instantiate class.
	 * 
	 * @param string|null $string
	 * The string to instantiate with.
	 * 
	 * Placeholders may optionally be set in the given string as `{{placeholder}}` to be replaced by a corresponding 
	 * set of parameters, and they must be exclusively composed of identifiers, which are defined as words which must 
	 * start with a letter (`a-z` or `A-Z`) or underscore (`_`), and may only contain letters (`a-z` or `A-Z`), 
	 * digits (`0-9`) and underscores (`_`).
	 * 
	 * They may also contain pointers to specific object properties or associative array values from the given set of 
	 * parameters by using a dot between identifiers, such as `{{object.property}}`, with no limit on the number of 
	 * pointers chained.
	 * 
	 * If suffixed with opening and closing parenthesis, such as `{{object.method()}}`, then the given pointers are 
	 * interpreted as getter method calls, but they cannot be given any arguments.
	 * 
	 * @param \Dracodeum\Kit\Enums\Info\Level $info_level
	 * The info level to instantiate with.
	 */
	final public function __construct(?string $string = null, EInfoLevel $info_level = EInfoLevel::ENDUSER)
	{
		if ($string !== null) {
			$this->setString($string, $info_level);
		}
	}
	
	
	
	//Implemented final public methods (Dracodeum\Kit\Interfaces\Stringable)
	/** {@inheritdoc} */
	final public function toString($text_options = null): string
	{
		//initialize
		$text_options = TextOptions::coerce($text_options, nullable: true);
		$info_level_values = $text_options !== null
			? array_reverse(range(EInfoLevel::ENDUSER->value, $text_options->info_level))
			: array_map(fn (EInfoLevel $info_level) => $info_level->value, EInfoLevel::cases());
		
		//string
		$string = '';
		$plural_string = null;
		foreach ($info_level_values as $info_level_value) {
			if (isset($this->strings[$info_level_value])) {
				$string = $this->strings[$info_level_value];
				$plural_string = $this->plural_strings[$info_level_value] ?? null;
				break;
			}
		}
		
		//process
		if (UText::hasPlaceholders($string)) {
			//initialize
			$fill_text_options = TextOptions::coerce($text_options);
			$fill_options = [
				'object' => $this->object,
				'stringifier' => function (string $placeholder, $value) use ($fill_text_options): ?string {
					//string
					$string = isset($this->placeholders_stringifiers[$placeholder])
						? ($this->placeholders_stringifiers[$placeholder])($value, $fill_text_options)
						: (
							$this->stringifier !== null
							? ($this->stringifier)($value, $fill_text_options)
							: UText::stringify($value, $fill_text_options)
						);
					
					//flags
					$flags = $this->placeholders_flags[$placeholder] ?? 0x0;
					if (UByte::hasFlag($flags, self::FLAG_PLACEHOLDER_QUOTED)) {
						$string = $fill_text_options->info_level === EInfoLevel::ENDUSER->value
							? "\u{201c}{$string}\u{201d}"
							: "\"{$string}\"";
					}
					
					//return
					return $string;
				}
			];
			
			//fill
			$string = $plural_string !== null
				? UText::pfill(
					$string, $plural_string, $this->plural_number, $this->plural_number_placeholder, $this->parameters,
					$fill_text_options, $fill_options
				)
				: UText::fill($string, $this->parameters, $fill_text_options, $fill_options);
			
			//finalize
			unset($fill_text_options, $fill_options);
			
		} elseif ($plural_string !== null && abs($this->plural_number) !== 1.0) {
			$string = $plural_string;
		}
		
		//texts
		if ($this->texts) {
			//strings
			$strings = [];
			foreach ($this->texts as $text) {
				$strings[] = $text->toString($text_options);
			}
			$strings = array_values(array_filter($strings, fn ($string) => $string !== ''));
			
			//string
			if ($strings) {
				if ($string !== '') {
					$string .= "\n";
				}
				$string .= $this->texts_strings_stringifier !== null
					? ($this->texts_strings_stringifier)($strings, TextOptions::coerce($text_options))
					: implode("\n", $strings);
			}
			
			//finalize
			unset($strings);
		}
		
		//return
		return $string;
	}
	
	
	
	//Implemented final public static methods (Dracodeum\Kit\Interfaces\StringInstantiable)
	/** {@inheritdoc} */
	final public static function fromString(string $string): object
	{
		return self::build($string);
	}
	
	
	
	//Implemented final public methods (JsonSerializable)
	/** {@inheritdoc} */
	final public function jsonSerialize(): mixed
	{
		return $this->toString();
	}
	
	
	
	//Final public methods
	/**
	 * Check if has string.
	 * 
	 * @param \Dracodeum\Kit\Enums\Info\Level $info_level
	 * The info level to check for.
	 * 
	 * @return bool
	 * Boolean `true` if has string.
	 */
	final public function hasString(EInfoLevel $info_level = EInfoLevel::ENDUSER): bool
	{
		return $this->getString($info_level) !== null;
	}
	
	/**
	 * Get string.
	 * 
	 * @param \Dracodeum\Kit\Enums\Info\Level $info_level
	 * The info level to get for.
	 * 
	 * @return string|null
	 * The string, or `null` if none is set.
	 */
	final public function getString(EInfoLevel $info_level = EInfoLevel::ENDUSER): ?string
	{
		return $this->strings[$info_level->value] ?? null;
	}
	
	/**
	 * Set string.
	 * 
	 * @param string $string
	 * The string to set.
	 * 
	 * Placeholders may optionally be set in the given string as `{{placeholder}}` to be replaced by a corresponding 
	 * set of parameters, and they must be exclusively composed of identifiers, which are defined as words which must 
	 * start with a letter (`a-z` or `A-Z`) or underscore (`_`), and may only contain letters (`a-z` or `A-Z`), 
	 * digits (`0-9`) and underscores (`_`).
	 * 
	 * They may also contain pointers to specific object properties or associative array values from the given set of 
	 * parameters by using a dot between identifiers, such as `{{object.property}}`, with no limit on the number of 
	 * pointers chained.
	 * 
	 * If suffixed with opening and closing parenthesis, such as `{{object.method()}}`, then the given pointers are 
	 * interpreted as getter method calls, but they cannot be given any arguments.
	 * 
	 * @param \Dracodeum\Kit\Enums\Info\Level $info_level
	 * The info level to set with.
	 * 
	 * @return $this
	 * This instance, for chaining purposes.
	 */
	final public function setString(string $string, EInfoLevel $info_level = EInfoLevel::ENDUSER)
	{
		$this->strings[$info_level->value] = $string;
		return $this;
	}
	
	/**
	 * Check if has plural string.
	 * 
	 * @param \Dracodeum\Kit\Enums\Info\Level $info_level
	 * The info level to check for.
	 * 
	 * @return bool
	 * Boolean `true` if has plural string.
	 */
	final public function hasPluralString(EInfoLevel $info_level = EInfoLevel::ENDUSER): bool
	{
		return $this->getPluralString($info_level) !== null;
	}
	
	/**
	 * Get plural string.
	 * 
	 * @param \Dracodeum\Kit\Enums\Info\Level $info_level
	 * The info level to get for.
	 * 
	 * @return string|null
	 * The plural string, or `null` if none is set.
	 */
	final public function getPluralString(EInfoLevel $info_level = EInfoLevel::ENDUSER): ?string
	{
		return $this->plural_strings[$info_level->value] ?? null;
	}
	
	/**
	 * Set plural string.
	 * 
	 * @param string $string
	 * The string to set.
	 * 
	 * Placeholders may optionally be set in the given string as `{{placeholder}}` to be replaced by a corresponding 
	 * set of parameters, and they must be exclusively composed of identifiers, which are defined as words which must 
	 * start with a letter (`a-z` or `A-Z`) or underscore (`_`), and may only contain letters (`a-z` or `A-Z`), 
	 * digits (`0-9`) and underscores (`_`).
	 * 
	 * They may also contain pointers to specific object properties or associative array values from the given set of 
	 * parameters by using a dot between identifiers, such as `{{object.property}}`, with no limit on the number of 
	 * pointers chained.
	 * 
	 * If suffixed with opening and closing parenthesis, such as `{{object.method()}}`, then the given pointers are 
	 * interpreted as getter method calls, but they cannot be given any arguments.
	 * 
	 * @param \Dracodeum\Kit\Enums\Info\Level $info_level
	 * The info level to set with.
	 * 
	 * @return $this
	 * This instance, for chaining purposes.
	 */
	final public function setPluralString(string $string, EInfoLevel $info_level = EInfoLevel::ENDUSER)
	{
		$this->plural_strings[$info_level->value] = $string;
		return $this;
	}
	
	/**
	 * Get plural number.
	 * 
	 * @return float
	 * The plural number.
	 */
	final public function getPluralNumber(): float
	{
		return $this->plural_number;
	}
	
	/**
	 * Set plural number.
	 * 
	 * @param float $number
	 * The number to set.
	 * 
	 * @return $this
	 * This instance, for chaining purposes.
	 */
	final public function setPluralNumber(float $number)
	{
		$this->plural_number = $number;
		return $this;
	}
	
	/**
	 * Check if has plural number placeholder.
	 * 
	 * @return bool
	 * Boolean `true` if has plural number placeholder.
	 */
	final public function hasPluralNumberPlaceholder(): bool
	{
		return $this->plural_number_placeholder !== null;
	}
	
	/**
	 * Get plural number placeholder.
	 * 
	 * @return string|null
	 * The plural number placeholder, or `null` if none is set.
	 */
	final public function getPluralNumberPlaceholder(): ?string
	{
		return $this->plural_number_placeholder;
	}
	
	/**
	 * Set plural number placeholder.
	 * 
	 * @param string $placeholder
	 * The placeholder to set.
	 * 
	 * @return $this
	 * This instance, for chaining purposes.
	 */
	final public function setPluralNumberPlaceholder(string $placeholder)
	{
		$this->plural_number_placeholder = $placeholder;
		return $this;
	}
	
	/**
	 * Set parameter.
	 * 
	 * @param string $name
	 * The name to set with.
	 * 
	 * @param mixed $value
	 * The value to set with.
	 * 
	 * @return $this
	 * This instance, for chaining purposes.
	 */
	final public function setParameter(string $name, mixed $value)
	{
		$this->parameters[$name] = $value;
		return $this;
	}
	
	/**
	 * Set parameters.
	 * 
	 * @param array $parameters
	 * The parameters to set, as a set of `name => value` pairs.
	 * 
	 * @return $this
	 * This instance, for chaining purposes.
	 */
	final public function setParameters(array $parameters)
	{
		foreach ($parameters as $name => $value) {
			$this->parameters[$name] = $value;
		}
		return $this;
	}
	
	/**
	 * Set object.
	 * 
	 * @param object $object
	 * The object to set.
	 * 
	 * @return $this
	 * This instance, for chaining purposes.
	 */
	final public function setObject(object $object)
	{
		$this->object = $object;
		return $this;
	}
	
	/**
	 * Set stringifier.
	 * 
	 * @param callable $stringifier
	 * The function to use to stringify a given value.  
	 * It must be compatible with the following signature:  
	 * ```
	 * function (mixed $value, \Dracodeum\Kit\Options\Text $text_options): string
	 * ```
	 * 
	 * **Parameters:**
	 * - `mixed $value`  
	 *   The value to stringify.  
	 *   &nbsp;
	 * - `\Dracodeum\Kit\Options\Text $text_options`  
	 *   The text options instance to use.  
	 *   &nbsp;
	 * 
	 * **Return:** `string`  
	 * The stringified value.
	 * 
	 * @return $this
	 * This instance, for chaining purposes.
	 */
	final public function setStringifier(callable $stringifier)
	{
		UCall::assert('stringifier', $stringifier, function (mixed $value, TextOptions $text_options): string {});
		$this->stringifier = $stringifier;
		return $this;
	}
	
	/**
	 * Set placeholder as quoted.
	 * 
	 * @param string $placeholder
	 * The placeholder to set for.
	 * 
	 * @return $this
	 * This instance, for chaining purposes.
	 */
	final public function setPlaceholderAsQuoted(string $placeholder)
	{
		$this->setPlaceholderFlag($placeholder, self::FLAG_PLACEHOLDER_QUOTED);
		return $this;
	}
	
	/**
	 * Set placeholder stringifier.
	 * 
	 * @param string $placeholder
	 * The placeholder to set for.
	 * 
	 * @param callable $stringifier
	 * The function to use to stringify a given value.  
	 * It must be compatible with the following signature:  
	 * ```
	 * function (mixed $value, \Dracodeum\Kit\Options\Text $text_options): string
	 * ```
	 * 
	 * **Parameters:**
	 * - `mixed $value`  
	 *   The value to stringify.  
	 *   &nbsp;
	 * - `\Dracodeum\Kit\Options\Text $text_options`  
	 *   The text options instance to use.  
	 *   &nbsp;
	 * 
	 * **Return:** `string`  
	 * The stringified value.
	 * 
	 * @return $this
	 * This instance, for chaining purposes.
	 */
	final public function setPlaceholderStringifier(string $placeholder, callable $stringifier)
	{
		UCall::assert('stringifier', $stringifier, function (mixed $value, TextOptions $text_options): string {});
		$this->placeholders_stringifiers[$placeholder] = $stringifier;
		return $this;
	}
	
	/**
	 * Check if is localized.
	 * 
	 * @return bool
	 * Boolean `true` if is localized.
	 */
	final public function isLocalized(): bool
	{
		return $this->localized;
	}
	
	/**
	 * Set as localized.
	 * 
	 * @param string|null $context
	 * The context to set with.
	 * 
	 * @return $this
	 * This instance, for chaining purposes.
	 */
	final public function setAsLocalized(?string $context = null)
	{
		$this->localized = true;
		
		//TODO
		
		return $this;
	}
	
	/**
	 * Check if has texts.
	 * 
	 * @return bool
	 * Boolean `true` if has texts.
	 */
	final public function hasTexts(): bool
	{
		return (bool)$this->texts;
	}
	
	/**
	 * Get text instances.
	 * 
	 * @return \Dracodeum\Kit\Primitives\Text[]
	 * The text instances.
	 */
	final public function getTexts(): array
	{
		return $this->texts;
	}
	
	/**
	 * Prepend text.
	 * 
	 * @param coercible<\Dracodeum\Kit\Primitives\Text> $text
	 * The text to prepend.
	 * 
	 * @return $this
	 * This instance, for chaining purposes.
	 */
	final public function prependText($text)
	{
		$this->coerce($text);
		array_unshift($this->texts, $text);
		return $this;
	}
	
	/**
	 * Append text.
	 * 
	 * @param coercible<\Dracodeum\Kit\Primitives\Text> $text
	 * The text to append.
	 * 
	 * @return $this
	 * This instance, for chaining purposes.
	 */
	final public function appendText($text)
	{
		$this->coerce($text);
		$this->texts[] = $text;
		return $this;
	}
	
	/**
	 * Set texts strings stringifier.
	 * 
	 * @param callable $stringifier
	 * The function to use to stringify a given set of text strings.  
	 * It must be compatible with the following signature:  
	 * ```
	 * function (array $strings, \Dracodeum\Kit\Options\Text $text_options): string
	 * ```
	 * 
	 * **Parameters:**
	 * - `string[] $strings`  
	 *   The strings to stringify.  
	 *   &nbsp;
	 * - `\Dracodeum\Kit\Options\Text $text_options`  
	 *   The text options instance to use.  
	 *   &nbsp;
	 * 
	 * **Return:** `string`  
	 * The stringified value.
	 * 
	 * @return $this
	 * This instance, for chaining purposes.
	 */
	final public function setTextsStringsStringifier(callable $stringifier)
	{
		UCall::assert('stringifier', $stringifier, function (array $strings, TextOptions $text_options): string {});
		$this->texts_strings_stringifier = $stringifier;
		return $this;
	}
	
	
	
	//Final public static methods
	/**
	 * Build instance.
	 * 
	 * @param string|null $string
	 * The string to build with.
	 * 
	 * Placeholders may optionally be set in the given string as `{{placeholder}}` to be replaced by a corresponding 
	 * set of parameters, and they must be exclusively composed of identifiers, which are defined as words which must 
	 * start with a letter (`a-z` or `A-Z`) or underscore (`_`), and may only contain letters (`a-z` or `A-Z`), 
	 * digits (`0-9`) and underscores (`_`).
	 * 
	 * They may also contain pointers to specific object properties or associative array values from the given set of 
	 * parameters by using a dot between identifiers, such as `{{object.property}}`, with no limit on the number of 
	 * pointers chained.
	 * 
	 * If suffixed with opening and closing parenthesis, such as `{{object.method()}}`, then the given pointers are 
	 * interpreted as getter method calls, but they cannot be given any arguments.
	 * 
	 * @param \Dracodeum\Kit\Enums\Info\Level $info_level
	 * The info level to build with.
	 * 
	 * @return static
	 * The built instance.
	 */
	final public static function build(?string $string = null, EInfoLevel $info_level = EInfoLevel::ENDUSER): static
	{
		return new static($string, $info_level);
	}
	
	/**
	 * Coerce a given value into an instance.
	 * 
	 * //TODO: add @see towards Type prototype
	 * 
	 * @param mixed $value
	 * The value to coerce.
	 * 
	 * @param array $properties
	 * The properties to coerce with, as a set of `name => value` pairs.
	 * 
	 * @param bool $no_throw
	 * Do not throw an exception.
	 * 
	 * @throws \Dracodeum\Kit\Primitives\Text\Exceptions\CoercionFailed
	 * 
	 * @return bool
	 * Boolean `true` is always returned if the given value was successfully coerced into an instance, otherwise an 
	 * exception is thrown, unless `$no_throw` is set to boolean `true`, in which case boolean `false` is returned 
	 * instead.
	 */
	final public static function coerce(mixed &$value, array $properties = [], bool $no_throw = false): bool
	{
		//TODO: move this code to a Type prototype
		
		//coerce
		if (!($value instanceof self)) {
			if (UType::evaluateString($value)) {
				$value = self::build($value);
			} elseif ($no_throw) {
				return false;
			} else {
				throw new Exceptions\CoercionFailed([self::class, $value]);
			}
		}
		return true;
	}
	
	
	
	//Private methods
	/**
	 * Set placeholder flag.
	 * 
	 * @param string $placeholder
	 * The placeholder to set for.
	 * 
	 * @param int $flag
	 * The flag to set.
	 */
	private function setPlaceholderFlag(string $placeholder, int $flag): void
	{
		$this->placeholders_flags[$placeholder] ??= 0x0;
		UByte::setFlag($this->placeholders_flags[$placeholder], $flag);
	}
}
