<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Root;

use Dracodeum\Kit\Interfaces\Uninstantiable as IUninstantiable;
use Dracodeum\Kit\Traits;
use Dracodeum\Kit\Interfaces\Log\Event\{
	Data as IEventData,
	Tag as IEventTag
};
use Dracodeum\Kit\Root\Log\Options;
use Dracodeum\Kit\Components\Logger;
use Dracodeum\Kit\Structures\Log\Event;
use Dracodeum\Kit\Enumerations\Log\Level as ELevel;
use Dracodeum\Kit\Utilities\{
	Base32 as UBase32,
	Call as UCall,
	Text as UText
};
use Dracodeum\Kit\Utilities\Text\Options\Stringify as StringOptions;

/** This class is used to statically add log messages and events using loggers to process and persist them. */
final class Log implements IUninstantiable
{
	//Traits
	use Traits\Uninstantiable;
	
	
	
	//Private constants
	/** Number of bytes to use to generate an event ID. */
	private const EVENT_ID_GENERATION_BYTES = 12;
	
	
	
	//Private static properties
	/** @var \Dracodeum\Kit\Components\Logger[] */
	private static $loggers = [];
	
	/** @var callable[] */
	private static $event_processors = [];
	
	
	
	//Final public static methods
	/**
	 * Add logger.
	 * 
	 * @param \Dracodeum\Kit\Components\Logger|\Dracodeum\Kit\Prototypes\Logger|string $logger
	 * <p>The logger component instance or name, or prototype instance, class or name, to add.</p>
	 * @param array $properties [default = []]
	 * <p>The properties to add with, as a set of <samp>name => value</samp> pairs, 
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
	 * Add event processor function.
	 * 
	 * @param callable $processor
	 * <p>The processor function to use to process a given event instance.<br>
	 * It is expected to be compatible with the following signature:<br>
	 * <br>
	 * <code>function (\Dracodeum\Kit\Structures\Log\Event $event): void</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>\Dracodeum\Kit\Structures\Log\Event $event</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The event instance to process.<br>
	 * <br>
	 * Return: <code><b>void</b></code></p>
	 * @return void
	 */
	final public static function addEventProcessor(callable $processor): void
	{
		UCall::assert('processor', $processor, function (Event $event): void {});
		self::$event_processors[] = $processor;
	}
	
	/**
	 * Add event.
	 * 
	 * @param \Dracodeum\Kit\Structures\Log\Event|array $event
	 * <p>The event to add, as an instance or a set of <samp>name => value</samp> pairs.</p>
	 * @return void
	 */
	final public static function addEvent($event): void
	{
		//process
		$event = Event::coerce($event, true);
		foreach (self::$event_processors as $processor) {
			$processor($event);
		}
		self::processEvent($event);
		
		//add
		$event->setAsReadonly();
		$event->tags->unique()->setAsReadonly();
		foreach (self::$loggers as $logger) {
			$logger->addEvent($event);
		}
	}
	
	/**
	 * Generate an event ID.
	 * 
	 * @return string
	 * <p>The generated event ID.</p>
	 */
	final public static function generateEventId(): string
	{
		return UBase32::encode(random_bytes(self::EVENT_ID_GENERATION_BYTES), true);
	}
	
	/**
	 * Compose an event tag with a given set of strings.
	 * 
	 * @param string[] $strings
	 * <p>The set of strings to compose with.</p>
	 * @return string
	 * <p>The composed event tag with the given set of strings.</p>
	 */
	final public static function composeEventTag(array $strings): string
	{
		//guard
		if (empty($strings)) {
			UCall::haltParameter('strings', $strings, ['error_message' => "An empty set of strings is not allowed."]);
		}
		
		//encode
		foreach ($strings as &$string) {
			$string = str_replace(['%', ':'], ['%25', '%3A'], $string);
		}
		unset($string);
		
		//return
		return implode(':', $strings);
	}
	
