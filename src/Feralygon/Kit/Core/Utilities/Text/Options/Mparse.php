<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Utilities\Text\Options;

use Feralygon\Kit\Core\Utilities\Type as UType;

/**
 * Core text utility mparse method options class.
 * 
 * @since 1.0.0
 * @property bool $keep_nulls [default = false] <p>Keep the <code>null</code> values in the returned array.</p>
 */
class Mparse extends Parse
{
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function getDefaultPropertyValue(string $name)
	{
		switch ($name) {
			case 'keep_nulls':
				return false;
		}
		return parent::getDefaultPropertyValue($name);
	}
	
	/** {@inheritdoc} */
	protected function evaluateProperty(string $name, &$value) : ?bool
	{
		switch ($name) {
			case 'keep_nulls':
				return UType::evaluateBoolean($value);
		}
		return parent::evaluateProperty($name, $value);
	}
}
