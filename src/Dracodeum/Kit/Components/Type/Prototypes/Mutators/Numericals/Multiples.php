<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components\Type\Prototypes\Mutators\Numericals;

use Dracodeum\Kit\Components\Type\Prototypes\Mutators\Numerical as Prototype;
use Dracodeum\Kit\Components\Type\Prototypes\Mutator\Interfaces\ExplanationProducer as IExplanationProducer;
use Dracodeum\Kit\Primitives\Text;
use Dracodeum\Kit\Options\Text as TextOptions;
use Dracodeum\Kit\Traits\LazyProperties\Property;
use Dracodeum\Kit\Utilities\{
	Text as UText,
	Type as UType
};

/**
 * This prototype restricts a given numeric value to a set of multiples.
 * 
 * @property-write (int|float)[] $multiples [writeonce] [transient]  
 * The multiples to restrict to.
 * 
 * @property-write bool $negate [writeonce] [transient] [default = false]  
 * Negate the restriction condition, so the given multiples behave as disallowed multiples instead.
 */
class Multiples extends Prototype implements IExplanationProducer
{
	//Protected properties
	protected array $multiples;
	
	protected bool $negate = false;
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function process(mixed &$value)
	{
		foreach ($this->multiples as $multiple) {
			if (is_int($multiple) && is_int($value) && $value % $multiple === 0) {
				return !$this->negate;
			} elseif (is_float($multiple) || is_float($value)) {
				$f = (float)$value / (float)$multiple;
				if ($f === floor($f)) {
					return !$this->negate;
				}
			}
		}
		return $this->negate;
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Components\Type\Prototypes\Mutator\Interfaces\ExplanationProducer)
	/** {@inheritdoc} */
	public function produceExplanation()
	{
		//text (initialize)
		$text = Text::build()
			->setPluralNumber(count($this->multiples))
			->setParameter('multiples', $this->multiples)
			->setPlaceholderStringifier('multiples', function (mixed $value, TextOptions $text_options): string {
				return UText::commify($value, $text_options, $this->negate ? 'and' : 'or');
			})
			->setAsLocalized(self::class)
		;
		
		//text (finalize)
		if ($this->negate) {
			$text
				->setString("Cannot be a multiple of {{multiples}}.")
				->setPluralString("Cannot be a multiple of any of the following: {{multiples}}.")
			;
		} else {
			$text
				->setString("Must be a multiple of {{multiples}}.")
				->setPluralString("Must be a multiple of at least one of the following: {{multiples}}.")
			;
		}
		
		//return
		return $text;
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Prototype\Traits\PropertiesInitializer)
	/** {@inheritdoc} */
	protected function initializeProperties(): void
	{
		$this->addRequiredPropertyName('multiples');
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Prototype\Traits\PropertyBuilder)
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		return match ($name) {
			'multiples'
				=> $this->createProperty()
					->setMode('w--')
					->setAsArray(fn (&$key, &$value): bool => UType::evaluateNumber($value), true, true)
					->bind(self::class)
				,
			'negate' => $this->createProperty()->setMode('w--')->setAsBoolean()->bind(self::class),
			default => null
		};
	}
}
