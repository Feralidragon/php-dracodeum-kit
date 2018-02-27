<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Components\Input;

use Feralygon\Kit\Exception as KitException;
use Feralygon\Kit\Components\Input as Component;

/**
 * Input component exception class.
 * 
 * @since 1.0.0
 * @property-read \Feralygon\Kit\Components\Input $component <p>The input component instance.</p>
 * @property-read \Feralygon\Kit\Prototypes\Input $prototype <p>The input prototype instance.</p>
 * @see \Feralygon\Kit\Components\Input
 */
abstract class Exception extends KitException
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function buildProperties() : void
	{
		$this->addProperty('component')->setAsStrictObject(Component::class)->setAsRequired();
		$this->addProperty('prototype')->setAsStrictObject(Component::getPrototypeBaseClass())->setAsRequired();
	}
}