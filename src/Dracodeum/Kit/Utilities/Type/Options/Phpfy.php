<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Utilities\Type\Options;

use Dracodeum\Kit\Options;
use Dracodeum\Kit\Traits\LazyProperties\Property;

/**
 * Type utility <code>phpfy</code> method options.
 * 
 * @property bool $pretty [coercive] [default = false]
 * <p>Return human-readable and visually appealing PHP code.</p>
 * @property bool $no_throw [coercive] [default = false]
 * <p>Do not throw an exception.</p>
 * @property int|null $spaces [coercive] [default = null]
 * <p>The number of space characters to use for indentation.<br>
 * If not set, then a tab character is used.</p>
 */
class Phpfy extends Options
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		switch ($name) {
			case 'pretty':
				//no break
			case 'no_throw':
				return $this->createProperty()->setAsBoolean()->setDefaultValue(false);
			case 'spaces':
				return $this->createProperty()->setAsInteger(true, null, true)->setDefaultValue(null);
		}
		return null;
	}
}