	/**
	 * Decompose a given event tag into a set of strings.
	 * 
	 * @param string $tag
	 * <p>The tag to decompose.</p>
	 * @return string[]
	 * <p>The given event tag decomposed into a set of strings.</p>
	 */
	final public static function decomposeEventTag(string $tag): array
	{
		//guard
		if ($tag === '') {
			UCall::haltParameter('tag', $tag, ['error_message' => "An empty tag is not allowed."]);
		}
		
		//decompose
		$strings = explode(':', $tag);
		foreach ($strings as &$string) {
			$string = str_replace(['%3A', '%25'], [':', '%'], $string);
		}
		unset($string);
		
		//return
		return $strings;
	}
	
	/**
	 * Create an event instance with a given level and message.
	 * 
	 * @see \Dracodeum\Kit\Enumerations\Log\Level
	 * @param int|string $level
	 * <p>The level to create with, 
	 * as a name or value from the <code>Dracodeum\Kit\Enumerations\Log\Level</code> enumeration.</p>
	 * @param string $message
	 * <p>The message to create with, optionally set with placeholders as <samp>{{placeholder}}</samp>.<br>
	 * <br>
	 * If set, then placeholders must be exclusively composed of identifiers, 
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
	 * <p>Additional options to use, as an instance or a set of <samp>name => value</samp> pairs.</p>
	 * @return \Dracodeum\Kit\Structures\Log\Event
	 * <p>The created event instance with the given level and message.</p>
	 */
	final public static function createEvent($level, string $message, $options = null): Event
	{
		//initialize
		$level = ELevel::coerceValue($level);
		$options = Options\Event::coerce($options);
		
		//message
		if (!empty($options->parameters)) {
			//string options
			$string_options = StringOptions::coerce($options->string_options, false);
			if (!$string_options->loaded('quote_strings')) {
				$string_options->quote_strings = true;
			}
			
			//fill
			$message = UText::fill($message, $options->parameters, null, [
				'string_options' => $string_options,
				'stringifier' => $options->stringifier
			]);
		}
		
		//return
		return Event::build([
			'timestamp' => 'now',
			'level' => $level,
			'message' => $message,
			'object' => self::getEventObject($options->object_class, $options->stack_offset),
			'class' => $options->object_class ?? UCall::stackPreviousObjectClass($options->stack_offset),
			'function' => $options->function_name ?? UCall::stackPreviousName(false, false, $options->stack_offset),
			'name' => $options->name,
			'data' => $options->data,
			'tags' => $options->tags
		]);
	}
	
	/**
	 * Create an event instance with a given level and message in plural form.
	 * 
	 * @see \Dracodeum\Kit\Enumerations\Log\Level
	 * @param int|string $level
	 * <p>The level to create with, 
	 * as a name or value from the <code>Dracodeum\Kit\Enumerations\Log\Level</code> enumeration.</p>
	 * @param string $message1
	 * <p>The message in singular form to create with, 
	 * optionally set with placeholders as <samp>{{placeholder}}</samp>.<br>
	 * <br>
	 * If set, then placeholders must be exclusively composed of identifiers, 
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
	 * @param string $message2
	 * <p>The message in plural form to create with, 
	 * optionally set with placeholders as <samp>{{placeholder}}</samp>.<br>
	 * <br>
	 * If set, then placeholders must be exclusively composed of identifiers, 
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
	 * @param float $number
	 * <p>The number to use to select either the singular (<var>$message1</var>) or plural (<var>$message2</var>) form 
	 * of the message.</p>
	 * @param string|null $number_placeholder [default = null]
	 * <p>The placeholder to fill with the given number in the final message.</p>
	 * @param \Dracodeum\Kit\Root\Log\Options\PEvent|array|null $options [default = null]
	 * <p>Additional options to use, as an instance or a set of <samp>name => value</samp> pairs.</p>
	 * @return \Dracodeum\Kit\Structures\Log\Event
	 * <p>The created event instance with the given level and message in plural form.</p>
	 */
	final public static function createPEvent(
		$level, string $message1, string $message2, float $number, ?string $number_placeholder = null, $options = null
	): Event
	{
		//initialize
		$level = ELevel::coerceValue($level);
		$options = Options\PEvent::coerce($options);
		
		//string options
		$string_options = StringOptions::coerce($options->string_options, false);
		if (!$string_options->loaded('quote_strings')) {
			$string_options->quote_strings = true;
		}
		
		//message
		$message = UText::pfill(
			$message1, $message2, $number, $number_placeholder, $options->parameters, null, [
				'string_options' => $string_options,
				'stringifier' => $options->stringifier
			]
		);
		
		//return
		return Event::build([
			'timestamp' => 'now',
			'level' => $level,
			'message' => $message,
			'object' => self::getEventObject($options->object_class, $options->stack_offset),
			'class' => $options->object_class ?? UCall::stackPreviousObjectClass($options->stack_offset),
			'function' => $options->function_name ?? UCall::stackPreviousName(false, false, $options->stack_offset),
			'name' => $options->name,
			'data' => $options->data,
			'tags' => $options->tags
		]);
	}
	
