<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Factory\Objects\Type;

use Feralygon\Kit\Exception as KitException;
use Feralygon\Kit\Factory\Objects\Type;

/**
 * Factory type object exception class.
 * 
 * @since 1.0.0
 * @property-read \Feralygon\Kit\Factory\Objects\Type $type <p>The type instance.</p>
 * @see \Feralygon\Kit\Factory\Objects\Type
 */
abstract class Exception extends KitException
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function buildProperties() : void
	{
		$this->addProperty('type')->setAsStrictObject(Type::class)->setAsRequired();
	}
}
