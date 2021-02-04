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
	/** Catastrophic event in the application, requiring immediate attention and action. */
	public const EMERGENCY = 0;
	
	/** Error event of utmost importance, requiring immediate attention. */
	public const ALERT = 1;
	
	/**
	 * Critical error event which resulted in a complete interruption in the execution of the application and cannot be 
	 * recovered from, which must be checked and corrected.
	 */
	public const CRITICAL = 2;
	
	/** Error event occurred during the execution of the application, which should be checked and corrected. */
	public const ERROR = 3;
	
	/**
	 * Warning event referring to a possible future error which may affect the execution of the application, 
	 * which should be checked and corrected.
	 */
	public const WARNING = 4;
	
	/** Significant event which should be checked. */
	public const NOTICE = 5;
	
	/** Informational event without any special importance. */
	public const INFO = 6;
	
	/** Debugging event with limited and basic information. */
	public const DEBUG = 7;
	
	/** Debugging event with a complete and detailed stack trace. */
	public const TRACE = 8;
}
