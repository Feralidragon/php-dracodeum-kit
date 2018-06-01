<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Utilities\Url\Options;

use Feralygon\Kit\Options;
use Feralygon\Kit\Traits\LazyProperties\Property;

/**
 * URL utility <code>unquerify</code> method options.
 * 
 * @since 1.0.0
 * @property bool $allow_arrays [default = false]
 * <p>Allow the given string to be unquerified into array values.</p>
 * @see \Feralygon\Kit\Utilities\Url
 */
class Unquerify extends Options
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function buildProperty(string $name) : ?Property
	{
		switch ($name) {
			case 'allow_arrays':
				return $this->createProperty()->setAsBoolean()->setDefaultValue(false);
		}
		return null;
	}
}
