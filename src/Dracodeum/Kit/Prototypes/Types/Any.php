<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Prototypes\Types;

use Dracodeum\Kit\Prototypes\Type as Prototype;
use Dracodeum\Kit\Prototypes\Type\Interfaces\Textifier as ITextifier;
use Dracodeum\Kit\Components\Type;
use Dracodeum\Kit\Primitives\Error;
use Dracodeum\Kit\Traits\LazyProperties\Property;

/**
 * This prototype represents any type of value.
 * 
 * @property-write \Dracodeum\Kit\Components\Type[] $types [writeonce] [transient] [default = []]  
 * The type instances to use.
 */
class Any extends Prototype implements ITextifier
{
	//Protected properties
	/** @var \Dracodeum\Kit\Components\Type[] */
	protected array $types = [];
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function process(mixed &$value, $context, bool $strict): ?Error
	{
		$error = null;
		foreach ($this->types as $type) {
			$error = $type->process($value, $context);
			if ($error === null) {
				return null;
			}
		}
		return $error;
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Prototypes\Type\Interfaces\Textifier)
	/** {@inheritdoc} */
	public function textify(mixed $value)
	{
		foreach ($this->types as $type) {
			$text = $type->textify($value, no_throw: true);
			if ($text !== null) {
				return $text;
			}
		}
		return null;
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Prototype\Traits\PropertyBuilder)
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		return match ($name) {
			'types'
				=> $this->createProperty()
					->setMode('w--')
					->setAsArray(fn (&$key, &$value): bool => Type::evaluate($value), true)
					->bind(self::class)
				,
			default => null
		};
	}
}
