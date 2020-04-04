<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Prototypes;

use Dracodeum\Kit\Prototype;
use Dracodeum\Kit\Structures\Log\Event;

/**
 * @see \Dracodeum\Kit\Components\Logger
 * @see \Dracodeum\Kit\Prototypes\Logger\Interfaces\EventProcessor
 */
abstract class Logger extends Prototype
{
	//Abstract public methods
	/**
	 * Add event instance.
	 * 
	 * @param \Dracodeum\Kit\Structures\Log\Event $event
	 * <p>The event instance to add.</p>
	 * @return void
	 */
	abstract public function addEvent(Event $event): void;
}
