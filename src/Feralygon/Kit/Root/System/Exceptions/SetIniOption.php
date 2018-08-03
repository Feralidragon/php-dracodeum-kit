<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Root\System\Exceptions;

use Feralygon\Kit\Root\System\Exception;

/**
 * Root system <code>setIniOption</code> method exception.
 * 
 * @since 1.0.0
 * @property-read string $name
 * <p>The name.</p>
 * @property-read mixed $value
 * <p>The value.</p>
 */
abstract class SetIniOption extends Exception
{
	//Implemented protected methods (Feralygon\Kit\Exception\Traits\PropertiesLoader)
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		$this->addProperty('name')->setAsString();
		$this->addProperty('value');
	}
}
