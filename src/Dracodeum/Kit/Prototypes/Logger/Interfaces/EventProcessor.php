<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Prototypes\Logger\Interfaces;

use Dracodeum\Kit\Structures\Log\Event;

/** This interface defines a method to process an event instance in a logger prototype. */
interface EventProcessor
{
	//Public methods
	/**
	 * Process a given event instance.
	 * 
	 * @param \Dracodeum\Kit\Structures\Log\Event $event
	 * <p>The event instance to process.</p>
	 * @return void
	 */
	public function processEvent(Event $event): void;
}
