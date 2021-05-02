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

/**
 * This prototype represents a resource.
 * 
 * Only a resource is allowed.
 * 
 * @property-write string|null $type [writeonce] [transient] [default = null]  
 * The type to restrict to.
 */
class TResource extends Prototype implements ITextifier
{
	//Protected properties
	protected ?string $type = null;
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function process(mixed &$value, $context): ?Error
	{
		//check
		if (!is_resource($value)) {
			return Error::build(text: "Only a resource is allowed.");
		}
		
		//type
		if ($this->type !== null && get_resource_type($value) !== $this->type) {
			$text = Text::build("Only a resource of the type {{type}} is allowed.")
				->setParameter('type', $this->type)
				->setPlaceholderAsQuoted('type')
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
		return Text::build("resource<{{type}}>#{{id}}")->setParameters([
			'type' => get_resource_type($value),
			'id' => get_resource_id($value)
		]);
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Prototype\Traits\PropertyBuilder)
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		return match ($name) {
			'type' => $this->createProperty()->setMode('w--')->setAsString(true)->bind(self::class),
			default => null
		};
	}
}
