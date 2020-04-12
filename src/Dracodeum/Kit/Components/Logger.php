<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components;

use Dracodeum\Kit\Component;
use Dracodeum\Kit\Components\Logger\Structures;
use Dracodeum\Kit\Factories\Component as Factory;
use Dracodeum\Kit\Prototypes\Logger as Prototype;
use Dracodeum\Kit\Prototypes\Logger\Interfaces as PrototypeInterfaces;

/**
 * This component represents a logger which processes and persists log events.
 * 
 * @see \Dracodeum\Kit\Prototypes\Logger
 */
class Logger extends Component
{
	//Implemented public static methods
	/** {@inheritdoc} */
	public static function getBasePrototypeClass(): string
	{
		return Prototype::class;
	}
	
	
	
	//Implemented protected static methods (Dracodeum\Kit\Component\Traits\DefaultBuilder)
	/** {@inheritdoc} */
	protected static function getDefaultBuilder(): ?callable
	{
		return [Factory::class, 'logger'];
	}
	
	
	
	//Final public methods
	/**
	 * Add event.
	 * 
	 * @param \Dracodeum\Kit\Components\Logger\Structures\Event|array $event
	 * <p>The event to add, as an instance or <samp>name => value</samp> pairs.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function addEvent($event): Logger
	{
		$prototype = $this->getPrototype();
		if ($prototype instanceof PrototypeInterfaces\EventAdder) {
			$event = Structures\Event::coerce($event, true);
			if ($prototype instanceof PrototypeInterfaces\EventProcessor) {
				$prototype->processEvent($event);
			}
			$prototype->addEvent($event->setAsReadonly(true));
		}
		return $this;
	}
}
