<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Components\Input\Components\Modifier\Immutables;

use Feralygon\Kit\Core\Immutable;
use Feralygon\Kit\Core\Utilities\Type as UType;

/**
 * Core input modifier component schema immutable class.
 * 
 * @since 1.0.0
 * @property-read string $name <p>The name.</p>
 * @property-read mixed $data [default = null] <p>The data.</p>
 * @see \Feralygon\Kit\Core\Components\Input\Components\Modifier
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
		}
		return null;
	}
}
