<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components\Type\Prototypes\Mutators\Stringables;

use Dracodeum\Kit\Components\Type\Prototypes\Mutators\Stringable as Prototype;
use Dracodeum\Kit\Traits\LazyProperties\Property;
use Dracodeum\Kit\Utilities\Text as UText;

/**
 * This prototype converts a given stringable value to uppercase.
 * 
 * @property-write bool $unicode [writeonce] [transient] [default = false]  
 * Convert as Unicode.
 */
class ToUppercase extends Prototype
{
	//Protected properties
	protected bool $unicode = false;
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function process(mixed &$value)
	{
		$value = UText::upper($value, $this->unicode);
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Prototype\Traits\PropertyBuilder)
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		return match ($name) {
			'unicode' => $this->createProperty()->setMode('w--')->setAsBoolean()->bind(self::class),
			default => null
		};
	}
}
