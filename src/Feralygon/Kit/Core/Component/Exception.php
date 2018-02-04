<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Component;

use Feralygon\Kit\Core;
use Feralygon\Kit\Core\Component;

/**
 * Core component exception class.
 * 
 * @since 1.0.0
 * @property-read \Feralygon\Kit\Core\Component|string $component <p>The component instance or class.</p>
 * @see \Feralygon\Kit\Core\Component
 */
abstract class Exception extends Core\Exception
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function loadProperties() : void
	{
		$this->addObjectClassProperty('component', true, Component::class);
	}
}
