<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Traits\DebugInfo\Info;

use Dracodeum\Kit\Exception as KitException;
use Dracodeum\Kit\Traits\DebugInfo\Info;

/**
 * @property-read \Dracodeum\Kit\Traits\DebugInfo\Info $info [strict]
 * <p>The info instance.</p>
 */
abstract class Exception extends KitException
{
	//Implemented protected methods (Dracodeum\Kit\Exception\Traits\PropertiesLoader)
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		$this->addProperty('info')->setAsStrictObject(Info::class);
	}
}
