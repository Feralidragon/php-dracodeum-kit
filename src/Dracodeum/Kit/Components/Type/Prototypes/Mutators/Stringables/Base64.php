<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components\Type\Prototypes\Mutators\Stringables;

use Dracodeum\Kit\Components\Type\Prototypes\Mutators\Stringable as Prototype;
use Dracodeum\Kit\Components\Type\Prototypes\Mutator\Interfaces\ExplanationProducer as IExplanationProducer;
use Dracodeum\Kit\Primitives\Text;
use Dracodeum\Kit\Enums\Info\Level as EInfoLevel;
use Dracodeum\Kit\Traits\LazyProperties\Property;
use Dracodeum\Kit\Utilities\Base64 as UBase64;

/**
 * This prototype restricts a given stringable value to a Base64 format.
 * 
 * @property-write bool|null $url_safe [writeonce] [transient] [default = null]  
 * If set to boolean `true`, then only allow the URL-safe format.  
 * If set to boolean `false`, then disallow the URL-safe format.  
 * If not set, then any format is allowed.
 */
class Base64 extends Prototype implements IExplanationProducer
{
	//Protected properties
	protected ?bool $url_safe = null;
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function process(mixed &$value)
	{
		return UBase64::encoded($value, $this->url_safe);
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Components\Type\Prototypes\Mutator\Interfaces\ExplanationProducer)
	/** {@inheritdoc} */
	public function produceExplanation()
	{
		//text
		$text = Text::build()
			->setParameters([
				'letters' => ['a' => 'a', 'z' => 'z', 'A' => 'A', 'Z' => 'Z'],
				'digits' => ['num0' => '0', 'num9' => '9'],
				'signs' => ['plus' => '+', 'minus' => '-', 'equal' => '='],
				'underscore' => '_',
				'slash' => '/'
			])
			->setAsLocalized(self::class)
		;
		
		//return
		return match ($this->url_safe) {
			true
				=> $text
					->setString(
						"Only the URL-safe Base64 format is allowed, " . 
							"which must only be composed of alphanumeric ({{letters.a}}-{{letters.z}}, " . 
							"{{letters.A}}-{{letters.Z}} and {{digits.num0}}-{{digits.num9}}), " . 
							"minus sign ({{signs.minus}}) and underscore ({{underscore}}) characters, " . 
							"as groups of 2 to 4 characters."
					)
					->setString(
						"Only the URL-safe Base64 format is allowed, " . 
							"which must only be composed of ASCII alphanumeric ({{letters.a}}-{{letters.z}}, " . 
							"{{letters.A}}-{{letters.Z}} and {{digits.num0}}-{{digits.num9}}), " . 
							"minus sign ({{signs.minus}}) and underscore ({{underscore}}) characters, " . 
							"as groups of 2 to 4 characters.",
						EInfoLevel::TECHNICAL
					)
				,
			false
				=> $text
					->setString(
						"Only the Base64 format is allowed, " . 
							"which must only be composed of alphanumeric ({{letters.a}}-{{letters.z}}, " . 
							"{{letters.A}}-{{letters.Z}} and {{digits.num0}}-{{digits.num9}}), " . 
							"plus sign ({{signs.plus}}) and slash ({{slash}}) characters, " . 
							"as groups of 2 to 4 characters, optionally ending with equal signs ({{signs.equal}})."
					)
					->setString(
						"Only the Base64 format is allowed, " . 
							"which must only be composed of ASCII alphanumeric ({{letters.a}}-{{letters.z}}, " . 
							"{{letters.A}}-{{letters.Z}} and {{digits.num0}}-{{digits.num9}}), " . 
							"plus sign ({{signs.plus}}) and slash ({{slash}}) characters, " . 
							"as groups of 2 to 4 characters, optionally padded with equal signs ({{signs.equal}}).",
						EInfoLevel::TECHNICAL
					)
				,
			default
				=> $text
					->setString(
						"Only the Base64 format is allowed, " . 
							"which must only be composed of alphanumeric ({{letters.a}}-{{letters.z}}, " . 
							"{{letters.A}}-{{letters.Z}} and {{digits.num0}}-{{digits.num9}}), " . 
							"plus sign ({{signs.plus}}) and slash ({{slash}}) characters, " . 
							"as groups of 2 to 4 characters, optionally ending with equal signs ({{signs.equal}}), " . 
							"or minus sign ({{signs.minus}}) and underscore ({{underscore}}) characters, " . 
							"instead of plus sign and slash, respectively, with no equal signs, for URL-safe Base64."
					)
					->setString(
						"Only the Base64 format is allowed, " . 
							"which must only be composed of ASCII alphanumeric ({{letters.a}}-{{letters.z}}, " . 
							"{{letters.A}}-{{letters.Z}} and {{digits.num0}}-{{digits.num9}}), " . 
							"plus sign ({{signs.plus}}) and slash ({{slash}}) characters, " . 
							"as groups of 2 to 4 characters, optionally padded with equal signs ({{signs.equal}}), " . 
							"or minus sign ({{signs.minus}}) and underscore ({{underscore}}) characters, " . 
							"instead of plus sign and slash, respectively, with no equal signs, for URL-safe Base64.",
						EInfoLevel::TECHNICAL
					)
		};
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Prototype\Traits\PropertyBuilder)
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		return match ($name) {
			'url_safe' => $this->createProperty()->setMode('w--')->setAsBoolean(true)->bind(self::class),
			default => null
		};
	}
}
