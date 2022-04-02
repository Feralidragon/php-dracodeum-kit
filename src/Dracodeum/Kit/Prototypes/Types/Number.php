<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Prototypes\Types;

use Dracodeum\Kit\Prototypes\Type as Prototype;
use Dracodeum\Kit\Prototypes\Type\Interfaces\MutatorProducer as IMutatorProducer;
use Dracodeum\Kit\Interfaces\{
	Integerable as IIntegerable,
	Floatable as IFloatable
};
use Dracodeum\Kit\Primitives\{
	Error,
	Text
};
use Dracodeum\Kit\Prototypes\Types\Number\Enumerations\Type as EType;
use Dracodeum\Kit\Components\Type\Prototypes\Mutators\Numericals as NumericalMutators;
use Dracodeum\Kit\Traits\LazyProperties\Property;
use Dracodeum\Kit\Enumerations\InfoLevel as EInfoLevel;
use Dracodeum\Kit\Utilities\Math as UMath;

/**
 * This prototype represents a number.
 * 
 * Only the following types of values are allowed to be coerced into a number:
 * - an integer or float;
 * - a numeric string, such as `123.45` for 123.45;
 * - a human-readable numeric string, such as `123k` for 123000;
 * - an integerable object, as an object implementing the `Dracodeum\Kit\Interfaces\Integerable` interface;
 * - a floatable object, as an object implementing the `Dracodeum\Kit\Interfaces\Floatable` interface.
 * 
 * @property-write enum<\Dracodeum\Kit\Prototypes\Types\Number\Enumerations\Type>|null $type [writeonce] [transient] [default = null]  
 * The type to restrict and cast to.
 * 
 * @see \Dracodeum\Kit\Interfaces\Integerable
 * @see \Dracodeum\Kit\Interfaces\Floatable
 */
class Number extends Prototype implements IMutatorProducer
{
	//Protected properties
	/** @var enum<\Dracodeum\Kit\Prototypes\Types\Number\Enumerations\Type>|null */
	protected $type = null;
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function process(mixed &$value, $context, bool $strict): ?Error
	{
		//process (strict)
		if ($strict) {
			if ($this->type === EType::INTEGER && !is_int($value)) {
				return Error::build(
					text: Text::build("Only an integer number is strictly allowed.")->setAsLocalized(self::class)
				);
			} elseif ($this->type === EType::FLOAT && !is_float($value)) {
				return Error::build(
					text: Text::build("Only a floating-point number is strictly allowed.")->setAsLocalized(self::class)
				);
			} elseif (!is_int($value) && !is_float($value)) {
				return Error::build(
					text: Text::build("Only a number is strictly allowed.")->setAsLocalized(self::class)
				);
			}
			return null;
		}
		
		//process
		$number = null;
		if (is_int($value) || is_float($value)) {
			$number = $value;
		} elseif (is_string($value)) {
			$number = is_numeric($value) ? (float)$value : UMath::mnumber($value, true);
		} elseif ($value instanceof IFloatable) {
			$number = $value->toFloat();
		} elseif ($value instanceof IIntegerable) {
			$number = $value->toInteger();
		}
		
		//error
		if ($number === null) {
			$text = Text::build()
				->setString("Only a number is allowed.")
				->setString(
					"Only the following types of values are allowed to be coerced into a number:\n" . 
						" - an integer or float;\n" . 
						" - a numeric string, such as \"123.45\" for 123.45;\n" .  
						" - a human-readable numeric string, such as \"123k\" for 123000;\n" . 
						" - an integerable object, as an object implementing the " . 
						"\"Dracodeum\Kit\Interfaces\Integerable\" interface;\n" . 
						" - a floatable object, as an object implementing the " . 
						"\"Dracodeum\Kit\Interfaces\Floatable\" interface.",
					EInfoLevel::INTERNAL
				)
				->setAsLocalized(self::class)
			;
			return Error::build(text: $text);
		}
		
		//cast
		if ((float)$number === floor($number)) {
			$number = (int)$number;
		}
		
		//type
		if ($this->type === EType::INTEGER && !is_int($number)) {
			return Error::build(text: Text::build("Only an integer number is allowed.")->setAsLocalized(self::class));
		} elseif ($this->type === EType::FLOAT) {
			$number = (float)$number;
		}
		
		//finalize
		$value = $number;
		
		//return
		return null;
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Prototypes\Type\Interfaces\MutatorProducer)
	/** {@inheritdoc} */
	public function produceMutator(string $name, array $properties)
	{
		return match ($name) {
			'minimum', 'min' => NumericalMutators\Minimum::class,
			'xminimum', 'xmin' => new NumericalMutators\Minimum(['exclusive' => true] + $properties),
			'unsigned' => new NumericalMutators\Minimum([0] + $properties),
			'positive' => new NumericalMutators\Minimum([0, 'exclusive' => true] + $properties),
			'maximum', 'max' => NumericalMutators\Maximum::class,
			'xmaximum', 'xmax' => new NumericalMutators\Maximum(['exclusive' => true] + $properties),
			'negative' => new NumericalMutators\Maximum([0, 'exclusive' => true] + $properties),
			'range' => NumericalMutators\Range::class,
			'xrange' => new NumericalMutators\Range(['min_exclusive' => true, 'max_exclusive' => true] + $properties),
			'non_range' => new NumericalMutators\Range(['negate' => true] + $properties),
			'non_xrange' => new NumericalMutators\Range(
				['negate' => true, 'min_exclusive' => true, 'max_exclusive' => true] + $properties
			),
			'odd' => NumericalMutators\Odd::class,
			'even' => NumericalMutators\Even::class,
			'multiples' => NumericalMutators\Multiples::class,
			'non_multiples' => new NumericalMutators\Multiples(['negate' => true] + $properties),
			default => null
		};
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Prototype\Traits\PropertyBuilder)
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		return match ($name) {
			'type'
				=> $this->createProperty()
					->setMode('w--')
					->setAsEnumerationValue(EType::class, true)
					->bind(self::class)
				,
			default => null
		};
	}
}
