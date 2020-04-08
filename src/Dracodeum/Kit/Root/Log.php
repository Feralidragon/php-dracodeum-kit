<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Root;

use Dracodeum\Kit\Interfaces\Uninstantiable as IUninstantiable;
use Dracodeum\Kit\Traits;
use Dracodeum\Kit\Root\Log\Options;
use Dracodeum\Kit\Components\Logger;
use Dracodeum\Kit\Enumerations\Log\Level as ELevel;
use Dracodeum\Kit\Structures\Log\Event;
use Dracodeum\Kit\Utilities\Call as UCall;

/** This class is used to statically add log events using loggers to process and persist those events. */
final class Log implements IUninstantiable
{
	//Traits
	use Traits\Uninstantiable;
	
	
	
	//Private static properties
	/** @var \Dracodeum\Kit\Components\Logger[] */
	private static $loggers = [];
	
	
	
	//Final public static methods
	/**
	 * Add logger.
	 * 
	 * @param \Dracodeum\Kit\Components\Logger|\Dracodeum\Kit\Prototypes\Logger|string $logger
	 * <p>The logger component instance or name, or prototype instance, class or name, to add.</p>
	 * @param array $properties [default = []]
	 * <p>The properties to add with, as <samp>name => value</samp> pairs, 
	 * if a component name, or a prototype class or name, is given.<br>
	 * Required properties may also be given as an array of values (<samp>[value1, value2, ...]</samp>), 
	 * in the same order as how these properties were first declared.</p>
	 * @return void
	 */
	final public static function addLogger($logger, array $properties = []): void
	{
		self::$loggers[] = Logger::coerce($logger, $properties);
	}
	
	/**
	 * Add event.
	 * 
	 * @param \Dracodeum\Kit\Structures\Log\Event|array $event
	 * <p>The event to add, as an instance or <samp>name => value</samp> pairs.</p>
	 * @return void
	 */
	final public static function addEvent($event): void
	{
		if (!empty(self::$loggers)) {
			$event = Event::coerce($event);
			foreach (self::$loggers as $logger) {
				$logger->addEvent($event);
			}
		}
	}
	
	/**
	 * Create an event instance with a given severity level and message.
	 * 
	 * @see \Dracodeum\Kit\Enumerations\Log\Level
	 * @param int|string $level
	 * <p>The severity level to create with, 
	 * as a name or value from the <code>Dracodeum\Kit\Enumerations\Log\Level</code> enumeration.</p>
	 * @param string $message
	 * <p>The message to create with, optionally set with placeholders as <samp>{{placeholder}}</samp>.<br>
	 * <br>
	 * If set, then placeholders must be exclusively composed by identifiers, 
	 * which are defined as words which must start with a letter (<samp>a-z</samp> and <samp>A-Z</samp>) 
	 * or underscore (<samp>_</samp>), and may only contain letters (<samp>a-z</samp> and <samp>A-Z</samp>), 
	 * digits (<samp>0-9</samp>) and underscores (<samp>_</samp>).<br>
	 * <br>
	 * They may also be used with pointers to specific object properties or associative array values, 
	 * by using a dot between identifiers, such as <samp>{{object.property}}</samp>, 
	 * with no limit on the number of chained pointers.<br>
	 * <br>
	 * If suffixed with opening and closing parenthesis, such as <samp>{{object.method()}}</samp>, 
	 * then the identifiers are interpreted as getter method calls, but they cannot be given any arguments.</p>
	 * @param \Dracodeum\Kit\Root\Log\Options\Event|array|null $options [default = null]
	 * <p>Additional options to use, as an instance or <samp>name => value</samp> pairs.</p>
	 * @return \Dracodeum\Kit\Structures\Log\Event
	 * <p>The created event instance with the given severity level and message..</p>
	 */
	final public static function createEvent($level, string $message, $options = null): Event
	{
		//initialize
		$level = ELevel::coerceValue($level);
		$options = Options\Event::coerce($options);
		
		//message
		//TODO
		
		//return
		return Event::build([
			'level' => $level,
			'message' => $message,
			'class' => $options->object_class ?? UCall::stackPreviousObjectClass($options->stack_offset),
			'function' => $options->function_name ?? UCall::stackPreviousName(false, false, $options->stack_offset),
			'name' => $options->name,
			'tag' => $options->tag,
			'data' => $options->data,
			'tags' => $options->tags
		]);
	}
	
	/**
	 * Process and persist event with a given severity level and message.
	 * 
	 * @see \Dracodeum\Kit\Enumerations\Log\Level
	 * @param int|string $level
	 * <p>The severity level to log with, 
	 * as a name or value from the <code>Dracodeum\Kit\Enumerations\Log\Level</code> enumeration.</p>
	 * @param string $message
	 * <p>The message to log with, optionally set with placeholders as <samp>{{placeholder}}</samp>.<br>
	 * <br>
	 * If set, then placeholders must be exclusively composed by identifiers, 
	 * which are defined as words which must start with a letter (<samp>a-z</samp> and <samp>A-Z</samp>) 
	 * or underscore (<samp>_</samp>), and may only contain letters (<samp>a-z</samp> and <samp>A-Z</samp>), 
	 * digits (<samp>0-9</samp>) and underscores (<samp>_</samp>).<br>
	 * <br>
	 * They may also be used with pointers to specific object properties or associative array values, 
	 * by using a dot between identifiers, such as <samp>{{object.property}}</samp>, 
	 * with no limit on the number of chained pointers.<br>
	 * <br>
	 * If suffixed with opening and closing parenthesis, such as <samp>{{object.method()}}</samp>, 
	 * then the identifiers are interpreted as getter method calls, but they cannot be given any arguments.</p>
	 * @param \Dracodeum\Kit\Root\Log\Options\Event|array|null $options [default = null]
	 * <p>Additional options to use, as an instance or <samp>name => value</samp> pairs.</p>
	 * @return void
	 */
	final public static function event($level, string $message, $options = null): void
	{
		self::addEvent(self::createEvent($level, $message, $options));
	}
}
