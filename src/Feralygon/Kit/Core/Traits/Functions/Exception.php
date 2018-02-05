<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Traits\Functions;

use Feralygon\Kit\Core;

/**
 * Core functions trait exception class.
 * 
 * @since 1.0.0
 * @property-read object $object <p>The object.</p>
 * @see \Feralygon\Kit\Core\Traits\Functions
 */
abstract class Exception extends Core\Exception
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function loadProperties() : void
	{
		$this->addStrictObjectProperty('object', true);
	}
}
