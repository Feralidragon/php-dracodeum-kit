<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components\Input\Structures;

use Dracodeum\Kit\Structure;
use Dracodeum\Kit\Components\Input\Components\Modifier\Structures\Schema as ModifierSchema;
use Dracodeum\Kit\Utilities\Type as UType;

/**
 * @property string $name [coercive]
 * <p>The name.<br>
 * It cannot be empty.</p>
 * @property bool $nullable [coercive] [default = false]
 * <p>Allow null values.</p>
 * @property mixed $data [default = null]
 * <p>The data.</p>
 * @property \Dracodeum\Kit\Components\Input\Components\Modifier\Structures\Schema[] $modifiers [coercive] [default = []]
 * <p>The modifier schema instances.</p>
 */
class Schema extends Structure
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		$this->addProperty('name')->setAsString(true);
		$this->addProperty('nullable')->setAsBoolean()->setDefaultValue(false);
		$this->addProperty('data')->setDefaultValue(null);
		$this->addProperty('modifiers')
			->setAsArray(function (&$key, &$value): bool {
				return is_object($value) && UType::isA($value, ModifierSchema::class);
			}, true)
			->setDefaultValue([])
		;
	}
}
