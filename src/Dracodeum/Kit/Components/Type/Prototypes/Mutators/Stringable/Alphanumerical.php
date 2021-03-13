<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components\Type\Prototypes\Mutators\Stringable;

use Dracodeum\Kit\Components\Type\Prototypes\Mutator as Prototype;
use Dracodeum\Kit\Components\Type\Prototypes\Mutator\Interfaces\ExplanationProducer as IExplanationProducer;
use Dracodeum\Kit\Primitives\Text;
use Dracodeum\Kit\Enumerations\{
	InfoLevel as EInfoLevel,
	TextCase as ETextCase
};
use Dracodeum\Kit\Traits\LazyProperties\Property;

/**
 * This prototype restricts a given stringable value to alphanumerical characters.
 * 
 * @property-write enum<\Dracodeum\Kit\Enumerations\TextCase>|null $case [writeonce] [transient] [default = null]  
 * The case to restrict to.
 * 
 * @property-write bool $unicode [writeonce] [transient] [default = false]  
 * Check as Unicode.
 */
class Alphanumerical extends Prototype implements IExplanationProducer
{
	//Protected properties
	/** @var enum<\Dracodeum\Kit\Enumerations\TextCase>|null */
	protected $case = null;
	
	protected bool $unicode = false;
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function process(mixed &$value)
	{
		return (bool)preg_match(match ($this->case) {
			ETextCase::LOWER => $this->unicode ? '/^[\p{Ll}\p{Lm}\p{Lo}\pN]*$/u' : '/^[a-z\d]*$/',
			ETextCase::UPPER => $this->unicode ? '/^[\p{Lu}\p{Lm}\p{Lo}\pN]*$/u' : '/^[A-Z\d]*$/',
			default => $this->unicode ? '/^[\pL\pN]*$/u' : '/^[a-z\d]*$/i'
		}, $value);
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Components\Type\Prototypes\Mutator\Interfaces\ExplanationProducer)
	/** {@inheritdoc} */
	public function produceExplanation()
	{
		//text
		$text = Text::build()->setAsLocalized(self::class);
		
		//unicode
		if ($this->unicode) {
			return match ($this->case) {
				ETextCase::LOWER => $text->setString("Only lowercase alphanumeric characters are allowed."),
				ETextCase::UPPER => $text->setString("Only uppercase alphanumeric characters are allowed."),
				default => $text->setString("Only alphanumeric characters are allowed.")
			};
		}
		
		//return
		return match ($this->case) {
			ETextCase::LOWER
				=> $text
					->setString(
						"Only lowercase alphanumeric characters ({{letters.a}}-{{letters.z}} and " . 
							"{{digits.num0}}-{{digits.num9}}) are allowed."
					)
					->setString(
						"Only ASCII lowercase alphanumeric characters ({{letters.a}}-{{letters.z}} and " . 
							"{{digits.num0}}-{{digits.num9}}) are allowed.",
						EInfoLevel::TECHNICAL
					)
					->setParameters([
						'letters' => ['a' => 'a', 'z' => 'z'],
						'digits' => ['num0' => '0', 'num9' => '9']
					])
				,
			ETextCase::UPPER
				=> $text
					->setString(
						"Only uppercase alphanumeric characters ({{letters.A}}-{{letters.Z}} and " . 
							"{{digits.num0}}-{{digits.num9}}) are allowed."
					)
					->setString(
						"Only ASCII uppercase alphanumeric characters ({{letters.A}}-{{letters.Z}} and " . 
							"{{digits.num0}}-{{digits.num9}}) are allowed.",
						EInfoLevel::TECHNICAL
					)
					->setParameters([
						'letters' => ['A' => 'A', 'Z' => 'Z'],
						'digits' => ['num0' => '0', 'num9' => '9']
					])
				,
			default
				=> $text
					->setString(
						"Only alphanumeric characters ({{letters.a}}-{{letters.z}}, " . 
							"{{letters.A}}-{{letters.Z}} and {{digits.num0}}-{{digits.num9}}) are allowed."
					)
					->setString(
						"Only ASCII alphanumeric characters ({{letters.a}}-{{letters.z}}, " . 
							"{{letters.A}}-{{letters.Z}} and {{digits.num0}}-{{digits.num9}}) are allowed.",
						EInfoLevel::TECHNICAL
					)
					->setParameters([
						'letters' => ['a' => 'a', 'z' => 'z', 'A' => 'A', 'Z' => 'Z'],
						'digits' => ['num0' => '0', 'num9' => '9']
					])
		};
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
			'unicode' => $this->createProperty()->setMode('w--')->setAsBoolean()->bind(self::class),
			default => null
		};
	}
}
