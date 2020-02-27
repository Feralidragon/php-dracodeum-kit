<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Utilities\Call\Options;

use Dracodeum\Kit\Traits\LazyProperties\Property;

/**
 * Call utility <code>haltExecution</code> method options.
 * 
 * @property mixed $value [default = null]
 * <p>The value, returned by the given executed function, to use.</p>
 * @property \Exception|null $exception [strict] [default = null]
 * <p>The exception instance, thrown from the given executed function, to use.</p>
 */
class HaltExecution extends Halt
{
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		switch ($name) {
			case 'value':
				return $this->createProperty()->setDefaultValue(null);
			case 'exception':
				return $this->createProperty()->setAsStrictObject(\Exception::class, true)->setDefaultValue(null);
		}
		return parent::buildProperty($name);
	}
}
