<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Attributes\Property;

use Attribute;

/**
 * {@inheritdoc}
 * 
 * This attribute defines the property type as strict.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class Strict extends Type
{
	//Implemented final protected methods
	/** {@inheritdoc} */
	final protected function isStrict(): bool
	{
		return true;
	}
}
