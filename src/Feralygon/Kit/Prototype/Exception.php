<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Prototype;

use Feralygon\Kit\Exception as KitException;
use Feralygon\Kit\Prototype;

/**
 * Prototype exception class.
 * 
 * @since 1.0.0
 * @property-read \Feralygon\Kit\Prototype $prototype <p>The prototype instance.</p>
 * @see \Feralygon\Kit\Prototype
 */
abstract class Exception extends KitException
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function buildProperties() : void
	{
		$this->addProperty('prototype')->setAsStrictObject(Prototype::class)->setAsRequired();
	}
}
