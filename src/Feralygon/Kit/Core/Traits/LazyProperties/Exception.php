<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Traits\LazyProperties;

use Feralygon\Kit\Core;

/**
 * Core lazy properties trait exception class.
 * 
 * @since 1.0.0
 * @property-read object $object <p>The object.</p>
 * @see \Feralygon\Kit\Core\Traits\LazyProperties
 */
abstract class Exception extends Core\Exception
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function buildProperties() : void
	{
		$this->addProperty('object')->setAsStrictObject()->setAsRequired();
	}
}
