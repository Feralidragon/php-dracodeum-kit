<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components\Type\Prototypes\Mutators\Stringable;

use Dracodeum\Kit\Components\Type\Prototypes\Mutator as Prototype;
use Dracodeum\Kit\Components\Type\Prototypes\Mutator\Interfaces\ExplanationProducer as IExplanationProducer;
use Dracodeum\Kit\Primitives\Text;

/** This prototype restricts a given stringable value to hexadecimal characters. */
class Hexadecimal extends Prototype implements IExplanationProducer
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function process(mixed &$value)
	{
		if (preg_match('/^[\da-f]*$/i', $value)) {
			$value = strtolower($value);
			return true;
		}
		return false;
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Components\Type\Prototypes\Mutator\Interfaces\ExplanationProducer)
	/** {@inheritdoc} */
	public function produceExplanation()
	{
		return Text::build()
			->setString(
				"Only hexadecimal characters ({{digits.num0}}-{{digits.num9}}, " . 
					"{{letters.a}}-{{letters.f}} and {{letters.A}}-{{letters.F}}) are allowed."
			)
			->setParameters([
				'letters' => ['a' => 'a', 'f' => 'f', 'A' => 'A', 'F' => 'F'],
				'digits' => ['num0' => '0', 'num9' => '9']
			])
			->setAsLocalized(self::class)
		;
	}
}
