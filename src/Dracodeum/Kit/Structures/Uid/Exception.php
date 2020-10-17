<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Structures\Uid;

use Dracodeum\Kit\Exception as KitException;
use Dracodeum\Kit\Structures\Uid;

/**
 * @property-read \Dracodeum\Kit\Structures\Uid|string $uid
 * <p>The UID instance or class.</p>
 */
abstract class Exception extends KitException
{
	//Implemented protected methods (Dracodeum\Kit\Exception\Traits\PropertiesLoader)
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		$this->addProperty('uid')->setAsObjectClass(Uid::class);
	}
}
