<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components\Input\Prototypes\Modifiers\Filters;

use Dracodeum\Kit\Components\Input\Prototypes\Modifiers\Filter;
use Dracodeum\Kit\Components\Input\Prototypes\Modifier\Interfaces\SchemaData as ISchemaData;
use Dracodeum\Kit\Traits\LazyProperties\Property;
use Dracodeum\Kit\Utilities\{
	Text as UText,
	Type as UType
};

/**
 * This filter prototype converts a given input value to uppercase.
 * 
 * @property-write bool $unicode [writeonce] [transient] [default = false]
 * <p>Convert a given input value as Unicode.</p>
 */
class Uppercase extends Filter implements ISchemaData
{
	//Protected properties
	/** @var bool */
	protected $unicode = false;
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function getName(): string
	{
		return 'uppercase';
	}
	
	/** {@inheritdoc} */
	public function processValue(&$value): bool
	{
		if (UType::evaluateString($value)) {
			$value = UText::upper($value, $this->unicode);
			return true;
		}
		return false;
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Components\Input\Prototypes\Modifier\Interfaces\SchemaData)
	/** {@inheritdoc} */
	public function getSchemaData()
	{
		return [
			'unicode' => $this->unicode
		];
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Prototype\Traits\PropertyBuilder)
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		switch ($name) {
			case 'unicode':
				return $this->createProperty()->setMode('w--')->setAsBoolean()->bind(self::class);
		}
		return null;
	}
}
