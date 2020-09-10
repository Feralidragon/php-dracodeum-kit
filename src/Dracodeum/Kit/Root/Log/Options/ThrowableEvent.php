<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Root\Log\Options;

use Dracodeum\Kit\Options;
use Dracodeum\Kit\Traits\LazyProperties\Property;
use Dracodeum\Kit\Primitives\Vector;

/**
 * @property \Dracodeum\Kit\Primitives\Vector $tags [coercive] [default = \Dracodeum\Kit\Primitives\Vector::build()]
 * <p>The tags vector instance to log with, with each value coerced into a string.<br>
 * The values cannot be empty.</p>
 * @property string|null $function_name [coercive] [default = null]
 * <p>The function or method name to use.<br>
 * If not set, then the name of the current function or method in the stack is used.</p>
 * @property int $stack_offset [coercive] [default = 0]
 * <p>The stack offset to use.<br>
 * It must be greater than or equal to <code>0</code>.</p>
 * @property object|string|null $object_class [coercive] [default = null]
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
