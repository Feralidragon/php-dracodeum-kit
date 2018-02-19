<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Enumeration;

use Feralygon\Kit\Exception as KitException;
use Feralygon\Kit\Enumeration;

/**
 * Enumeration exception class.
 * 
 * @since 1.0.0
 * @property-read string $enumeration <p>The enumeration class.</p>
 * @see \Feralygon\Kit\Enumeration
 */
abstract class Exception extends KitException
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function buildProperties() : void
	{
		$this->addProperty('enumeration')->setAsStrictClass(Enumeration::class)->setAsRequired();
	}
}
