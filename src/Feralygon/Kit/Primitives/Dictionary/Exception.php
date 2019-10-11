<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Primitives\Dictionary;

use Feralygon\Kit\Exception as KitException;
use Feralygon\Kit\Primitives\Dictionary;

/**
 * @property-read \Feralygon\Kit\Primitives\Dictionary|string $dictionary [coercive = object or class]
 * <p>The dictionary instance or class.</p>
 */
abstract class Exception extends KitException
{
	//Implemented protected methods (Feralygon\Kit\Exception\Traits\PropertiesLoader)
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		$this->addProperty('dictionary')->setAsObjectClass(Dictionary::class);
	}
}
