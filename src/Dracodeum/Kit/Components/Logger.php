<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components;

use Dracodeum\Kit\Component;
use Dracodeum\Kit\Structures\Log\Event;
use Dracodeum\Kit\Factories\Component as Factory;
use Dracodeum\Kit\Prototypes\Logger as Prototype;
use Dracodeum\Kit\Traits\LazyProperties\Property;
use Dracodeum\Kit\Enumerations\Log\Level as ELevel;

/**
 * This component represents a logger which processes and persists log events.
 * 
 * @property-write enum<\Dracodeum\Kit\Enumerations\Log\Level>|null $min_level [writeonce] [transient] [default = null]  
 * The minimum allowed level to add a given event with.
 * 
 * @property-write enum<\Dracodeum\Kit\Enumerations\Log\Level>|null $max_level [writeonce] [transient] [default = null]  
 * The maximum allowed level to add a given event with.
 * 
 * @method \Dracodeum\Kit\Prototypes\Logger getPrototype() [protected]
 * 
 * @see \Dracodeum\Kit\Prototypes\Logger
 */
class Logger extends Component
{
	//Private properties
	/** @var int|null */
	private $min_level = null;
	
	/** @var int|null */
	private $max_level = null;
	
	
	
	//Implemented public static methods
	/** {@inheritdoc} */
	public static function getPrototypeBaseClass(): string
	{
		return Prototype::class;
	}
	
	
	
	//Implemented protected static methods (Dracodeum\Kit\Component\Traits\DefaultBuilder)
	/** {@inheritdoc} */
	protected static function getDefaultBuilder(): ?callable
	{
		return [Factory::class, 'logger'];
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Component\Traits\PropertyBuilder)
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		switch ($name) {
			case 'min_level':
				//no break
			case 'max_level':
				return $this->createProperty()
					->setMode('w--')
					->setAsEnumerationValue(ELevel::class, true)
					->bind(self::class)
				;
		}
		return null;
	}
	
	
	
	//Final public methods
	/**
	 * Add event.
	 * 
	 * @param coercible:struct<\Dracodeum\Kit\Structures\Log\Event> $event
	 * The event to add.
	 * 
	 * @return $this
	 * This instance, for chaining purposes.
	 */
	final public function addEvent($event): Logger
	{
		//level
		if (
			($this->min_level !== null && $event->level < $this->min_level) || 
			($this->max_level !== null && $event->level > $this->max_level)
		) {
			return $this;
		}
		
		//event
		$event = Event::coerce($event);
		if (!$event->isReadonly()) {
			$event = $event->clone()->setAsReadonly();
		}
		
		//add
		$this->getPrototype()->addEvent($event);
		
		//return
		return $this;
	}
}
