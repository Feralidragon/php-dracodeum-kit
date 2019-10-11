<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Options;

use Feralygon\Kit\Exception as KitException;
use Feralygon\Kit\Options;

/**
 * @property-read \Feralygon\Kit\Options|string $options [coercive = object or class]
 * <p>The options instance or class.</p>
 */
abstract class Exception extends KitException
{
	//Implemented protected methods (Feralygon\Kit\Exception\Traits\PropertiesLoader)
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		$this->addProperty('options')->setAsObjectClass(Options::class);
	}
}
