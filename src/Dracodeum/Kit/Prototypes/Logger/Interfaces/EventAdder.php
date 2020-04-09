<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Prototypes\Logger\Interfaces;

use Dracodeum\Kit\Structures\Log\Event;

/** This interface defines a method to add an event instance in a logger prototype. */
interface EventAdder
{
	//Public methods
	/**
	 * Add event instance.
	 * 
	 * @param \Dracodeum\Kit\Structures\Log\Event $event
	 * <p>The event instance to add.</p>
	 * @return void
	 */
	public function addEvent(Event $event): void;
}
