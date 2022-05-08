<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Enumeration;

use Dracodeum\Kit\Exception as KException;
use Dracodeum\Kit\Enumeration;

/**
 * @property-read string $enumeration [strict = class]
 * <p>The enumeration class.</p>
 */
abstract class Exception extends KException
{
	//Implemented protected methods (Dracodeum\Kit\Exception\Traits\PropertiesLoader)
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		$this->addProperty('enumeration')->setAsStrictClass(Enumeration::class);
	}
}
