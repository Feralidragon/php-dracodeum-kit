<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components\Type\Prototypes\Mutators\Numericals;

use Dracodeum\Kit\Components\Type\Prototypes\Mutators\Numerical as Prototype;
use Dracodeum\Kit\Components\Type\Prototypes\Mutator\Interfaces\ExplanationProducer as IExplanationProducer;
use Dracodeum\Kit\Primitives\Text;

/** This prototype restricts a given numeric value to an even number. */
class Even extends Prototype implements IExplanationProducer
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function process(mixed &$value)
	{
		$v = $value;
		if (is_float($v)) {
			if ($v !== floor($v)) {
				return false;
			}
			$v = (int)$v;
		}
		return $v % 2 === 0;
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Components\Type\Prototypes\Mutator\Interfaces\ExplanationProducer)
	/** {@inheritdoc} */
	public function produceExplanation()
	{
		return Text::build("Must be even.")->setAsLocalized(self::class);
	}
}
