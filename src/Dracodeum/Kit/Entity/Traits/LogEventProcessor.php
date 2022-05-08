<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Entity\Traits;

use Dracodeum\Kit\Structures\Log\Event as LogEvent;

/** This trait defines a method to process a log event instance in an entity. */
trait LogEventProcessor
{
	//Protected methods
	/**
	 * Process a given log event instance.
	 * 
	 * @param \Dracodeum\Kit\Structures\Log\Event $event
	 * <p>The log event instance to process.</p>
	 */
	protected function processLogEvent(LogEvent $event): void {}
}
