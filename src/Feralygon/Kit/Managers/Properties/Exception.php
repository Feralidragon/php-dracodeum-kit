<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Managers\Properties;

use Feralygon\Kit\Exception as KitException;
use Feralygon\Kit\Managers\Properties as Manager;

/**
 * @since 1.0.0
 * @property-read \Feralygon\Kit\Managers\Properties $manager
 * <p>The properties manager instance.</p>
 * @see \Feralygon\Kit\Managers\Properties
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
