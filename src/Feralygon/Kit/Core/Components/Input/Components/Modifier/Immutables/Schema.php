<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Components\Input\Components\Modifier\Immutables;

use Feralygon\Kit\Core\Immutable;

/**
 * Core input modifier component schema immutable class.
 * 
 * @since 1.0.0
 * @property-read string $name <p>The name.<br>
 * It cannot be empty.</p>
 * @property-read mixed $data [default = null] <p>The data.</p>
 * @see \Feralygon\Kit\Core\Components\Input\Components\Modifier
 */
class Schema extends Immutable
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function loadProperties() : void
	{
		$this->addStringProperty('name', true, true);
		$this->addProperty('data');
	}
}
