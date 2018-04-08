<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Prototypes\Inputs\Numbers\Integer;

use Feralygon\Kit\Exception as KitException;
use Feralygon\Kit\Prototypes\Inputs\Numbers\Integer as Prototype;

/**
 * @since 1.0.0
 * @property-read \Feralygon\Kit\Prototypes\Inputs\Numbers\Integer $prototype
 * <p>The prototype instance.</p>
 * @see \Feralygon\Kit\Prototypes\Inputs\Numbers\Integer
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
