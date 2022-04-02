<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Utilities\Text\Options;

use Dracodeum\Kit\Traits\LazyProperties\Property;

/**
 * Text utility <code>mparse</code> method options.
 * 
 * @property bool $keep_nulls [default = false]
 * <p>Keep the <code>null</code> values in the returned array.</p>
 */
class Mparse extends Parse
{
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		switch ($name) {
			case 'keep_nulls':
				return $this->createProperty()->setAsBoolean()->setDefaultValue(false);
		}
		return parent::buildProperty($name);
	}
}
