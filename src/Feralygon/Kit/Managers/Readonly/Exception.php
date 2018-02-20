<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Managers\Readonly;

use Feralygon\Kit\Exception as KitException;
use Feralygon\Kit\Managers\Readonly as Manager;

/**
 * Read-only manager exception class.
 * 
 * @since 1.0.0
 * @property-read \Feralygon\Kit\Managers\Readonly $manager <p>The read-only manager instance.</p>
 * @see \Feralygon\Kit\Managers\Readonly
 */
abstract class Exception extends KitException
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function buildProperties() : void
	{
		$this->addProperty('manager')->setAsStrictObject(Manager::class)->setAsRequired();
	}
}
