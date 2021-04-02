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
 * This prototype restricts a given numeric value to a range of numbers.
 * 
 * @property-write int|float $min_number [writeonce] [transient]  
 * The minimum number to restrict to.
 * 
 * @property-write int|float $max_number [writeonce] [transient]  
 * The maximum number to restrict to.
 * 
 * @property-write bool $min_exclusive [writeonce] [transient] [default = false]  
 * Check minimum as exclusive: do not allow a given value to be equal to the given minimum number.
 * 
 * @property-write bool $max_exclusive [writeonce] [transient] [default = false]  
 * Check maximum as exclusive: do not allow a given value to be equal to the given maximum number.
 * 
 * @property-write bool $negate [writeonce] [transient] [default = false]  
 * Negate the restriction condition, so the given range behaves as a disallowed range instead.
 */
class Range extends Prototype implements IExplanationProducer
{
	//Protected properties
	protected int|float $min_number;
	
	protected int|float $max_number;
	
	protected bool $min_exclusive = false;
	
	protected bool $max_exclusive = false;
	
	protected bool $negate = false;
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function process(mixed &$value)
	{
		return $this->negate !== (
			($this->min_exclusive ? $value > $this->min_number : $value >= $this->min_number) && 
			($this->max_exclusive ? $value < $this->max_number : $value <= $this->max_number)
		);
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Components\Type\Prototypes\Mutator\Interfaces\ExplanationProducer)
	/** {@inheritdoc} */
	public function produceExplanation()
	{
		//text
		$text = Text::build()
			->setParameters([
				'min_number' => $this->min_number,
				'max_number' => $this->max_number
			])
			->setAsLocalized(self::class)
		;
		
		//string
		if ($this->negate) {
			if ($this->min_exclusive && $this->max_exclusive) {
				$text->setString("Must be less than {{min_number}} or greater than {{max_number}}.");
			} elseif ($this->min_exclusive) {
				$text->setString("Must be less than or equal to {{min_number}}, or greater than {{max_number}}.");
			} elseif ($this->max_exclusive) {
				$text->setString("Must be less than {{min_number}}, or greater than or equal to {{max_number}}.");
			} else {
				$text->setString("Cannot be between {{min_number}} and {{max_number}}, inclusive.");
			}
		} elseif ($this->min_exclusive && $this->max_exclusive) {
			$text->setString("Must be greater than {{min_number}} and less than {{max_number}}.");
		} elseif ($this->min_exclusive) {
			$text->setString("Must be greater than {{min_number}} and less than or equal to {{max_number}}.");
		} elseif ($this->max_exclusive) {
			$text->setString("Must be greater than or equal to {{min_number}} and less than {{max_number}}.");
		} else {
			$text->setString("Must be between {{min_number}} and {{max_number}}, inclusive.");
		}
		
		//return
		return $text;
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Prototype\Traits\PropertiesInitializer)
	/** {@inheritdoc} */
	protected function initializeProperties(): void
	{
		$this->addRequiredPropertyNames(['min_number', 'max_number']);
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Prototype\Traits\PropertyBuilder)
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		return match ($name) {
			'min_number', 'max_number' => $this->createProperty()->setMode('w--')->setAsNumber()->bind(self::class),
			'min_exclusive', 'max_exclusive', 'negate'
				=> $this->createProperty()->setMode('w--')->setAsBoolean()->bind(self::class),
			default => null
		};
	}
}
