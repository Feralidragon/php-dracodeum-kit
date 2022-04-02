<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Prototypes\Types;

use Dracodeum\Kit\Prototypes\Type as Prototype;
use Dracodeum\Kit\Prototypes\Type\Interfaces\Textifier as ITextifier;
use Dracodeum\Kit\{
	Component as KitComponent,
	Prototype as KitPrototype
};
use Dracodeum\Kit\Primitives\{
	Error,
	Text
};
use Dracodeum\Kit\Traits\LazyProperties\Property;
use Dracodeum\Kit\Enumerations\InfoLevel as EInfoLevel;
use Dracodeum\Kit\Utilities\Type as UType;

/**
 * This prototype represents a component.
 * 
 * Only the following types of values are allowed to be coerced into a component:
 * - a component instance or name;
 * - a prototype instance, class or name.
 * 
 * @property-write string $class [writeonce] [transient]  
 * The class to use.
 * 
 * @property-write array $properties [writeonce] [transient] [default = []]  
 * The properties to use, as a set of `name => value` pairs.  
 * Required properties may also be given as an array of values (`[value1, value2, ...]`), 
 * in the same order as how these properties were first declared.  
 * 
 * @property-write callable|null $builder [writeonce] [transient] [default = null]  
 * The function to use to build an instance.  
 * It must be compatible with the following signature:  
 * ```
 * function ($prototype, array $properties): Dracodeum\Kit\Component
 * ```
 * 
 * **Parameters:**
 * - `coercible:prototype|null $prototype`  
 *   The prototype to build with.  
 *   &nbsp;
 * - `array $properties`  
 *   The properties to build with, as a set of `name => value` pairs.  
 *   Required properties may also be given as an array of values (`[value1, value2, ...]`), 
 *   in the same order as how these properties were first declared.  
 *   &nbsp;
 * 
 * **Return:** `Dracodeum\Kit\Component`  
 * The built instance.
 * 
 * @property-write callable|null $named_builder [writeonce] [transient] [default = null]  
 * The function to use to build an instance for a given name.  
 * It must be compatible with the following signature:  
 * ```
 * function (string $name, array $properties): ?Dracodeum\Kit\Component
 * ```
 * 
 * **Parameters:**
 * - `string $name`  
 *   The name to build for.  
 *   &nbsp;
 * - `array $properties`  
 *   The properties to build with, as a set of `name => value` pairs.  
 *   Required properties may also be given as an array of values (`[value1, value2, ...]`), 
 *   in the same order as how these properties were first declared.  
 *   &nbsp;
 * 
 * **Return:** `Dracodeum\Kit\Component|null`  
 * The built instance for the given name, or `null` if none was built.
 * 
 * @see \Dracodeum\Kit\Component
 */
class Component extends Prototype implements ITextifier
{
	//Protected properties
	protected string $class;
	
	protected array $properties = [];
	
	/** @var callable|null */
	protected $builder = null;
	
	/** @var callable|null */
	protected $named_builder = null;
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function process(mixed &$value, $context, bool $strict): ?Error
	{
		//initialize
		$component = null;
		$class = $this->class;
		$properties = $this->properties;
		$builder = $this->builder;
		$named_builder = $this->named_builder;
		
		//check
		if ($value instanceof KitComponent) {
			$component = $value;
		} elseif ($strict) {
			return Error::build(text: "Only a component instance is strictly allowed.");
		} elseif (!is_string($value) && !($value instanceof KitPrototype)) {
			$text = Text::build()
				->setString("Only a component is allowed.")
				->setString(
					"Only the following types of values are allowed to be coerced into a component:\n" . 
						" - a component instance or name;\n" . 
						" - a prototype instance, class or name.",
					EInfoLevel::INTERNAL
				)
			;
			return Error::build(text: $text);
		}
		
		//named builder
		if ($component === null && is_string($value) && $named_builder !== null) {
			$component = $named_builder($value, $properties);
		}
		
		//build
		if ($component === null) {
			$component = $builder !== null ? $builder($value, $properties) : $class::build($value, $properties);
		}
		
		//class
		if (!UType::isA($component, $class)) {
			$text = Text::build("Only a component of a class matching or extending from {{class}} is allowed.")
				->setParameter('class', $class)
				->setPlaceholderAsQuoted('class')
			;
			return Error::build(text: $text);
		}
		
		//finalize
		$value = $component;
		
		//return
		return null;
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Prototypes\Type\Interfaces\Textifier)
	/** {@inheritdoc} */
	public function textify(mixed $value)
	{
		return Text::build("component<{{class}}>#{{id}}")->setParameters([
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
			'class' => $this->createProperty()->setMode('w--')->setAsClass(KitComponent::class)->bind(self::class),
			'properties' => $this->createProperty()->setMode('w--')->setAsArray()->bind(self::class),
			'builder'
				=> $this->createProperty()
					->setMode('w--')
					->setAsCallable(function ($prototype, array $properties): KitComponent {}, true, true)
					->bind(self::class)
				,
			'named_builder'
				=> $this->createProperty()
					->setMode('w--')
					->setAsCallable(function (string $name, array $properties): ?KitComponent {}, true, true)
					->bind(self::class)
				,
			default => null
		};
	}
}
