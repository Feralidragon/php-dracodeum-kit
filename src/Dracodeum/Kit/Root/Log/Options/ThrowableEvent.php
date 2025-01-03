<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Root\Log\Options;

use Dracodeum\Kit\Options;
use Dracodeum\Kit\Traits\LazyProperties\Property;
use Dracodeum\Kit\Primitives\Vector;

/**
 * @property \Dracodeum\Kit\Primitives\Vector $tags [default = \Dracodeum\Kit\Primitives\Vector::build()]
 * <p>The tags vector instance to log with.</p>
 * @property string|null $function_name [default = null]
 * <p>The function or method name to use.<br>
 * If not set, then the name of the current function or method in the stack is used.</p>
 * @property int $stack_offset [default = 0]
 * <p>The stack offset to use.</p>
 * @property object|string|null $object_class [default = null]
 * <p>The object or class to use.<br>
 * If not set, then the object or class of the current function or method in the stack is used.</p>
 */
class ThrowableEvent extends Options
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		switch ($name) {
			case 'function_name':
				return $this->createProperty()->setAsString(true, true)->setDefaultValue(null);
			case 'tags':
				return $this->createProperty()->setAsVector(Vector::build()->setAsString(true))->setDefaultValue([]);
			case 'stack_offset':
				return $this->createProperty()->setAsInteger(true)->setDefaultValue(0);
			case 'object_class':
				return $this->createProperty()->setAsObjectClass(null, true)->setDefaultValue(null);
		}
		return null;
	}
}
