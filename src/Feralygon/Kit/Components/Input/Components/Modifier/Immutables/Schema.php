<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Components\Input\Components\Modifier\Immutables;

use Feralygon\Kit\Immutable;

/**
 * Input modifier component schema immutable class.
 * 
 * @since 1.0.0
 * @property-read string $name <p>The name.</p>
 * @property-read mixed $data [default = null] <p>The data.</p>
 * @see \Feralygon\Kit\Components\Input\Components\Modifier
 */
class Schema extends Immutable
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function buildProperties() : void
	{
		$this->addProperty('name')->setAsString(true)->setAsRequired();
		$this->addProperty('data')->setDefaultValue(null);
	}
}
