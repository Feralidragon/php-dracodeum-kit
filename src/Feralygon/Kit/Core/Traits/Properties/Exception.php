<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Traits\Properties;

use Feralygon\Kit\Core;

/**
 * Core properties trait exception class.
 * 
 * @since 1.0.0
 * @property-read object $object <p>The object.</p>
 * @see \Feralygon\Kit\Core\Traits\Properties
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
