<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Prototype;

use Feralygon\Kit\Core;
use Feralygon\Kit\Core\Prototype;

/**
 * Core prototype exception class.
 * 
 * @since 1.0.0
 * @property-read \Feralygon\Kit\Core\Prototype $prototype <p>The prototype instance.</p>
 * @see \Feralygon\Kit\Core\Prototype
 */
abstract class Exception extends Core\Exception
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function loadProperties() : void
	{
		$this->addStrictObjectProperty('prototype', true, Prototype::class);
	}
}
