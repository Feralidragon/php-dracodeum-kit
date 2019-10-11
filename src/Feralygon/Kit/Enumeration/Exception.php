<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Enumeration;

use Feralygon\Kit\Exception as KitException;
use Feralygon\Kit\Enumeration;

/**
 * @property-read string $enumeration [strict = class]
 * <p>The enumeration class.</p>
 */
abstract class Exception extends KitException
{
	//Implemented protected methods (Feralygon\Kit\Exception\Traits\PropertiesLoader)
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		$this->addProperty('enumeration')->setAsStrictClass(Enumeration::class);
	}
}
