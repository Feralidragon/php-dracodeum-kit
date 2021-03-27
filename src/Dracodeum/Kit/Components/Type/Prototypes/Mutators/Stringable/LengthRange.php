<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components\Type\Prototypes\Mutators\Stringable;

use Dracodeum\Kit\Components\Type\Prototypes\Mutator as Prototype;
use Dracodeum\Kit\Components\Type\Prototypes\Mutator\Interfaces\ExplanationProducer as IExplanationProducer;
use Dracodeum\Kit\Primitives\Text;
use Dracodeum\Kit\Traits\LazyProperties\Property;
use Dracodeum\Kit\Utilities\Text as UText;

/**
 * This prototype restricts a given stringable value to a length range.
 * 
 * @property-write int $min_length [writeonce] [transient]  
 * The minimum length to restrict to.
 * 
 * @property-write int $max_length [writeonce] [transient]  
 * The maximum length to restrict to.
 * 
 * @property-write bool $unicode [writeonce] [transient] [default = false]  
 * Check as Unicode.
 */
class LengthRange extends Prototype implements IExplanationProducer
{
	//Protected properties
	protected int $min_length;
	
	protected int $max_length;
	
	protected bool $unicode = false;
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function process(mixed &$value)
	{
		$length = UText::length($value, $this->unicode);
		return $length >= $this->min_length && $length <= $this->max_length;
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Components\Type\Prototypes\Mutator\Interfaces\ExplanationProducer)
	/** {@inheritdoc} */
	public function produceExplanation()
	{
		return Text::build()
			->setString("Only between {{min_length}} and {{max_length}} character is allowed.")
			->setPluralString("Only between {{min_length}} and {{max_length}} characters are allowed.")
			->setPluralNumberPlaceholder('max_length')
			->setPluralNumber($this->max_length)
			->setParameter('min_length', $this->min_length)
			->setAsLocalized(self::class)
		;
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Prototype\Traits\PropertiesInitializer)
	/** {@inheritdoc} */
	protected function initializeProperties(): void
	{
		$this->addRequiredPropertyNames(['min_length', 'max_length']);
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Prototype\Traits\PropertyBuilder)
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		return match ($name) {
			'min_length', 'max_length'
				=> $this->createProperty()->setMode('w--')->setAsInteger(true)->bind(self::class),
			'unicode' => $this->createProperty()->setMode('w--')->setAsBoolean()->bind(self::class),
			default => null
		};
	}
}
