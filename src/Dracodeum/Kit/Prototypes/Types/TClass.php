<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Prototypes\Types;

use Dracodeum\Kit\Prototypes\Type as Prototype;
use Dracodeum\Kit\Prototypes\Type\Interfaces\Textifier as ITextifier;
use Dracodeum\Kit\Primitives\{
	Error,
	Text
};
use Dracodeum\Kit\Traits\LazyProperties\Property;
use Dracodeum\Kit\Utilities\Type as UType;
use ReflectionClass;

/**
 * This prototype represents a class.
 * 
 * Only a string, as a full class name, or an object, is allowed to be coerced into a class.
 * 
 * @property-write string|null $class [writeonce] [transient] [default = null]  
 * The class to restrict to.
 */
class TClass extends Prototype implements ITextifier
{
	//Protected properties
	protected ?string $class = null;
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function process(mixed &$value, $context): ?Error
	{
		//process
		$class = null;
		if (is_string($value) && class_exists($value)) {
			$class = $value[0] === '\\' ? substr($value, 1) : $value;
		} elseif (is_object($value)) {
			$class = get_class($value);
		} else {
			return Error::build(
				text: "Only a string, as a full class name, or an object, is allowed to be coerced into a class."
			);
		}
		
		//class
		if ($this->class !== null && !UType::isA($class, $this->class)) {
			$text = Text::build("Only a class matching or extending from {{class}} is allowed.")
				->setParameter('class', $this->class)
				->setPlaceholderAsQuoted('class')
			;
			return Error::build(text: $text);
		}
		
		//finalize
		$value = $class;
		
		//return
		return null;
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Prototypes\Type\Interfaces\Textifier)
	/** {@inheritdoc} */
	public function textify(mixed $value)
	{
		$name = $value;
		if (UType::anonymous($value)) {
			$reflection = new ReflectionClass($value);
			$name = Text::build("anonymous@{{filename}}:{{line}}")->setParameters([
				'filename' => $reflection->getFileName(),
				'line' => $reflection->getStartLine()
			]);
		}
		return Text::build("class<{{name}}>")->setParameter('name', $name);
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Prototype\Traits\PropertyBuilder)
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		return match ($name) {
			'class' => $this->createProperty()->setMode('w--')->setAsClass()->bind(self::class),
			default => null
		};
	}
}
