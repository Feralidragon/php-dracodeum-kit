<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Primitives\Vector;

use Feralygon\Kit\Exception as KitException;
use Feralygon\Kit\Primitives\Vector;

/**
 * @property-read \Feralygon\Kit\Primitives\Vector|string $vector [coercive = object or class]
 * <p>The vector instance or class.</p>
 * @see \Feralygon\Kit\Primitives\Vector
 */
abstract class Exception extends KitException
{
	//Implemented protected methods (Feralygon\Kit\Exception\Traits\PropertiesLoader)
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		$this->addProperty('vector')->setAsObjectClass(Vector::class);
	}
}
