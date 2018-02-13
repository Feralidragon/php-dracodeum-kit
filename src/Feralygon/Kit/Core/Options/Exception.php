<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Options;

use Feralygon\Kit\Core;
use Feralygon\Kit\Core\Options;

/**
 * Core options exception class.
 * 
 * @since 1.0.0
 * @property-read \Feralygon\Kit\Core\Options|string $options <p>The options instance or class.</p>
 * @see \Feralygon\Kit\Core\Options
 */
abstract class Exception extends Core\Exception
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function buildProperties() : void
	{
		$this->addProperty('options')->setAsObjectClass(Options::class)->setAsRequired();
	}
}
