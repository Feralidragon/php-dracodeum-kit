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
 * This prototype restricts a given countable value to a count range.
 * 
 * @property-write int $min_count [writeonce] [transient]  
 * The minimum count to restrict to.
 * 
 * @property-write int $max_count [writeonce] [transient]  
 * The maximum count to restrict to.
 */
class CountRange extends Prototype implements IExplanationProducer
{
	//Protected properties
	protected int $min_count;
	
	protected int $max_count;
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function process(mixed &$value)
	{
		$count = count($value);
		return $count >= $this->min_count && $count <= $this->max_count;
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Components\Type\Prototypes\Mutator\Interfaces\ExplanationProducer)
	/** {@inheritdoc} */
	public function produceExplanation()
	{
		return Text::build()
			->setString("Only between {{min_count}} and {{max_count}} value is allowed.")
			->setPluralString("Only between {{min_count}} and {{max_count}} values are allowed.")
			->setPluralNumberPlaceholder('max_count')
			->setPluralNumber($this->max_count)
			->setParameter('min_count', $this->min_count)
			->setAsLocalized(self::class)
		;
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Prototype\Traits\PropertiesInitializer)
	/** {@inheritdoc} */
	protected function initializeProperties(): void
	{
		$this->addRequiredPropertyNames(['min_count', 'max_count']);
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Prototype\Traits\PropertyBuilder)
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		return match ($name) {
			'min_count', 'max_count' => $this->createProperty()->setMode('w--')->setAsInteger(true)->bind(self::class),
			default => null
		};
	}
}
