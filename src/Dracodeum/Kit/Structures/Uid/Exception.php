<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Structures\Uid;

use Dracodeum\Kit\Exception as KException;
use Dracodeum\Kit\Structures\Uid;

/**
 * @property-read \Dracodeum\Kit\Structures\Uid|string $uid
 * <p>The UID instance or class.</p>
 */
abstract class Exception extends KException
{
	//Implemented protected methods (Dracodeum\Kit\Exception\Traits\PropertiesLoader)
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		$this->addProperty('uid')->setAsObjectClass(Uid::class);
	}
}
