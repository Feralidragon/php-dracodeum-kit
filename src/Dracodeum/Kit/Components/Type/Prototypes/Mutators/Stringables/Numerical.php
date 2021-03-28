<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components\Type\Prototypes\Mutators\Stringables;

use Dracodeum\Kit\Components\Type\Prototypes\Mutators\Stringable as Prototype;
use Dracodeum\Kit\Components\Type\Prototypes\Mutator\Interfaces\ExplanationProducer as IExplanationProducer;
use Dracodeum\Kit\Primitives\Text;
use Dracodeum\Kit\Enumerations\InfoLevel as EInfoLevel;
use Dracodeum\Kit\Traits\LazyProperties\Property;

/**
 * This prototype restricts a given stringable value to numerical characters.
 * 
 * @property-write bool $unicode [writeonce] [transient] [default = false]  
 * Check as Unicode.
 */
class Numerical extends Prototype implements IExplanationProducer
{
	//Protected properties
	protected bool $unicode = false;
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function process(mixed &$value)
	{
		return (bool)preg_match($this->unicode ? '/^\pN*$/u' : '/^\d*$/', $value);
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Components\Type\Prototypes\Mutator\Interfaces\ExplanationProducer)
	/** {@inheritdoc} */
	public function produceExplanation()
	{
		$text = Text::build()->setAsLocalized(self::class);
		return $this->unicode
			? $text->setString("Only numeric characters are allowed.")
			: $text
				->setString("Only numeric characters ({{digits.num0}}-{{digits.num9}}) are allowed.")
				->setString(
					"Only ASCII numeric characters ({{digits.num0}}-{{digits.num9}}) are allowed.",
					EInfoLevel::TECHNICAL
				)
				->setParameter('digits', ['num0' => '0', 'num9' => '9'])
			;
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
