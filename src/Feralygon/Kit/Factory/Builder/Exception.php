<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Factory\Builder;

use Feralygon\Kit\Exception as KitException;
use Feralygon\Kit\Factory\Builder;

/**
 * Factory builder exception class.
 * 
 * @since 1.0.0
 * @property-read \Feralygon\Kit\Factory\Builder $builder <p>The builder instance.</p>
 * @see \Feralygon\Kit\Factory\Builder
 */
abstract class Exception extends KitException
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function buildProperties() : void
	{
		$this->addProperty('builder')->setAsStrictObject(Builder::class)->setAsRequired();
	}
}
