<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components\Type\Prototypes\Mutators\Countables;

use Dracodeum\Kit\Components\Type\Prototypes\Mutators\Countable as Prototype;
use Dracodeum\Kit\Components\Type\Prototypes\Mutator\Interfaces\ExplanationProducer as IExplanationProducer;
use Dracodeum\Kit\Primitives\Text;
use Dracodeum\Kit\Traits\LazyProperties\Property;

/**
 * This prototype restricts a given countable value to an exact count.
 * 
 * @property-write int $count [writeonce] [transient]  
 * The count to restrict to.
 */
class Count extends Prototype implements IExplanationProducer
{
	//Protected properties
	protected int $count;
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function process(mixed &$value)
	{
		return count($value) === $this->count;
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Components\Type\Prototypes\Mutator\Interfaces\ExplanationProducer)
	/** {@inheritdoc} */
	public function produceExplanation()
	{
		return Text::build()
			->setString("Only exactly {{count}} value is allowed.")
			->setPluralString("Only exactly {{count}} values are allowed.")
			->setPluralNumberPlaceholder('count')
			->setPluralNumber($this->count)
			->setAsLocalized(self::class)
		;
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Prototype\Traits\PropertiesInitializer)
	/** {@inheritdoc} */
	protected function initializeProperties(): void
	{
		$this->addRequiredPropertyName('count');
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Prototype\Traits\PropertyBuilder)
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		return match ($name) {
			'count' => $this->createProperty()->setMode('w--')->setAsInteger(true)->bind(self::class),
			default => null
		};
	}
}
