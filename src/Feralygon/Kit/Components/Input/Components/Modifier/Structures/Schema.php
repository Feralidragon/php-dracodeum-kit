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
 * @property string $type [coercive]
 * <p>The type.<br>
 * It cannot be empty.</p>
 * @property string|null $subtype [coercive] [default = null]
 * <p>The subtype.<br>
 * If set, then it cannot be empty.</p>
 * @property mixed $data [default = null]
 * <p>The data.</p>
 */
class Schema extends Structure
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		$this->addProperty('name')->setAsString(true);
		$this->addProperty('type')->setAsString(true);
		$this->addProperty('subtype')->setAsString(true, true)->setDefaultValue(null);
		$this->addProperty('data')->setDefaultValue(null);
	}
}
