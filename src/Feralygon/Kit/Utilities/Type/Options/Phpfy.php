<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Utilities\Type\Options;

use Feralygon\Kit\Options;
use Feralygon\Kit\Traits\LazyProperties\Objects\Property;

/**
 * Type utility <code>phpfy</code> method options.
 * 
 * @since 1.0.0
 * @property bool $pretty [default = false]
 * <p>Return human-readable and visually appealing PHP code.</p>
 * @property bool $no_throw [default = false]
 * <p>Do not throw an exception.</p>
 * @property int|null $spaces [default = null]
 * <p>The number of space characters to use for indentation.<br>
 * If not set, then a tab character is used.<br>
 * If set, then it must be greater than or equal to <code>0</code>.</p>
 * @see \Feralygon\Kit\Utilities\Type
 */
class Phpfy extends Options
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function buildProperty(string $name) : ?Property
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
