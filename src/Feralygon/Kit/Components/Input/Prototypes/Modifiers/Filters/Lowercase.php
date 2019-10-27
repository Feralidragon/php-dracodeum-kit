<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Components\Input\Prototypes\Modifiers\Filters;

use Feralygon\Kit\Components\Input\Prototypes\Modifiers\Filter;
use Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\SchemaData as ISchemaData;
use Feralygon\Kit\Traits\LazyProperties\Property;
use Feralygon\Kit\Utilities\{
	Text as UText,
	Type as UType
};

/**
 * This filter prototype converts a given input value to lowercase.
 * 
 * @property-write bool $unicode [writeonce] [transient] [coercive] [default = false]
 * <p>Convert a given input value as Unicode.</p>
 */
class Lowercase extends Filter implements ISchemaData
{
	//Protected properties
	/** @var bool */
	protected $unicode = false;
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function getName(): string
	{
		return 'lowercase';
	}
	
	/** {@inheritdoc} */
	public function processValue(&$value): bool
	{
		if (UType::evaluateString($value)) {
			$value = UText::lower($value, $this->unicode);
			return true;
		}
		return false;
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\SchemaData)
	/** {@inheritdoc} */
	public function getSchemaData()
	{
		return [
			'unicode' => $this->unicode
		];
	}
	
	
	
	//Implemented protected methods (Feralygon\Kit\Prototype\Traits\PropertyBuilder)
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
