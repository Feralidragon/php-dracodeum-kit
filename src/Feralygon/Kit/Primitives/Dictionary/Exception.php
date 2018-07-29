<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Primitives\Dictionary;

use Feralygon\Kit\Exception as KitException;
use Feralygon\Kit\Primitives\Dictionary;

/**
 * @since 1.0.0
 * @property-read \Feralygon\Kit\Primitives\Dictionary|string $dictionary
 * <p>The dictionary instance or class.</p>
 * @see \Feralygon\Kit\Primitives\Dictionary
 */
abstract class Exception extends KitException
{
	//Implemented protected methods (Feralygon\Kit\Exception\Traits\Properties)
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		$this->addProperty('dictionary')->setAsObjectClass(Dictionary::class);
	}
}
