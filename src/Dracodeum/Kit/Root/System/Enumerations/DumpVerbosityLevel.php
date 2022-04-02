<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
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
	/** Output of relevant properties only. */
	public const LOW = 1;
	
	/** Output of relevant properties only, but with extended information. */
	public const MEDIUM = 2;
	
	/** Full output of all internal properties. */
	public const HIGH = 3;
}
