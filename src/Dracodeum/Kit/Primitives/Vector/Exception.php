<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Primitives\Vector;

use Dracodeum\Kit\Exception as KitException;
use Dracodeum\Kit\Primitives\Vector;

/**
 * @property-read \Dracodeum\Kit\Primitives\Vector|string $vector [coercive = object or class]
 * <p>The vector instance or class.</p>
 */
abstract class Exception extends KitException
{
	//Implemented protected methods (Dracodeum\Kit\Exception\Traits\PropertiesLoader)
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		$this->addProperty('vector')->setAsObjectClass(Vector::class);
	}
}
