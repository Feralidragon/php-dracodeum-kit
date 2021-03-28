<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components\Type\Prototypes\Mutators\Stringables;

use Dracodeum\Kit\Components\Type\Prototypes\Mutators\Stringable as Prototype;
use Dracodeum\Kit\Components\Type\Prototypes\Mutator\Interfaces\ExplanationProducer as IExplanationProducer;
use Dracodeum\Kit\Primitives\Text;
use Dracodeum\Kit\Enumerations\{
	InfoLevel as EInfoLevel,
	TextCase as ETextCase
};
use Dracodeum\Kit\Options\Text as TextOptions;
use Dracodeum\Kit\Traits\LazyProperties\Property;
use Dracodeum\Kit\Utilities\Text as UText;

/**
 * This prototype restricts a given stringable value to an identifier format.
 * 
 * @property-write enum<\Dracodeum\Kit\Enumerations\TextCase>|null $case [writeonce] [transient] [default = null]  
 * The case to restrict to.
 * 
 * @property-write bool $extended [writeonce] [transient] [default = false]  
 * Use the extended format, in which dots may be used as delimiters between words (pointers).
 */
class Identifier extends Prototype implements IExplanationProducer
{
	//Protected properties
	/** @var enum<\Dracodeum\Kit\Enumerations\TextCase>|null */
	protected $case = null;
	
	protected bool $extended = false;
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function process(mixed &$value)
	{
		if (UText::isIdentifier($value, $this->extended)) {
			return match ($this->case) {
				ETextCase::LOWER => strtolower($value) === $value,
				ETextCase::UPPER => strtoupper($value) === $value,
				default => true
			};
		}
		return false;
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Components\Type\Prototypes\Mutator\Interfaces\ExplanationProducer)
	/** {@inheritdoc} */
	public function produceExplanation()
	{
		//text
		$text = Text::build()
			->setString("Only the following format is allowed:")
			->setString("Only an identifier with the following format is allowed:", EInfoLevel::TECHNICAL)
			->setAsLocalized(self::class)
			->setTextsStringsStringifier(function (array $strings, TextOptions $text_options): string {
				return UText::mbulletify($strings, $text_options, ['merge' => true, 'punctuate' => true]);
			})
		;
		
		//text 1
		$text1 = Text::build()
			->setParameters([
				'letters' => ['a' => 'a', 'z' => 'z', 'A' => 'A', 'Z' => 'Z'],
				'underscore' => '_'
			])
			->setAsLocalized(self::class)
		;
		$text->appendText(match ($this->case) {
			ETextCase::LOWER
				=> $text1
					->setString(
						"must start with a lowercase letter ({{letters.a}}-{{letters.z}}) or " . 
							"underscore ({{underscore}})"
					)
					->setString(
						"must start with an ASCII lowercase letter ({{letters.a}}-{{letters.z}}) or " . 
							"underscore ({{underscore}})",
						EInfoLevel::TECHNICAL
					)
				,
			ETextCase::UPPER
				=> $text1
					->setString(
						"must start with an uppercase letter ({{letters.A}}-{{letters.Z}}) or " . 
							"underscore ({{underscore}})"
					)
					->setString(
						"must start with an ASCII uppercase letter ({{letters.A}}-{{letters.Z}}) or " . 
							"underscore ({{underscore}})",
						EInfoLevel::TECHNICAL
					)
				,
			default
				=> $text1
					->setString(
						"must start with a letter ({{letters.a}}-{{letters.z}} or " . 
							"{{letters.A}}-{{letters.Z}}) or underscore ({{underscore}})",
					)
					->setString(
						"must start with an ASCII letter ({{letters.a}}-{{letters.z}} or " . 
							"{{letters.A}}-{{letters.Z}}) or underscore ({{underscore}})",
						EInfoLevel::TECHNICAL
					)
		});
		
		//text 2
		$text2 = Text::build()
			->setParameters([
				'letters' => ['a' => 'a', 'z' => 'z', 'A' => 'A', 'Z' => 'Z'],
				'digits' => ['num0' => '0', 'num9' => '9'],
				'underscore' => '_'
			])
			->setAsLocalized(self::class)
		;
		$text->appendText(match ($this->case) {
			ETextCase::LOWER
				=> $text2
					->setString(
						"must only be composed of lowercase letters ({{letters.a}}-{{letters.z}}), " . 
							"digits ({{digits.num0}}-{{digits.num9}}) and underscores ({{underscore}})"
					)
					->setString(
						"must only be composed of ASCII lowercase letters ({{letters.a}}-{{letters.z}}), " . 
							"digits ({{digits.num0}}-{{digits.num9}}) and underscores ({{underscore}})",
						EInfoLevel::TECHNICAL
					)
				,
			ETextCase::UPPER
				=> $text2
					->setString(
						"must only be composed of uppercase letters ({{letters.A}}-{{letters.Z}}), " . 
							"digits ({{digits.num0}}-{{digits.num9}}) and underscores ({{underscore}})"
					)
					->setString(
						"must only be composed of ASCII uppercase letters ({{letters.A}}-{{letters.Z}}), " . 
							"digits ({{digits.num0}}-{{digits.num9}}) and underscores ({{underscore}})",
						EInfoLevel::TECHNICAL
					)
				,
			default
				=> $text2
					->setString(
						"must only be composed of letters ({{letters.a}}-{{letters.z}} and " . 
							"{{letters.A}}-{{letters.Z}}), digits ({{digits.num0}}-{{digits.num9}}) and " . 
							"underscores ({{underscore}})"
					)
					->setString(
						"must only be composed of ASCII letters ({{letters.a}}-{{letters.z}} and " . 
							"{{letters.A}}-{{letters.Z}}), digits ({{digits.num0}}-{{digits.num9}}) and " . 
							"underscores ({{underscore}})",
						EInfoLevel::TECHNICAL
					)
		});
		
		//extended
		if ($this->extended) {
			$text->appendText(
				Text::build()
					->setString("dots ({{dot}}) may also be used, as delimiters between words")
					->setString(
						"dots ({{dot}}) may also be used, as delimiters between words (pointers)", EInfoLevel::TECHNICAL
					)
					->setParameter('dot', '.')
					->setAsLocalized(self::class)
			);
		}
		
		//return
		return $text;
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Prototype\Traits\PropertyBuilder)
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		return match ($name) {
			'case'
				=> $this->createProperty()
					->setMode('w--')
					->setAsEnumerationValue(ETextCase::class, true)
					->bind(self::class)
				,
			'extended' => $this->createProperty()->setMode('w--')->setAsBoolean()->bind(self::class),
			default => null
		};
	}
}
