<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Primitives\Text;

use Dracodeum\Kit\Primitive\Exception as PrimitiveException;
use Dracodeum\Kit\Primitives\Text as Primitive;

abstract class Exception extends PrimitiveException
{
	//Implemented protected static methods
	/** {@inheritdoc} */
	protected static function getPrimitiveClass(): string
	{
		return Primitive::class;
	}
}
