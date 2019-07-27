<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Components\Input\Components\Modifier\Structures;

use Feralygon\Kit\Structure;

/**
 * @property string $name [coercive]
 * <p>The name.<br>
 * It cannot be empty.</p>
 * @property mixed $data [default = null]
 * <p>The data.</p>
 * @see \Feralygon\Kit\Components\Input\Components\Modifier
 */
class Schema extends Structure
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		$this->addProperty('name')->setAsString(true);
		$this->addProperty('data')->setDefaultValue(null);
	}
}
