<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components\Type\Prototypes\Mutators\Numericals;

use Dracodeum\Kit\Components\Type\Prototypes\Mutators\Numerical as Prototype;
use Dracodeum\Kit\Components\Type\Prototypes\Mutator\Interfaces\ExplanationProducer as IExplanationProducer;
use Dracodeum\Kit\Primitives\Text;
use Dracodeum\Kit\Traits\LazyProperties\Property;

/**
 * This prototype restricts a given numeric value to a minimum number.
 * 
 * @property-write int|float $number [writeonce] [transient]  
 * The number to restrict to.
 * 
 * @property-write bool $exclusive [writeonce] [transient] [default = false]  
 * Check as exclusive: do not allow a given value to be equal to the given number.
 */
class Minimum extends Prototype implements IExplanationProducer
{
	//Protected properties
	protected int|float $number;
	
	protected bool $exclusive = false;
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function process(mixed &$value)
	{
		return $this->exclusive ? $value > $this->number : $value >= $this->number;
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Components\Type\Prototypes\Mutator\Interfaces\ExplanationProducer)
	/** {@inheritdoc} */
	public function produceExplanation()
	{
		$text = Text::build()->setParameter('number', $this->number)->setAsLocalized(self::class);
		return $this->exclusive
			? $text->setString("Must be greater than {{number}}.")
			: $text->setString("Must be greater than or equal to {{number}}.");
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Prototype\Traits\PropertiesInitializer)
	/** {@inheritdoc} */
	protected function initializeProperties(): void
	{
		$this->addRequiredPropertyName('number');
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Prototype\Traits\PropertyBuilder)
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		return match ($name) {
			'number' => $this->createProperty()->setMode('w--')->setAsNumber()->bind(self::class),
			'exclusive' => $this->createProperty()->setMode('w--')->setAsBoolean()->bind(self::class),
			default => null
		};
	}
}
