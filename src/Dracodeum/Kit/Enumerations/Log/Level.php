<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Enumerations\Log;

use Dracodeum\Kit\Enumeration;

/**
 * This enumeration represents log severity levels.
 * 
 * @see http://tools.ietf.org/html/rfc5424
 */
class Level extends Enumeration
{
	//Public constants
	/**
	 * Emergency log severity level, used to represent a catastrophic event such as a complete application shutdown,
	 * requiring immediate attention and action.
	 */
	public const EMERGENCY = 0;
	
	/**
	 * Alert log severity level, used to represent an error event of utmost importance, requiring immediate attention.
	 */
	public const ALERT = 1;
	
	/**
	 * Critical log severity level, used to represent a critical error event which resulted in a complete interruption 
	 * in the execution of the application and cannot be recovered from, which must be checked and corrected.
	 */
	public const CRITICAL = 2;
	
	/**
	 * Error log severity level, used to represent an error event occurred during the execution of the application, 
	 * which should be checked and corrected.
	 */
	public const ERROR = 3;
	
	/**
	 * Warning log severity level, used to represent a warning event referring to a possible future error which may 
	 * affect the execution of the application, which should be checked and corrected.
	 */
	public const WARNING = 4;
	
	/** Notice log severity level, used to represent a significant event which should be checked. */
	public const NOTICE = 5;
	
	/** Information log severity level, used to represent an informational event without any special importance. */
	public const INFO = 6;
	
	/** Debug log severity level, used to represent a debugging event with limited and basic information. */
	public const DEBUG = 7;
	
	/** Trace log severity level, used to represent a debugging event with a complete and detailed stack trace. */
	public const TRACE = 8;
}