	/**
	 * Create an event instance with a given level and throwable instance.
	 * 
	 * @see \Dracodeum\Kit\Enumerations\Log\Level
	 * @param int|string $level
	 * <p>The level to create with, 
	 * as a name or value from the <code>Dracodeum\Kit\Enumerations\Log\Level</code> enumeration.</p>
	 * @param \Throwable $throwable
	 * <p>The throwable instance to create with.</p>
	 * @param \Dracodeum\Kit\Root\Log\Options\ThrowableEvent|array|null $options [default = null]
	 * <p>Additional options to use, as an instance or a set of <samp>name => value</samp> pairs.</p>
	 * @return \Dracodeum\Kit\Structures\Log\Event
	 * <p>The created event instance with the given level and throwable instance.</p>
	 */
	final public static function createThrowableEvent($level, \Throwable $throwable, $options = null): Event
	{
		//initialize
		$level = ELevel::coerceValue($level);
		$options = Options\ThrowableEvent::coerce($options);
		
		//return
		return Event::build([
			'timestamp' => 'now',
			'level' => $level,
			'message' => $throwable->getMessage(),
			'object' => self::getEventObject($options->object_class, $options->stack_offset),
			'class' => $options->object_class ?? UCall::stackPreviousObjectClass($options->stack_offset),
			'function' => $options->function_name ?? UCall::stackPreviousName(false, false, $options->stack_offset),
			'name' => $throwable instanceof \Exception ? 'exception' : 'error',
			'data' => [
				'code' => $throwable->getCode(),
				'file' => $throwable->getFile(),
				'line' => $throwable->getLine(),
				'previous' => $throwable->getPrevious(),
				'trace' => $throwable->getTraceAsString()
			],
			'tags' => $options->tags
		]);
	}
	
	/**
	 * Log event with a given level and message.
	 * 
	 * @see \Dracodeum\Kit\Enumerations\Log\Level
	 * @param int|string $level
	 * <p>The level to log with, 
	 * as a name or value from the <code>Dracodeum\Kit\Enumerations\Log\Level</code> enumeration.</p>
	 * @param string $message
	 * <p>The message to log with, optionally set with placeholders as <samp>{{placeholder}}</samp>.<br>
	 * <br>
	 * If set, then placeholders must be exclusively composed of identifiers, 
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
	 * <p>Additional options to use, as an instance or a set of <samp>name => value</samp> pairs.</p>
	 * @return void
	 */
	final public static function event($level, string $message, $options = null): void
	{
		//initialize
		$options = Options\Event::coerce($options, false);
		$options->stack_offset++;
		
		//add
		self::addEvent(self::createEvent($level, $message, $options));
	}
	
