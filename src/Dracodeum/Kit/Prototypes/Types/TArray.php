<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Prototypes\Types;

use Dracodeum\Kit\Prototypes\Type as Prototype;
use Dracodeum\Kit\Prototypes\Type\Interfaces\{
	Textifier as ITextifier,
	MutatorProducer as IMutatorProducer
};
use Dracodeum\Kit\Components\Type;
use Dracodeum\Kit\Interfaces\Arrayable as IArrayable;
use Dracodeum\Kit\Components\Type\Enumerations\Context as EContext;
use Dracodeum\Kit\Primitives\{
	Error,
	Text
};
use Dracodeum\Kit\Components\Type\Prototypes\Mutators\Countables as CountableMutators;
use Dracodeum\Kit\Options\Text as TextOptions;
use Dracodeum\Kit\Traits\LazyProperties\Property;
use Dracodeum\Kit\Enumerations\InfoLevel as EInfoLevel;
use Dracodeum\Kit\Utilities\{
	Data as UData,
	Text as UText
};

/**
 * This prototype represents an array.
 * 
 * Only the following types of values are allowed to be coerced into an array:
 * - an array;
 * - an arrayable object, as an object implementing the `Dracodeum\Kit\Interfaces\Arrayable` interface;
 * - a string, as a comma separated list of values, optionally with whitespace, such as `value1,value2,value3` or 
 * `value1, value2, value3` (when any non-internal context is used);
 * - a string, as a comma separated list of colon separated key-value pairs, optionally with whitespace, 
 * such as `key1:value1,key2:value2,key3:value3` or `key1: value1, key2: value2, key3: value3` (when any non-internal 
 * context is used).
 * 
 * @property-write \Dracodeum\Kit\Components\Type|null $type [writeonce] [transient] [default = null]  
 * The type instance to use.
 * 
 * @property-write \Dracodeum\Kit\Components\Type|null $key_type [writeonce] [transient] [default = null]  
 * The key type instance to use (associative arrays only).
 * 
 * @property-write bool $non_associative [writeonce] [transient] [default = false]  
 * Restrict to a non-associative array.
 * 
 * @see \Dracodeum\Kit\Interfaces\Arrayable
 */
class TArray extends Prototype implements ITextifier, IMutatorProducer
{
	//Protected properties
	protected ?Type $type = null;
	
	protected ?Type $key_type = null;
	
