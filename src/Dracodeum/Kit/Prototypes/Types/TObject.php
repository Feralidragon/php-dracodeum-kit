<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
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

/**
 * This prototype represents an object.
 * 
 * Only an object is allowed.
 * 
 * @property-write string|null $class [writeonce] [transient] [default = null]  
 * The class to restrict to.
 */
class TObject extends Prototype implements ITextifier
{
	//Protected properties
	protected ?string $class = null;
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function process(mixed &$value, $context, bool $strict): ?Error
	{
		//check
		if (!is_object($value)) {
			return Error::build(text: $strict ? "Only an object is strictly allowed." : "Only an object is allowed.");
		}
		
		//class
		if ($this->class !== null && !UType::isA($value, $this->class)) {
			$text = Text::build("Only an object of a class matching or extending from {{class}} is allowed.")
				->setParameter('class', $this->class)
				->setPlaceholderAsQuoted('class')
			;
			return Error::build(text: $text);
		}
		
		//return
		return null;
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Prototypes\Type\Interfaces\Textifier)
	/** {@inheritdoc} */
	public function textify(mixed $value)
	{
		return Text::build("object<{{class}}>#{{id}}")->setParameters([
			'class' => get_class($value),
			'id' => spl_object_id($value)
		]);
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
