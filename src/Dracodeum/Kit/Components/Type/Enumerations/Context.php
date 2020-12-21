<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components\Type\Enumerations;

use Dracodeum\Kit\Enumeration;

/**
 * This enumeration represents type contexts, 
 * which are used to define the behavior and output of a type component depending on the given context.
 */
class Context extends Enumeration
{
	//Public constants
	/** Internal context, as the context of the internal application. */
	public const INTERNAL = 'INTERNAL';
	
	/** Configuration context, as the context of a configuration file or environment variable. */
	public const CONFIGURATION = 'CONFIGURATION';
	
	/** Interface context, as the context of an API, CLI or similar interface. */
	public const INTERFACE = 'INTERFACE';
}
