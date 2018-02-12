<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Components\Input\Immutables;

use Feralygon\Kit\Core\Immutable;
use Feralygon\Kit\Core\Components\Input\Components\Modifier\Immutables\Schema as ModifierSchema;
use Feralygon\Kit\Core\Utilities\Type as UType;

/**
 * Core input component schema immutable class.
 * 
 * @since 1.0.0
 * @property-read string $name <p>The name.</p>
 * @property-read mixed $data [default = null] <p>The data.</p>
 * @property-read \Feralygon\Kit\Core\Components\Input\Components\Modifier\Immutables\Schema[] $modifiers [default = []] 
 * <p>The modifier schema instances.</p>
 * @see \Feralygon\Kit\Core\Components\Input
 */
class Schema extends Immutable
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function buildProperties() : void
	{
		$this->addProperty('name')->setAsString(true)->setAsRequired();
		$this->addProperty('data')->setDefaultValue(null);
		$this->addProperty('modifiers')
			->setAsArray(function (&$key, &$value) : bool {
				return is_object($value) && UType::isA($value, ModifierSchema::class);
			}, true)
			->setDefaultValue([])
		;
	}
}
