<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Managers\Properties;

use Feralygon\Kit\Core;
use Feralygon\Kit\Core\Managers\Properties as Manager;

/**
 * Core properties manager exception class.
 * 
 * @since 1.0.0
 * @property-read \Feralygon\Kit\Core\Managers\Properties $manager <p>The properties manager instance.</p>
 * @see \Feralygon\Kit\Core\Managers\Properties
 */
abstract class Exception extends Core\Exception
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function buildProperties() : void
	{
		$this->addProperty('manager')->setAsStrictObject(Manager::class)->setAsRequired();
	}
}
