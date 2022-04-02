<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Interfaces\Log\Event;

/** This interface defines a method to get the log event data from an object. */
interface Data
{
	//Public methods
	/**
	 * Get log event data.
	 * 
	 * @return mixed
	 * <p>The log event data.</p>
	 */
	public function getLogEventData();
}