	protected bool $non_associative = false;
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function process(mixed &$value, $context): ?Error
	{
		//initialize
		$type = $this->type;
		$key_type = $this->key_type;
		$associative = !$this->non_associative;
		
		//process
		$array = null;
		if (is_array($value)) {
			$array = $value;
		} elseif (is_string($value) && $context !== EContext::INTERNAL) {
			$array = [];
			if (!UText::empty($value, true)) {
				foreach (explode(',', $value) as $i => $v) {
					if ($associative && preg_match('/^(?P<key>[^:]*):(?P<value>.*)$/s', $v, $matches)) {
						$key = trim($matches['key']);
						if (array_key_exists($key, $array)) {
							$text = Text::build()
								->setString("Duplicated key {{key}} found at position {{position}}.")
								->setParameters([
									'key' => $key,
									'position' => $i + 1
								])
								->setPlaceholderAsQuoted('key')
								->setAsLocalized(self::class)
							;
							return Error::build(text: $text);
						}
						$array[$key] = trim($matches['value']);
					} else {
						$array[] = trim($v);
					}
				}
			}
		} elseif ($value instanceof IArrayable) {
			$array = $value->toArray();
		}
		
		//check
		if ($array === null || (!$associative && UData::associative($array))) {
			$text = Text::build()->setAsLocalized(self::class);
			if ($context === EContext::INTERNAL) {
				//internal
				$text->setString(
					"Only the following types of values are allowed to be coerced into an array:\n" . 
						" - an array;\n" . 
						" - an arrayable object, as an object implementing the " . 
						"\"Dracodeum\Kit\Interfaces\Arrayable\" interface.",
					EInfoLevel::INTERNAL
				);
				
				//non-internal
				if ($associative) {
					$text
						->setString("Only a list of key-value pairs is allowed.")
						->setString("Only an associative array is allowed.", EInfoLevel::TECHNICAL)
					;
				} else {
					$text
						->setString("Only a list of values is allowed.")
						->setString("Only a non-associative array is allowed.", EInfoLevel::TECHNICAL)
					;
				}
				
			} elseif ($associative) {
				$text
					->setString(
						"Only a list of key-value pairs is allowed, " . 
							"which may be given as a comma separated list of colon separated key-value pairs."
					)
					->setString(
						"Only an associative array is allowed, " . 
							"which may be given as a comma separated list of colon separated key-value pairs.",
						EInfoLevel::TECHNICAL
					)
					->setString(
						"Only the following types of values are allowed to be coerced into an array:\n" . 
							" - an array;\n" . 
							" - an arrayable object, as an object implementing the " . 
							"\"Dracodeum\Kit\Interfaces\Arrayable\" interface;\n" . 
							" - a string, as a comma separated list of values, optionally with whitespace, " . 
							"such as \"value1,value2,value3\" or \"value1, value2, value3\";\n" . 
							" - a string, as a comma separated list of colon separated key-value pairs, " . 
							"optionally with whitespace, such as \"key1:value1,key2:value2,key3:value3\" or " . 
							"\"key1: value1, key2: value2, key3: value3\".",
						EInfoLevel::INTERNAL
					)
				;
			} else {
				$text
					->setString("Only a list of values is allowed, which may be given as a comma separated list.")
					->setString(
						"Only a non-associative array is allowed, which may be given as a comma separated list.",
						EInfoLevel::TECHNICAL
					)
					->setString(
						"Only the following types of values are allowed to be coerced into an array:\n" . 
							" - an array;\n" . 
							" - an arrayable object, as an object implementing the " . 
							"\"Dracodeum\Kit\Interfaces\Arrayable\" interface;\n" . 
							" - a string, as a comma separated list of values, optionally with whitespace, " . 
							"such as \"value1,value2,value3\" or \"value1, value2, value3\".",
						EInfoLevel::INTERNAL
					)
				;
			}
			return Error::build(text: $text);
		}
		
		//error stringifier
		$error_stringifier = function (mixed $value, TextOptions $text_options): string {
			return UText::uncapitalize(
				$value instanceof Text
					? $value->toString($text_options)
					: UText::localize("Unknown error.", self::class, $text_options),
				true
			);
		};
		
		//key type
		if ($associative && $key_type !== null) {
			$i = 0;
			$a = [];
			foreach ($array as $k => $v) {
				$error = $key_type->process($k, $context);
				if ($error === null) {
					if (array_key_exists($k, $a)) {
						$text = Text::build()
							->setString("Duplicated key {{key}} found at position {{position}}.")
							->setParameters([
								'key' => $k,
								'position' => $i + 1
							])
							->setPlaceholderAsQuoted('key')
							->setAsLocalized(self::class)
						;
						return Error::build(text: $text);
					}
					$a[$k] = $v;
				} else {
					$text = Text::build()
						->setString("Invalid key {{key}} at position {{position}}: {{error}}")
						->setParameters([
							'key' => $k,
							'position' => $i + 1,
							'error' => $error->getText()
						])
						->setPlaceholderAsQuoted('key')
						->setPlaceholderStringifier('error', $error_stringifier)
						->setAsLocalized(self::class)
					;
					return Error::build(text: $text);
				}
				$i++;
			}
			$array = $a;
			unset($a);
		}
		
		//type
		if ($type !== null) {
			$i = 0;
			foreach ($array as $k => &$v) {
				$error = $type->process($v, $context);
				if ($error !== null) {
					//text
					$text = Text::build()
						->setParameters([
							'key' => $k,
							'index' => $i,
							'position' => $i + 1,
							'error' => $error->getText()
						])
						->setPlaceholderAsQuoted('key')
						->setPlaceholderStringifier('error', $error_stringifier)
						->setAsLocalized(self::class)
					;
					
					//associative
					if ($associative) {
						$text->setString("Invalid value at key {{key}}: {{error}}");
					} else {
						$text
							->setString("Invalid value at position {{position}}: {{error}}")
							->setString("Invalid value at index {{index}}: {{error}}", EInfoLevel::TECHNICAL)
						;
					}
					
					//return
					return Error::build(text: $text);
				}
				$i++;
			}
			unset($v);
		}
		
		//finalize
		$value = $array;
		
		//return
		return null;
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Prototypes\Type\Interfaces\Textifier)
	/** {@inheritdoc} */
	public function textify(mixed $value)
	{
		//initialize
		$type = $this->type;
		$key_type = $this->key_type;
		$associative = !$this->non_associative;
		
		//key texts
		$key_texts = [];
		if ($associative) {
			foreach ($value as $k => $v) {
				if ($key_type !== null) {
					$key_texts[] = $key_type->textify($k);
				} else {
					Text::coerce($k);
					$key_texts[] = $k;
				}
			}
		}
		
		//value texts
		$value_texts = [];
		foreach ($value as $v) {
			if ($type !== null) {
				$value_texts[] = $type->textify($v);
			} else {
				Text::coerce($v);
				$value_texts[] = $v;
			}
		}
		
		//items text
		$items_text = Text::build()
			->setTextsStringsStringifier(
				function (array $strings, TextOptions $text_options) use ($associative): string {
					//multiline (check)
					$multiline = $associative;
					if (!$multiline) {
						foreach ($strings as $string) {
							$multiline = UText::multiline($string);
							if ($multiline) {
								break;
							}
						}
					}
					
					//multiline (process)
					if ($multiline) {
						$prev_multi = false;
						foreach ($strings as $i => &$string) {
							$multi = UText::multiline($string);
							if ($i > 0 && ($multi || $prev_multi)) {
								$string = "\n{$string}";
							}
							$prev_multi = $multi;
						}
						unset($string);
					}
					
					//string
					$string = implode($multiline ? ",\n" : ", ", $strings);
					if ($multiline && $text_options->info_level !== EInfoLevel::ENDUSER) {
						$string = "\n" . UText::indentate($string) . "\n";
					}
					
					//return
					return $string;
				}
			)
		;
		
		//value stringifier
		$value_stringifier = null;
		if ($associative) {
			$value_stringifier = function (mixed $value, TextOptions $text_options): string {
				$string = $value->toString($text_options);
				if (UText::multiline($string)) {
					$string = "\n" . UText::indentate($string);
				}
				return $string;
			};
		}
		
		//item texts
		foreach ($value_texts as $i => $value_text) {
			$item_text = $value_text;
			if ($associative) {
				$item_text = Text::build("{{key}}: {{value}}")
					->setParameters([
						'key' => $key_texts[$i],
						'value' => $value_text
					])
					->setPlaceholderStringifier('value', $value_stringifier)
				;
			}
			$items_text->appendText($item_text);
		}
		
		//return
		return Text::build("{{items}}")
			->setString($associative ? "{{{items}}}" : "[{{items}}]", EInfoLevel::TECHNICAL)
			->setParameter('items', $items_text)
		;
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Prototypes\Type\Interfaces\MutatorProducer)
	/** {@inheritdoc} */
	public function produceMutator(string $name, array $properties)
	{
		return match ($name) {
			'non_empty' => CountableMutators\NonEmpty::class,
			default => null
		};
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Prototype\Traits\PropertyBuilder)
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		return match ($name) {
			'type', 'key_type'
				=> $this->createProperty()
					->setMode('w--')
					->setAsComponent(Type::class, nullable: true)
					->bind(self::class)
				,
			'non_associative' => $this->createProperty()->setMode('w--')->setAsBoolean()->bind(self::class),
			default => null
		};
	}
}
