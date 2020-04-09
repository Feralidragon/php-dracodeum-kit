<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Root;

use Dracodeum\Kit\Interfaces\Uninstantiable as IUninstantiable;
use Dracodeum\Kit\Traits;
use Dracodeum\Kit\Root\Log\Options;
use Dracodeum\Kit\Structures\Log\Event;
use Dracodeum\Kit\Components\Logger;
use Dracodeum\Kit\Enumerations\Log\Level as ELevel;
use Dracodeum\Kit\Utilities\{
	Base64 as UBase64,
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
	 * <p>The event to add, as an instance or <samp>name => value</samp> pairs.</p>
	 * @return void
	 */
	final public static function addEvent($event): void
	{
		$event = Event::coerce($event, empty(self::$event_processors) ? null : true);
		foreach (self::$event_processors as $processor) {
			$processor($event);
		}
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
		return UBase64::encode(random_bytes(self::EVENT_ID_GENERATION_BYTES), true); //TODO: use Base32
	}
	
	/**
	 * Compose an event tag with a given set of strings.
	 * 
	 * @param string[] $strings
	 * <p>The set of strings to compose with.<br>
	 * It cannot be empty.</p>
	 * @return string
	 * <p>The composed event tag with the given set of strings.</p>
	 */
	final public static function composeEventTag(array $strings): string
	{
		//guard
		if (empty($strings)) {
			UCall::haltParameter('strings', $strings, ['error_message' => "An empty set of values is not allowed."]);
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
	 * <p>The tag to decompose.<br>
	 * It cannot be empty.</p>
	 * @return string[]
	 * <p>The given event tag decomposed into a set of strings.</p>
	 */
	final public static function decomposeEventTag(string $tag): array
	{
		//guard
		if ($tag === '') {
			UCall::haltParameter('tag', $tag, ['error_message' => "An empty value is not allowed."]);
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
			'id' => self::generateEventId(),
			'timestamp' => 'now',
			'level' => $level,
			'message' => $message,
			'host' => System::getHostname(true) ?? System::getIpAddress(true),
			'origin' => Runtime::getOrigin(),
			'runtime' => Runtime::getUuid(),
			'class' => $options->object_class ?? UCall::stackPreviousObjectClass($options->stack_offset),
			'function' => $options->function_name ?? UCall::stackPreviousName(false, false, $options->stack_offset),
			'name' => $options->name,
			'tag' => $options->tag,
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
	 * @param string $message2
	 * <p>The message in plural form to create with, 
	 * optionally set with placeholders as <samp>{{placeholder}}</samp>.<br>
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
	 * @param float $number
	 * <p>The number to use to select either the singular (<var>$message1</var>) or plural (<var>$message2</var>) form 
	 * of the message.</p>
	 * @param \Dracodeum\Kit\Root\Log\Options\PEvent|array|null $options [default = null]
	 * <p>Additional options to use, as an instance or <samp>name => value</samp> pairs.</p>
	 * @return \Dracodeum\Kit\Structures\Log\Event
	 * <p>The created event instance with the given level and message in plural form.</p>
	 */
	final public static function createPEvent(
		$level, string $message1, string $message2, float $number, $options = null
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
			$message1, $message2, $number, $options->number_placeholder, $options->parameters, null, [
				'string_options' => $string_options,
				'stringifier' => $options->stringifier
			]
		);
		
		//return
		return Event::build([
			'id' => self::generateEventId(),
			'timestamp' => 'now',
			'level' => $level,
			'message' => $message,
			'host' => System::getHostname(true) ?? System::getIpAddress(true),
			'origin' => Runtime::getOrigin(),
			'runtime' => Runtime::getUuid(),
			'class' => $options->object_class ?? UCall::stackPreviousObjectClass($options->stack_offset),
			'function' => $options->function_name ?? UCall::stackPreviousName(false, false, $options->stack_offset),
			'name' => $options->name,
			'tag' => $options->tag,
			'data' => $options->data,
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
	 * @param string $message2
	 * <p>The message in plural form to log with, optionally set with placeholders as <samp>{{placeholder}}</samp>.<br>
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
	 * @param float $number
	 * <p>The number to use to select either the singular (<var>$message1</var>) or plural (<var>$message2</var>) form 
	 * of the message.</p>
	 * @param \Dracodeum\Kit\Root\Log\Options\PEvent|array|null $options [default = null]
	 * <p>Additional options to use, as an instance or <samp>name => value</samp> pairs.</p>
	 * @return void
	 */
	final public static function pevent(
		$level, string $message1, string $message2, float $number, $options = null
	): void
	{
		//initialize
		$options = Options\PEvent::coerce($options, false);
		$options->stack_offset++;
		
		//add
		self::addEvent(self::createPEvent($level, $message1, $message2, $number, $options));
	}
}
