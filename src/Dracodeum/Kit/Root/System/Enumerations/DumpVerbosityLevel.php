<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Root\System\Enumerations;

use Dracodeum\Kit\Enumeration;

/**
 * This enumeration represents dump verbosity levels, which are used to define which level of verbosity to use 
 * in the output of the PHP <code>var_dump</code> function when an object is given.
 */
class DumpVerbosityLevel extends Enumeration
{
	//Public constants
	/** Low verbosity level, with the output of relevant properties only. */
	public const LOW = 1;
	
	/** Medium verbosity level, with the output of relevant properties only, but with extended information. */
	public const MEDIUM = 2;
	
	/** High verbosity level, with the full output of all internal properties. */
	public const HIGH = 3;
}
