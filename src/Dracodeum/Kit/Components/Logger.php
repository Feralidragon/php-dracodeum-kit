<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components;

use Dracodeum\Kit\Component;
use Dracodeum\Kit\Factories\Component as Factory;
use Dracodeum\Kit\Prototypes\Logger as Prototype;
use Dracodeum\Kit\Prototypes\Logger\Interfaces as PrototypeInterfaces;
use Dracodeum\Kit\Structures\Log\Event;

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
	 * @param \Dracodeum\Kit\Structures\Log\Event|array $event
	 * <p>The event to add, as an instance or <samp>name => value</samp> pairs.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function addEvent($event): Logger
	{
		//initialize
		$event = Event::coerce($event, true);
		$prototype = $this->getPrototype();
		
		//process
		if ($prototype instanceof PrototypeInterfaces\EventProcessor) {
			$prototype->processEvent($event);
		}
		
		//add
		$prototype->addEvent($event->setAsReadonly(true));
		
		//return
		return $this;
	}
}
