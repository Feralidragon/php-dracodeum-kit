<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Enumeration;

use Feralygon\Kit\Core;
use Feralygon\Kit\Core\Enumeration;

/**
 * Core enumeration exception class.
 * 
 * @since 1.0.0
 * @property-read string $enumeration <p>The enumeration class.</p>
 * @see \Feralygon\Kit\Core\Enumeration
 */
abstract class Exception extends Core\Exception
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function loadProperties() : void
	{
		$this->addStrictClassProperty('enumeration', true, Enumeration::class);
	}
}
