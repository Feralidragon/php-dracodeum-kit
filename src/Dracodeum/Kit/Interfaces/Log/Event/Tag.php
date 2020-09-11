<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Interfaces\Log\Event;

/** This interface defines a method to get the log event tag from an object. */
interface Tag
{
	//Public methods
	/**
	 * Get log event tag.
	 * 
	 * @return string
	 * <p>The log event tag.</p>
	 */
	public function getLogEventTag(): string;
}