	/**
	 * Log event with a given level and message in plural form.
	 * 
	 * @see \Dracodeum\Kit\Enumerations\Log\Level
	 * @param int|string $level
	 * <p>The level to log with, 
	 * as a name or value from the <code>Dracodeum\Kit\Enumerations\Log\Level</code> enumeration.</p>
	 * @param string $message1
	 * <p>The message in singular form to log with, 
	 * optionally set with placeholders as <samp>{{placeholder}}</samp>.<br>
	 * <br>
	 * If set, then placeholders must be exclusively composed of identifiers, 
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
	 * @param string $message2
	 * <p>The message in plural form to log with, optionally set with placeholders as <samp>{{placeholder}}</samp>.<br>
	 * <br>
	 * If set, then placeholders must be exclusively composed of identifiers, 
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
	 * @param float $number
	 * <p>The number to use to select either the singular (<var>$message1</var>) or plural (<var>$message2</var>) form 
	 * of the message.</p>
	 * @param string|null $number_placeholder [default = null]
	 * <p>The placeholder to fill with the given number in the final message.</p>
	 * @param \Dracodeum\Kit\Root\Log\Options\PEvent|array|null $options [default = null]
	 * <p>Additional options to use, as an instance or a set of <samp>name => value</samp> pairs.</p>
	 * @return void
	 */
	final public static function pevent(
		$level, string $message1, string $message2, float $number, ?string $number_placeholder = null, $options = null
	): void
	{
		//initialize
		$options = Options\PEvent::coerce($options, false);
		$options->stack_offset++;
		
		//add
		self::addEvent(self::createPEvent($level, $message1, $message2, $number, $number_placeholder, $options));
	}
	
	/**
	 * Log event with a given level and throwable instance.
	 * 
	 * @see \Dracodeum\Kit\Enumerations\Log\Level
	 * @param int|string $level
	 * <p>The level to log with, 
	 * as a name or value from the <code>Dracodeum\Kit\Enumerations\Log\Level</code> enumeration.</p>
	 * @param \Throwable $throwable
	 * <p>The throwable instance to log with.</p>
	 * @param \Dracodeum\Kit\Root\Log\Options\ThrowableEvent|array|null $options [default = null]
	 * <p>Additional options to use, as an instance or a set of <samp>name => value</samp> pairs.</p>
	 * @return void
	 */
	final public static function throwableEvent($level, \Throwable $throwable, $options = null): void
	{
		//initialize
		$options = Options\ThrowableEvent::coerce($options, false);
		$options->stack_offset++;
		
		//add
		self::addEvent(self::createThrowableEvent($level, $throwable, $options));
	}
	
	
	
	//Private static methods
	/**
	 * Get event object from a given object or class.
	 * 
	 * @param object|string|null $object_class
	 * <p>The object or class to get from.</p>
	 * @param int $stack_offset [default = 0]
	 * <p>The stack offset to use.</p>
	 * @return object|null
	 * <p>The event object from the given object or class, or <code>null</code> if none is set.</p>
	 */
	private static function getEventObject($object_class, int $stack_offset = 0): ?object
	{
		if (is_object($object_class)) {
			return $object_class;
		} elseif ($object_class === null) {
			return UCall::stackPreviousObject($stack_offset + 1);
		}
		return null;
	}
	
	/**
	 * Process a given event instance.
	 * 
	 * @param \Dracodeum\Kit\Structures\Log\Event $event
	 * <p>The event instance to process.</p>
	 * @return void
	 */
	private static function processEvent(Event $event): void
	{
		//tags
		$object = $event->object;
		if ($object !== null && $object instanceof IEventTag) {
			$event->tags->prepend($object->getLogEventTag());
		}
		
		//data
		$data = $event->data;
		self::processEventData($data);
		$event->data = $data;
	}
	
	/**
	 * Process a given set of event data.
	 * 
	 * @param mixed $data [reference]
	 * <p>The data to process.</p>
	 * @return void
	 */
	private static function processEventData(&$data): void
	{
		//object
		if (is_object($data) && $data instanceof IEventData) {
			$data = $data->getLogEventData();
		}
		
		//array
		if (is_array($data)) {
			foreach ($data as &$d) {
				self::processEventData($d);
			}
			unset($d);
		}
	}
}
