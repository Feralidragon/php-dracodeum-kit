<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Traits\DebugInfo\Info;

use Feralygon\Kit\Exception as KitException;
use Feralygon\Kit\Traits\DebugInfo\Info;

/**
 * @property-read \Feralygon\Kit\Traits\DebugInfo\Info $info [strict]
 * <p>The info instance.</p>
 * @see \Feralygon\Kit\Traits\DebugInfo\Info
 */
abstract class Exception extends KitException
{
	//Implemented protected methods (Feralygon\Kit\Exception\Traits\PropertiesLoader)
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		$this->addProperty('info')->setAsStrictObject(Info::class);
	}
}
