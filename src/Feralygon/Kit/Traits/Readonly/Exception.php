<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Traits\Readonly;

use Feralygon\Kit\Exception as KitException;

/**
 * @since 1.0.0
 * @property-read object $object
 * <p>The object.</p>
 * @see \Feralygon\Kit\Traits\Readonly
 */
abstract class Exception extends KitException
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function loadProperties() : void
	{
		$this->addProperty('object')->setAsStrictObject()->setAsRequired();
	}
}
