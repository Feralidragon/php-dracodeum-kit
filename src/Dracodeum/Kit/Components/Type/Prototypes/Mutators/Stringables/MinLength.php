<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components\Type\Prototypes\Mutators\Stringables;

use Dracodeum\Kit\Components\Type\Prototypes\Mutators\Stringable as Prototype;
use Dracodeum\Kit\Components\Type\Prototypes\Mutator\Interfaces\ExplanationProducer as IExplanationProducer;
use Dracodeum\Kit\Primitives\Text;
use Dracodeum\Kit\Traits\LazyProperties\Property;
use Dracodeum\Kit\Utilities\Text as UText;

/**
 * This prototype restricts a given stringable value to a minimum length.
 * 
 * @property-write int $length [writeonce] [transient]  
 * The length to restrict to.
 * 
 * @property-write bool $unicode [writeonce] [transient] [default = false]  
 * Check as Unicode.
 */
class MinLength extends Prototype implements IExplanationProducer
{
	//Protected properties
	protected int $length;
	
	protected bool $unicode = false;
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function process(mixed &$value)
	{
		return UText::length($value, $this->unicode) >= $this->length;
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Components\Type\Prototypes\Mutator\Interfaces\ExplanationProducer)
	/** {@inheritdoc} */
	public function produceExplanation()
	{
		return Text::build()
			->setString("At least {{length}} character is required.")
			->setPluralString("At least {{length}} characters are required.")
			->setPluralNumberPlaceholder('length')
			->setPluralNumber($this->length)
			->setAsLocalized(self::class)
		;
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Prototype\Traits\PropertiesInitializer)
	/** {@inheritdoc} */
	protected function initializeProperties(): void
	{
		$this->addRequiredPropertyName('length');
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Prototype\Traits\PropertyBuilder)
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		return match ($name) {
			'length' => $this->createProperty()->setMode('w--')->setAsInteger(true)->bind(self::class),
			'unicode' => $this->createProperty()->setMode('w--')->setAsBoolean()->bind(self::class),
			default => null
		};
	}
}
