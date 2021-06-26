<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
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
final class strict extends type
{
	//Implemented final protected methods
	/** {@inheritdoc} */
	final protected function isStrict(): bool
	{
		return true;
	}
}
