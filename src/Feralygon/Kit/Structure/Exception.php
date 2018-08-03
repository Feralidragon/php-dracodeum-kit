<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Structure;

use Feralygon\Kit\Exception as KitException;
use Feralygon\Kit\Structure;

/**
 * @since 1.0.0
 * @property-read \Feralygon\Kit\Structure|string $structure
 * <p>The structure instance or class.</p>
 * @see \Feralygon\Kit\Structure
 */
abstract class Exception extends KitException
{
	//Implemented protected methods (Feralygon\Kit\Exception\Traits\PropertiesLoader)
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		$this->addProperty('structure')->setAsObjectClass(Structure::class);
	}
}
