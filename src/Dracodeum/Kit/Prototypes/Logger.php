<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Prototypes;

use Dracodeum\Kit\Prototype;
use Dracodeum\Kit\Structures\Log\Event;

/** @see \Dracodeum\Kit\Components\Logger */
abstract class Logger extends Prototype
{
	//Abstract public methods
	/**
	 * Add event instance.
	 * 
	 * @param \Dracodeum\Kit\Structures\Log\Event $event
	 * <p>The event instance to add.</p>
	 */
	abstract public function addEvent(Event $event): void;
}
