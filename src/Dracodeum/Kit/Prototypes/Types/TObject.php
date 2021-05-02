<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Prototypes\Types;

use Dracodeum\Kit\Prototypes\Type as Prototype;
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
class TObject extends Prototype
{
	//Protected properties
	protected ?string $class = null;
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function process(mixed &$value, $context): ?Error
	{
		//check
		if (!is_object($value)) {
			return Error::build(text: "Only an object is allowed.");
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
