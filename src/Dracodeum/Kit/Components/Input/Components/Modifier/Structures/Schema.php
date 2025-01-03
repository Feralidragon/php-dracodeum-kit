<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components\Input\Components\Modifier\Structures;

use Dracodeum\Kit\Structure;

/**
 * @property string $name
 * <p>The name.</p>
 * @property string $type
 * <p>The type.</p>
 * @property string|null $subtype [default = null]
 * <p>The subtype.</p>
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
