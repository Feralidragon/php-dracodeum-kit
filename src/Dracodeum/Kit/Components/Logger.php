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
 * @property-write int|null $min_level [writeonce] [transient] [coercive = enumeration value] [default = null]
 * <p>The minimum allowed level to add a given event with, 
 * as a value from the <code>Dracodeum\Kit\Enumerations\Log\Level</code> enumeration.</p>
 * @property-write int|null $max_level [writeonce] [transient] [coercive = enumeration value] [default = null]
 * <p>The maximum allowed level to add a given event with, 
 * as a value from the <code>Dracodeum\Kit\Enumerations\Log\Level</code> enumeration.</p>
 * @see \Dracodeum\Kit\Prototypes\Logger
 * @see \Dracodeum\Kit\Enumerations\Log\Level
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
	 * @param \Dracodeum\Kit\Structures\Log\Event|array $event
	 * <p>The event to add, as an instance or a set of <samp>name => value</samp> pairs.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
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
			$event = $event->clone(true)->setAsReadonly();
			$event->tags->setAsReadonly();
		}
		
		//add
		$this->getPrototype()->addEvent($event);
		
		//return
		return $this;
	}
}
