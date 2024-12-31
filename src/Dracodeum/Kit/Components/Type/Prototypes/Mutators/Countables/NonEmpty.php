<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components\Type\Prototypes\Mutators\Countables;

use Dracodeum\Kit\Components\Type\Prototypes\Mutators\Countable as Prototype;
use Dracodeum\Kit\Components\Type\Prototypes\Mutator\Interfaces\ExplanationProducer as IExplanationProducer;
use Dracodeum\Kit\Primitives\Text;

/** This prototype prevents a given countable value from being empty. */
class NonEmpty extends Prototype implements IExplanationProducer
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function process(mixed &$value)
	{
		return count($value) > 0;
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Components\Type\Prototypes\Mutator\Interfaces\ExplanationProducer)
	/** {@inheritdoc} */
	public function produceExplanation()
	{
		return Text::build("Cannot be empty.")->setAsLocalized(self::class);
	}
}
