<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Components\Input\Immutables;

use Feralygon\Kit\Core\Immutable;
use Feralygon\Kit\Core\Components\Input\Components\Modifier\Immutables\Schema as ModifierSchema;
use Feralygon\Kit\Core\Utilities\{
	Data as UData,
	Type as UType
};

/**
 * Core input component schema immutable class.
 * 
 * @since 1.0.0
 * @property-read string $name <p>The name.</p>
 * @property-read mixed $data [default = null] <p>The data.</p>
 * @property-read \Feralygon\Kit\Core\Components\Input\Components\Modifier\Immutables\Schema[] $modifiers [default = []] 
 * <p>The modifier schema instances.</p>
 * @see \Feralygon\Kit\Core\Components\Input
 */
class Schema extends Immutable
{
	//Implemented public static methods
	/** {@inheritdoc} */
	public static function getRequiredPropertyNames() : array
	{
		return ['name'];
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function evaluateProperty(string $name, &$value) : ?bool
	{
		switch ($name) {
			case 'name':
				return UType::evaluateString($value, true);
			case 'data':
				return true;
			case 'modifiers':
				$value = $value ?? [];
				return UData::evaluate($value, function (&$key, &$value) : bool {
					return is_object($value) && UType::isA($value, ModifierSchema::class);
				}, true);
		}
		return null;
	}
}
