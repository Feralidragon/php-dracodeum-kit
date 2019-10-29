<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Enumerations;

use Dracodeum\Kit\Enumeration;

/** This enumeration represents text cases (lowercase and uppercase). */
class TextCase extends Enumeration
{
	//Public constants
	/** Lowercase text. */
	public const LOWER = CASE_LOWER;
	
	/** Uppercase text. */
	public const UPPER = CASE_UPPER;
}
