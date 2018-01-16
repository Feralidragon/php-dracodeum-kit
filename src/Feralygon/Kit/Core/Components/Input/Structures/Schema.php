<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Components\Input\Structures;

use Feralygon\Kit\Core\Structure;
use Feralygon\Kit\Core\Traits\ExtendedProperties\Objects\Property;
use Feralygon\Kit\Core\Components\Input\Components\Modifier\Structures\Schema as ModifierSchema;
use Feralygon\Kit\Core\Utilities\{
	Data as UData,
	Type as UType
};

/**
 * Core input component schema structure class.
 * 
 * @since 1.0.0
 * @property-read string $name <p>The name.</p>
 * @property-read mixed $data [default = null] <p>The data.</p>
 * @property-read \Feralygon\Kit\Core\Components\Input\Components\Modifier\Structures\Schema[] $modifiers [default = []] <p>The modifier schema instances.</p>
 * @see \Feralygon\Kit\Core\Components\Input
 */
class Schema extends Structure
{
	//Implemented public static methods
	/** {@inheritdoc} */
	public static function getRequiredPropertyNames() : array
	{
		return ['name'];
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function buildProperty(string $name) : ?Property
	{
		switch ($name) {
			case 'name':
				return $this->createProperty()
					->setMode('r')
					->setEvaluator(function (&$value) : bool {
						return UType::evaluateString($value);
					})
				;
			case 'data':
				return $this->createProperty()
					->setMode('r')
					->setDefaultValue(null)
				;
			case 'modifiers':
				return $this->createProperty()
					->setMode('r')
					->setDefaultValue([])
					->setEvaluator(function (&$value) : bool {
						return UData::evaluate($value, function (&$key, &$value) : bool {
							return is_object($value) && UType::isA($value, ModifierSchema::class);
						}, true);
					})
				;
		}
		return null;
	}
}
