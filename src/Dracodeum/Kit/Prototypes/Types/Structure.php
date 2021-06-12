<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Prototypes\Types;

use Dracodeum\Kit\Prototypes\Type as Prototype;
use Dracodeum\Kit\Prototypes\Type\Interfaces\Textifier as ITextifier;
use Dracodeum\Kit\Interfaces\Arrayable as IArrayable;
use Dracodeum\Kit\Structure as KitStructure;
use Dracodeum\Kit\Primitives\{
	Error,
	Text
};
use Dracodeum\Kit\Traits\LazyProperties\Property;
use Dracodeum\Kit\Enumerations\InfoLevel as EInfoLevel;
use Dracodeum\Kit\Utilities\Type as UType;

/**
 * This prototype represents a structure.
 * 
 * Only the following types of values are allowed to be coerced into a structure:
 * - `null`;
 * - an instance;
 * - an array of properties, given as a set of `name => value` pairs, with required properties possibly being given as 
 * an array of values (`[value1, value2, ...]`), in the same order as how these properties were first declared;
 * - an arrayable object, as an object implementing the `Dracodeum\Kit\Interfaces\Arrayable` interface.
 * 
 * @property-write string $class [writeonce] [transient]  
 * The class to use.
 * 
 * @property-write bool $clone [writeonce] [transient] [default = false]  
 * If an instance is given, then clone it into a new one with the same properties.
 * 
 * @property-write callable|null $builder [writeonce] [transient] [default = null]  
 * The function to use to build an instance.  
 * It must be compatible with the following signature:  
 * ```
 * function (array $properties): Dracodeum\Kit\Structure
 * ```
 * 
 * **Parameters:**
 * - `array $properties`  
 *   The properties to build with, as a set of `name => value` pairs.  
 *   Required properties may also be given as an array of values (`[value1, value2, ...]`), 
 *   in the same order as how these properties were first declared.  
 *   &nbsp;
 * 
 * **Return:** `Dracodeum\Kit\Structure`  
 * The built instance.
 * 
 * @see \Dracodeum\Kit\Interfaces\Arrayable
 */
class Structure extends Prototype implements ITextifier
{
	//Protected properties
	protected string $class;
	
	protected bool $clone = false;
	
	/** @var callable|null */
	protected $builder = null;
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function process(mixed &$value, $context): ?Error
	{
		//initialize
		$structure = null;
		$properties = [];
		$class = $this->class;
		$clone = $this->clone;
		$builder = $this->builder;
		
		//check
		if ($value instanceof KitStructure) {
			$structure = $clone ? $value->clone() : $value;
		} elseif ($value instanceof IArrayable) {
			$properties = $value->toArray();
		} elseif (is_array($value)) {
			$properties = $value;
		} elseif ($value !== null) {
			$text = Text::build()
				->setString("Only a structure is allowed.")
				->setString(
					"Only the following types of values are allowed to be coerced into a structure:\n" . 
						" - null;\n" . 
						" - an instance;\n" . 
						" - an array of properties, given as a set of \"name => value\" pairs, with required " . 
						"properties possibly being given as an array of values (\"[value1, value2, ...]\"), " . 
						"in the same order as how these properties were first declared;\n" . 
						" - an arrayable object, as an object implementing the " . 
						"\"Dracodeum\Kit\Interfaces\Arrayable\" interface.",
					EInfoLevel::INTERNAL
				)
			;
			return Error::build(text: $text);
		}
		
		//structure properties
		if ($structure !== null && !UType::isA($structure, $class)) {
			$properties = $structure->toArray();
			$structure = null;
		}
		
		//build
		if ($structure === null) {
			$structure = $builder !== null ? $builder($properties) : $class::build($properties);
		}
		
		//class
		if (!UType::isA($structure, $class)) {
			$text = Text::build("Only a structure of a class matching or extending from {{class}} is allowed.")
				->setParameter('class', $class)
				->setPlaceholderAsQuoted('class')
			;
			return Error::build(text: $text);
		}
		
		//finalize
		$value = $structure;
		
		//return
		return null;
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Prototypes\Type\Interfaces\Textifier)
	/** {@inheritdoc} */
	public function textify(mixed $value)
	{
		return Text::build("structure<{{class}}>#{{id}}")->setParameters([
			'class' => get_class($value),
			'id' => spl_object_id($value)
		]);
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Prototype\Traits\PropertiesInitializer)
	/** {@inheritdoc} */
	protected function initializeProperties(): void
	{
		$this->addRequiredPropertyName('class');
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Prototype\Traits\PropertyBuilder)
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		return match ($name) {
			'class' => $this->createProperty()->setMode('w--')->setAsClass(KitStructure::class)->bind(self::class),
			'clone' => $this->createProperty()->setMode('w--')->setAsBoolean()->bind(self::class),
			'builder'
				=> $this->createProperty()
					->setMode('w--')
					->setAsCallable(function (array $properties): KitStructure {}, true, true)
					->bind(self::class)
				,
			default => null
		};
	}
}
