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
use Dracodeum\Kit\Utilities\{
	Text as UText,
	Type as UType
};

/**
 * This prototype restricts a given stringable value to a set of wildcard matches.
 * 
 * @property-write string[] $wildcards [writeonce] [transient]  
 * The wildcard matches to restrict the given value to.
 * 
 * @property-write bool $insensitive [writeonce] [transient] [default = false]  
 * Match the given wildcards in a case-insensitive manner.
 * 
 * @property-write bool $negate [writeonce] [transient] [default = false]  
 * Negate the restriction condition, so the given wildcards behave as disallowed wildcards instead.
 */
class Wildcards extends Prototype implements IExplanationProducer
{
	//Protected properties
	/** @var string[] */
	protected array $wildcards;
	
	protected bool $insensitive = false;
	
	protected bool $negate = false;
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function process(mixed &$value)
	{
		return UText::anyWildcardsMatch($value, $this->wildcards, $this->insensitive) !== $this->negate;
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Components\Type\Prototypes\Mutator\Interfaces\ExplanationProducer)
	/** {@inheritdoc} */
	public function produceExplanation()
	{
		
		//TODO
		
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Prototype\Traits\PropertiesInitializer)
	/** {@inheritdoc} */
	protected function initializeProperties(): void
	{
		$this->addRequiredPropertyName('wildcards');
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Prototype\Traits\PropertyBuilder)
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		return match ($name) {
			'wildcards' => $this->createProperty()
				->setMode('w--')
				->setAsArray(fn (&$key, &$value): bool => UType::evaluateString($value), true, true)
				->bind(self::class)
			,
			'insensitive', 'negate' => $this->createProperty()->setMode('w--')->setAsBoolean()->bind(self::class),
			default => null
		};
	}
}
