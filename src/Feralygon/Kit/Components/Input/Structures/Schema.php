<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Components\Input\Structures;

use Feralygon\Kit\Structure;
use Feralygon\Kit\Components\Input\Components\Modifier\Structures\Schema as ModifierSchema;
use Feralygon\Kit\Utilities\Type as UType;

/**
 * @since 1.0.0
 * @property string $name <p>The name.<br>
 * It cannot be empty.</p>
 * @property bool $nullable [default = false] <p>The nullable state.</p>
 * @property mixed $data [default = null] <p>The data.</p>
 * @property \Feralygon\Kit\Components\Input\Components\Modifier\Structures\Schema[] $modifiers [default = []] 
 * <p>The modifier schema instances.</p>
 * @see \Feralygon\Kit\Components\Input
 */
class Schema extends Structure
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function buildProperties() : void
	{
		$this->addProperty('name')->setAsString(true)->setAsRequired();
		$this->addProperty('nullable')->setAsBoolean()->setDefaultValue(false);
		$this->addProperty('data')->setDefaultValue(null);
		$this->addProperty('modifiers')
			->setAsArray(function (&$key, &$value) : bool {
				return is_object($value) && UType::isA($value, ModifierSchema::class);
			}, true)
			->setDefaultValue([])
		;
	}
}
