<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Options;

use Feralygon\Kit\Exception as KitException;
use Feralygon\Kit\Options;

/**
 * Options exception class.
 * 
 * @since 1.0.0
 * @property-read \Feralygon\Kit\Options|string $options <p>The options instance or class.</p>
 * @see \Feralygon\Kit\Options
 */
abstract class Exception extends KitException
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function buildProperties() : void
	{
		$this->addProperty('options')->setAsObjectClass(Options::class)->setAsRequired();
	}
}
