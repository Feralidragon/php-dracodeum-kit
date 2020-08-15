<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Utilities;

use Dracodeum\Kit\Utility;
use Dracodeum\Kit\Enumerations\Time as ETime;
use Dracodeum\Kit\Options\Text as TextOptions;
use Dracodeum\Kit\Utilities\Time\{
	Options,
	Exceptions
};

/** This utility implements a set of methods used to manipulate and get information about time. */
final class Time extends Utility
{
	//Private constants
	/** Multiples table. */
	private const MULTIPLES_TABLE = [[
		'time' => ETime::T1_YEAR,
		'symbol' => 'Y',
		'singular' => "year",
		'plural' => "years",
		'precision' => 0,
		'limit' => 2,
		'min_multiple' => 'M'
	], [
		'time' => ETime::T1_MONTH,
		'symbol' => 'M',
		'singular' => "month",
		'plural' => "months",
		'precision' => 0,
		'limit' => 2,
		'min_multiple' => 'W'
	], [
		'time' => ETime::T1_WEEK,
		'symbol' => 'W',
		'singular' => "week",
		'plural' => "weeks",
		'precision' => 0,
		'limit' => 2,
		'min_multiple' => 'D'
	], [
		'time' => ETime::T1_DAY,
		'symbol' => 'D',
		'singular' => "day",
		'plural' => "days",
		'precision' => 0,
		'limit' => 2,
		'min_multiple' => 'h'
	], [
		'time' => ETime::T1_HOUR,
		'symbol' => 'h',
		'singular' => "hour",
		'plural' => "hours",
		'precision' => 0,
		'limit' => 2,
		'min_multiple' => 'min'
	], [
		'time' => ETime::T1_MINUTE,
		'symbol' => 'min',
		'singular' => "minute",
		'plural' => "minutes",
		'precision' => 0,
		'limit' => 2,
		'min_multiple' => 's'
	], [
		'time' => 1,
		'symbol' => 's',
		'singular' => "second",
		'plural' => "seconds",
		'precision' => 2,
		'limit' => 1,
		'min_multiple' => 's'
	], [
		'time' => 1e-3,
		'symbol' => 'ms',
		'singular' => "millisecond",
		'plural' => "milliseconds",
		'precision' => 2,
		'limit' => 1,
		'min_multiple' => 'ms'
	], [
		'time' => 1e-6,
		'symbol' => 'µs',
		'singular' => "microsecond",
		'plural' => "microseconds",
		'precision' => 0,
		'limit' => 1,
		'min_multiple' => 'µs'
	], [
		'time' => 1e-9,
		'symbol' => 'ns',
		'singular' => "nanosecond",
		'plural' => "nanoseconds",
		'precision' => 0,
		'limit' => 1,
		'min_multiple' => 'ns'
	]];
	
	
	
	//Private static properties
	/** @var float[] */
	private static $multiples = [];
	
	
	
	//Final public static methods
	/**
	 * Get Unix timestamp from a given timestamp.
	 * 
	 * @see https://en.wikipedia.org/wiki/Unix_time
	 * @see https://php.net/manual/en/function.strtotime.php
	 * @see https://php.net/manual/en/class.datetimeinterface.php
	 * @param int|float|string|\DateTimeInterface $timestamp
	 * <p>The timestamp to get from, as one of the following:<br>
	 * &nbsp; &#8226; &nbsp; a string as supported by the PHP <code>strtotime</code> function;<br> 
	 * &nbsp; &#8226; &nbsp; an integer or float as the number of seconds since 1970-01-01 00:00:00 UTC;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>DateTimeInterface</code> interface.</p>
	 * @param bool $microseconds [default = false]
	 * <p>Return the Unix timestamp with microseconds.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Utilities\Time\Exceptions\InvalidTimestamp
	 * @return int|float|null
	 * <p>The Unix timestamp from the given timestamp.<br>
	 * If <var>$microseconds</var> is set to boolean <code>true</code>, 
	 * then the Unix timestamp is returned as a float, with microseconds.<br>
	 * If <var>$no_throw</var> is set to boolean <code>true</code>, 
	 * then <code>null</code> is returned if it could not be retrieved.</p>
	 */
	final public static function timestamp($timestamp, bool $microseconds = false, bool $no_throw = false)
	{
		//timestamp
		if ($microseconds && Type::evaluateFloat($timestamp)) {
			return $timestamp;
		} elseif (!$microseconds && Type::evaluateNumber($timestamp)) {
			return (int)$timestamp;
		} elseif (Type::evaluateString($timestamp, true)) {
			$t = strtotime($timestamp);
			if ($t !== false) {
				if ($microseconds) {
					$t += (float)(new \DateTime($timestamp))->format('u') / 1e6;
				}
				return $t;
			}
		} elseif (is_object($timestamp) && $timestamp instanceof \DateTimeInterface) {
			$t = $timestamp->getTimestamp();
			if ($microseconds) {
				$t += (float)$timestamp->format('u') / 1e6;
			}
			return $t;
		}
		
		//finalize
		if ($no_throw) {
			return null;
		}
		throw new Exceptions\InvalidTimestamp([$timestamp]);
	}
	
	/**
	 * Format a given timestamp.
	 * 
	 * @see https://php.net/manual/en/function.strtotime.php
	 * @see https://php.net/manual/en/function.date.php
	 * @see https://php.net/manual/en/function.date-default-timezone-set.php
	 * @see https://php.net/manual/en/class.datetime.php
	 * @see https://php.net/manual/en/class.datetimeimmutable.php
	 * @see https://php.net/manual/en/class.datetimeinterface.php
	 * @param int|float|string|\DateTimeInterface $timestamp
	 * <p>The timestamp to format, as one of the following:<br>
	 * &nbsp; &#8226; &nbsp; a string as supported by the PHP <code>strtotime</code> function;<br> 
	 * &nbsp; &#8226; &nbsp; an integer or float as the number of seconds since 1970-01-01 00:00:00 UTC;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>DateTimeInterface</code> interface.</p>
	 * @param string $format
	 * <p>The format to use, as supported by the PHP <code>date</code> function, 
	 * or as a <code>DateTime</code> or <code>DateTimeImmutable</code> class to instantiate.</p>
	 * @param string|null $timezone [default = null]
	 * <p>The timezone to use, as supported by the PHP <code>date_default_timezone_set</code> function.<br>
	 * If not set, then the currently set default timezone is used.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Utilities\Time\Exceptions\InvalidTimestamp
	 * @return string|\DateTime|\DateTimeImmutable|null
	 * <p>The formatted timestamp from the given one.<br>
	 * If <var>$no_throw</var> is set to boolean <code>true</code>, 
	 * then <code>null</code> is returned if it could not be formatted.</p>
	 */
	final public static function format($timestamp, string $format, ?string $timezone = null, bool $no_throw = false)
	{
		$default_timezone = date_default_timezone_get();
		try {
			//timezone
			if (isset($timezone)) {
				Call::guardParameter('timezone', $timezone, date_default_timezone_set($timezone));
			}
			
			//timestamp
			$timestamp = self::timestamp($timestamp, true, $no_throw);
			if (isset($timestamp)) {
				$microseconds = (int)(($timestamp - (int)$timestamp) * 1e6);
				$timestamp = (int)$timestamp;
				if (class_exists($format) && Type::isAny($format, [\DateTime::class, \DateTimeImmutable::class])) {
					return new $format(date('c', $timestamp) . " + {$microseconds} usec");
				} elseif ($microseconds === 0) {
					return date($format, $timestamp);
				} else {
					return (new \DateTime(date('c', $timestamp) . " + {$microseconds} usec"))->format($format);
				}
			}
			
		} finally {
			if (isset($timezone)) {
				date_default_timezone_set($default_timezone);
			}
		}
		return null;
	}
	
	/**
	 * Evaluate a given value as a date and time.
	 * 
	 * Only the following types and formats can be evaluated into a date and time:<br>
	 * &nbsp; &#8226; &nbsp; an integer or float as the number of seconds since 1970-01-01 00:00:00 UTC, 
	 * such as: <code>1483268400</code> for <samp>2017-01-01 12:00:00</samp>;<br>
	 * &nbsp; &#8226; &nbsp; a string as supported by the PHP <code>strtotime</code> function, 
	 * such as: <samp>2017-Jan-01 12:00:00</samp> for <samp>2017-01-01 12:00:00</samp>;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>DateTimeInterface</code> interface.
	 * 
	 * @see https://en.wikipedia.org/wiki/Unix_time
	 * @see https://en.wikipedia.org/wiki/Timestamp
	 * @see https://php.net/manual/en/function.strtotime.php
	 * @see https://php.net/manual/en/function.date.php
	 * @see https://php.net/manual/en/function.date-default-timezone-set.php
	 * @see https://php.net/manual/en/class.datetime.php
	 * @see https://php.net/manual/en/class.datetimeimmutable.php
	 * @see https://php.net/manual/en/class.datetimeinterface.php
	 * @param mixed $value [reference]
	 * <p>The value to evaluate (validate and sanitize).</p>
	 * @param string|null $format [default = null]
	 * <p>The format to evaluate into, as supported by the PHP <code>date</code> function, 
	 * or as a <code>DateTime</code> or <code>DateTimeImmutable</code> class to instantiate.<br>
	 * If not set, then the given value is evaluated into an integer or float as a Unix timestamp.</p>
	 * @param bool $microseconds [default = false]
	 * <p>Evaluate the given value with microseconds.</p>
	 * @param string|null $timezone [default = null]
	 * <p>The timezone to evaluate into, as supported by the PHP <code>date_default_timezone_set</code> function.<br>
	 * If not set, then the currently set default timezone is used.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to evaluate as <code>null</code>.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value was successfully evaluated into a date and time.</p>
	 */
	final public static function evaluateDateTime(
		&$value, ?string $format = null, bool $microseconds = false, ?string $timezone = null, bool $nullable = false
	): bool
	{
		return self::processDateTimeCoercion($value, $format, $microseconds, $timezone, $nullable, true);
	}
	
	/**
	 * Coerce a given value into a date and time.
	 * 
	 * Only the following types and formats can be coerced into a date and time:<br>
	 * &nbsp; &#8226; &nbsp; an integer or float as the number of seconds since 1970-01-01 00:00:00 UTC, 
	 * such as: <code>1483268400</code> for <samp>2017-01-01 12:00:00</samp>;<br>
	 * &nbsp; &#8226; &nbsp; a string as supported by the PHP <code>strtotime</code> function, 
	 * such as: <samp>2017-Jan-01 12:00:00</samp> for <samp>2017-01-01 12:00:00</samp>;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>DateTimeInterface</code> interface.
	 * 
	 * @see https://en.wikipedia.org/wiki/Unix_time
	 * @see https://en.wikipedia.org/wiki/Timestamp
	 * @see https://php.net/manual/en/function.strtotime.php
	 * @see https://php.net/manual/en/function.date.php
	 * @see https://php.net/manual/en/function.date-default-timezone-set.php
	 * @see https://php.net/manual/en/class.datetime.php
	 * @see https://php.net/manual/en/class.datetimeimmutable.php
	 * @see https://php.net/manual/en/class.datetimeinterface.php
	 * @param mixed $value
	 * <p>The value to coerce (validate and sanitize).</p>
	 * @param string|null $format [default = null]
	 * <p>The format to coerce into, as supported by the PHP <code>date</code> function, 
	 * or as a <code>DateTime</code> or <code>DateTimeImmutable</code> class to instantiate.<br>
	 * If not set, then the given value is coerced into an integer or float as a Unix timestamp.</p>
	 * @param bool $microseconds [default = false]
	 * <p>Coerce the given value with microseconds.</p>
	 * @param string|null $timezone [default = null]
	 * <p>The timezone to coerce into, as supported by the PHP <code>date_default_timezone_set</code> function.<br>
	 * If not set, then the currently set default timezone is used.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @throws \Dracodeum\Kit\Utilities\Time\Exceptions\DateTimeCoercionFailed
	 * @return int|float|string|\DateTime|\DateTimeImmutable|null
	 * <p>The given value coerced into a date and time.<br>
	 * If nullable, then <code>null</code> may also be returned.</p>
	 */
	final public static function coerceDateTime(
		$value, ?string $format = null, bool $microseconds = false, ?string $timezone = null, bool $nullable = false
	) {
		self::processDateTimeCoercion($value, $format, $microseconds, $timezone, $nullable);
		return $value;
	}
	
	/**
	 * Process the coercion of a given value into a date and time.
	 * 
	 * Only the following types and formats can be coerced into a date and time:<br>
	 * &nbsp; &#8226; &nbsp; an integer or float as the number of seconds since 1970-01-01 00:00:00 UTC, 
	 * such as: <code>1483268400</code> for <samp>2017-01-01 12:00:00</samp>;<br>
	 * &nbsp; &#8226; &nbsp; a string as supported by the PHP <code>strtotime</code> function, 
	 * such as: <samp>2017-Jan-01 12:00:00</samp> for <samp>2017-01-01 12:00:00</samp>;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>DateTimeInterface</code> interface.
	 * 
	 * @see https://en.wikipedia.org/wiki/Unix_time
	 * @see https://en.wikipedia.org/wiki/Timestamp
	 * @see https://php.net/manual/en/function.strtotime.php
	 * @see https://php.net/manual/en/function.date.php
	 * @see https://php.net/manual/en/function.date-default-timezone-set.php
	 * @see https://php.net/manual/en/class.datetime.php
	 * @see https://php.net/manual/en/class.datetimeimmutable.php
	 * @see https://php.net/manual/en/class.datetimeinterface.php
	 * @param mixed $value [reference]
	 * <p>The value to process (validate and sanitize).</p>
	 * @param string|null $format [default = null]
	 * <p>The format to coerce into, as supported by the PHP <code>date</code> function, 
	 * or as a <code>DateTime</code> or <code>DateTimeImmutable</code> class to instantiate.<br>
	 * If not set, then the given value is coerced into an integer or float as a Unix timestamp.</p>
	 * @param bool $microseconds [default = false]
	 * <p>Coerce the given value with microseconds.</p>
	 * @param string|null $timezone [default = null]
	 * <p>The timezone to coerce into, as supported by the PHP <code>date_default_timezone_set</code> function.<br>
	 * If not set, then the currently set default timezone is used.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Utilities\Time\Exceptions\DateTimeCoercionFailed
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value was successfully coerced into a date and time.</p>
	 */
	final public static function processDateTimeCoercion(
		&$value, ?string $format = null, bool $microseconds = false, ?string $timezone = null, bool $nullable = false,
		bool $no_throw = false
	): bool
	{
		//nullable
		if (!isset($value)) {
			if ($nullable) {
				return true;
			} elseif ($no_throw) {
				return false;
			}
			throw new Exceptions\DateTimeCoercionFailed([
				'value' => $value,
				'error_code' => Exceptions\DateTimeCoercionFailed::ERROR_CODE_NULL,
				'error_message' => "A null value is not allowed."
			]);
		}
		
		//timestamp
		$timestamp = self::timestamp($value, $microseconds, true);
		if (isset($timestamp)) {
			$value = isset($format) ? self::format($timestamp, $format, $timezone) : $timestamp;
			return true;
		}
		
		//finalize
		if ($no_throw) {
			return false;
		}
		throw new Exceptions\DateTimeCoercionFailed([
			'value' => $value,
			'error_code' => Exceptions\DateTimeCoercionFailed::ERROR_CODE_INVALID,
			'error_message' => "Only the following types and formats can be coerced into a date and time:\n" . 
				" - an integer or float as the number of seconds since 1970-01-01 00:00:00 UTC, " . 
				"such as: 1483268400 for \"2017-01-01 12:00:00\";\n" . 
				" - a string as supported by the PHP \"strtotime\" function, " . 
				"such as: \"2017-Jan-01 12:00:00\" for \"2017-01-01 12:00:00\";\n" . 
				" - an object implementing the \"DateTimeInterface\" interface."
		]);
	}
	
	/**
	 * Generate a string from a given date and time.
	 * 
	 * The returning string represents the given date and time in order to be shown or printed out in messages.
	 * 
	 * @see https://php.net/manual/en/function.strtotime.php
	 * @see https://php.net/manual/en/class.datetimeinterface.php
	 * @param int|float|string|\DateTimeInterface $datetime
	 * <p>The date and time to generate from, as one of the following:<br>
	 * &nbsp; &#8226; &nbsp; a string as supported by the PHP <code>strtotime</code> function;<br> 
	 * &nbsp; &#8226; &nbsp; an integer or float as the number of seconds since 1970-01-01 00:00:00 UTC;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>DateTimeInterface</code> interface.</p>
	 * @param \Dracodeum\Kit\Options\Text|array|null $text_options [default = null]
	 * <p>The text options to use, as an instance or <samp>name => value</samp> pairs.</p>
	 * @return string
	 * <p>The generated string from the given date and time.</p>
	 */
	final public static function stringifyDateTime($datetime, $text_options = null): string
	{
		$text_options = TextOptions::coerce($text_options);
		$format = 'Y-m-d H:i:s e';
		
		//TODO: use Localization to get the correct date and time format
		
		return self::coerceDateTime($datetime, $format);
	}
	
	/**
	 * Evaluate a given value as a date.
	 * 
	 * Only the following types and formats can be evaluated into a date:<br>
	 * &nbsp; &#8226; &nbsp; an integer or float as the number of seconds since 1970-01-01, 
	 * such as: <code>1483228800</code> for <samp>2017-01-01</samp>;<br>
	 * &nbsp; &#8226; &nbsp; a string as supported by the PHP <code>strtotime</code> function, 
	 * such as: <samp>2017-Jan-01</samp> for <samp>2017-01-01</samp>;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>DateTimeInterface</code> interface.
	 * 
	 * @see https://en.wikipedia.org/wiki/Unix_time
	 * @see https://en.wikipedia.org/wiki/Calendar_date
	 * @see https://php.net/manual/en/function.strtotime.php
	 * @see https://php.net/manual/en/function.date.php
	 * @see https://php.net/manual/en/class.datetime.php
	 * @see https://php.net/manual/en/class.datetimeimmutable.php
	 * @see https://php.net/manual/en/class.datetimeinterface.php
	 * @param mixed $value [reference]
	 * <p>The value to evaluate (validate and sanitize).</p>
	 * @param string|null $format [default = null]
	 * <p>The format to evaluate into, as supported by the PHP <code>date</code> function, 
	 * or as a <code>DateTime</code> or <code>DateTimeImmutable</code> class to instantiate.<br>
	 * If not set, then the given value is evaluated into an integer as a Unix timestamp.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to evaluate as <code>null</code>.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value was successfully evaluated into a date.</p>
	 */
	final public static function evaluateDate(&$value, ?string $format = null, bool $nullable = false): bool
	{
		return self::processDateCoercion($value, $format, $nullable, true);
	}
	
	/**
	 * Coerce a given value into a date.
	 * 
	 * Only the following types and formats can be coerced into a date:<br>
	 * &nbsp; &#8226; &nbsp; an integer or float as the number of seconds since 1970-01-01, 
	 * such as: <code>1483228800</code> for <samp>2017-01-01</samp>;<br>
	 * &nbsp; &#8226; &nbsp; a string as supported by the PHP <code>strtotime</code> function, 
	 * such as: <samp>2017-Jan-01</samp> for <samp>2017-01-01</samp>;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>DateTimeInterface</code> interface.
	 * 
	 * @see https://en.wikipedia.org/wiki/Unix_time
	 * @see https://en.wikipedia.org/wiki/Calendar_date
	 * @see https://php.net/manual/en/function.strtotime.php
	 * @see https://php.net/manual/en/function.date.php
	 * @see https://php.net/manual/en/class.datetime.php
	 * @see https://php.net/manual/en/class.datetimeimmutable.php
	 * @see https://php.net/manual/en/class.datetimeinterface.php
	 * @param mixed $value
	 * <p>The value to coerce (validate and sanitize).</p>
	 * @param string|null $format [default = null]
	 * <p>The format to coerce into, as supported by the PHP <code>date</code> function, 
	 * or as a <code>DateTime</code> or <code>DateTimeImmutable</code> class to instantiate.<br>
	 * If not set, then the given value is coerced into an integer as a Unix timestamp.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @throws \Dracodeum\Kit\Utilities\Time\Exceptions\DateCoercionFailed
	 * @return int|string|\DateTime|\DateTimeImmutable|null
	 * <p>The given value coerced into a date.<br>
	 * If nullable, then <code>null</code> may also be returned.</p>
	 */
	final public static function coerceDate($value, ?string $format = null, bool $nullable = false)
	{
		self::processDateCoercion($value, $format, $nullable);
		return $value;
	}
	
	/**
	 * Process the coercion of a given value into a date.
	 * 
	 * Only the following types and formats can be coerced into a date:<br>
	 * &nbsp; &#8226; &nbsp; an integer or float as the number of seconds since 1970-01-01, 
	 * such as: <code>1483228800</code> for <samp>2017-01-01</samp>;<br>
	 * &nbsp; &#8226; &nbsp; a string as supported by the PHP <code>strtotime</code> function, 
	 * such as: <samp>2017-Jan-01</samp> for <samp>2017-01-01</samp>;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>DateTimeInterface</code> interface.
	 * 
	 * @see https://en.wikipedia.org/wiki/Unix_time
	 * @see https://en.wikipedia.org/wiki/Calendar_date
	 * @see https://php.net/manual/en/function.strtotime.php
	 * @see https://php.net/manual/en/function.date.php
	 * @see https://php.net/manual/en/class.datetime.php
	 * @see https://php.net/manual/en/class.datetimeimmutable.php
	 * @see https://php.net/manual/en/class.datetimeinterface.php
	 * @param mixed $value [reference]
	 * <p>The value to process (validate and sanitize).</p>
	 * @param string|null $format [default = null]
	 * <p>The format to coerce into, as supported by the PHP <code>date</code> function, 
	 * or as a <code>DateTime</code> or <code>DateTimeImmutable</code> class to instantiate.<br>
	 * If not set, then the given value is coerced into an integer as a Unix timestamp.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Utilities\Time\Exceptions\DateCoercionFailed
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value was successfully coerced into a date.</p>
	 */
	final public static function processDateCoercion(
		&$value, ?string $format = null, bool $nullable = false, bool $no_throw = false
	): bool
	{
		//nullable
		if (!isset($value)) {
			if ($nullable) {
				return true;
			} elseif ($no_throw) {
				return false;
			}
			throw new Exceptions\DateCoercionFailed([
				'value' => $value,
				'error_code' => Exceptions\DateCoercionFailed::ERROR_CODE_NULL,
				'error_message' => "A null value is not allowed."
			]);
		}
		
		//timestamp
		$timestamp = self::timestamp($value, false, true);
		if (isset($timestamp)) {
			$timestamp = (int)(floor($timestamp / ETime::T1_DAY) * ETime::T1_DAY);
			$value = isset($format) ? self::format($timestamp, $format, 'UTC') : $timestamp;
			return true;
		}
		
		//finalize
		if ($no_throw) {
			return false;
		}
		throw new Exceptions\DateCoercionFailed([
			'value' => $value,
			'error_code' => Exceptions\DateCoercionFailed::ERROR_CODE_INVALID,
			'error_message' => "Only the following types and formats can be coerced into a date:\n" . 
				" - an integer or float as the number of seconds since 1970-01-01, " . 
				"such as: 1483228800 for \"2017-01-01\";\n" . 
				" - a string as supported by the PHP \"strtotime\" function, " . 
				"such as: \"2017-Jan-01\" for \"2017-01-01\";\n" . 
				" - an object implementing the \"DateTimeInterface\" interface."
		]);
	}
	
	/**
	 * Generate a string from a given date.
	 * 
	 * The returning string represents the given date in order to be shown or printed out in messages.
	 * 
	 * @see https://php.net/manual/en/function.strtotime.php
	 * @see https://php.net/manual/en/class.datetimeinterface.php
	 * @param int|float|string|\DateTimeInterface $date
	 * <p>The date to generate from, as one of the following:<br>
	 * &nbsp; &#8226; &nbsp; a string as supported by the PHP <code>strtotime</code> function;<br> 
	 * &nbsp; &#8226; &nbsp; an integer or float as the number of seconds since 1970-01-01;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>DateTimeInterface</code> interface.</p>
	 * @param \Dracodeum\Kit\Options\Text|array|null $text_options [default = null]
	 * <p>The text options to use, as an instance or <samp>name => value</samp> pairs.</p>
	 * @return string
	 * <p>The generated string from the given date.</p>
	 */
	final public static function stringifyDate($date, $text_options = null): string
	{
		$text_options = TextOptions::coerce($text_options);
		$format = 'Y-m-d';
		
		//TODO: use Localization to get the correct date format
		
		return self::coerceDate($date, $format);
	}
	
	/**
	 * Evaluate a given value as a time.
	 * 
	 * Only the following types and formats can be evaluated into a time:<br>
	 * &nbsp; &#8226; &nbsp; an integer or float as the number of seconds, 
	 * such as: <code>50700</code> for <samp>14:05:00</samp>;<br>
	 * &nbsp; &#8226; &nbsp; a string as supported by the PHP <code>strtotime</code> function, 
	 * such as: <samp>2:05PM</samp> for <samp>14:05:00</samp>;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>DateTimeInterface</code> interface.
	 * 
	 * @see https://en.wikipedia.org/wiki/Unix_time
	 * @see https://en.wikipedia.org/wiki/Timestamp
	 * @see https://php.net/manual/en/function.strtotime.php
	 * @see https://php.net/manual/en/function.date.php
	 * @see https://php.net/manual/en/function.date-default-timezone-set.php
	 * @see https://php.net/manual/en/class.datetime.php
	 * @see https://php.net/manual/en/class.datetimeimmutable.php
	 * @see https://php.net/manual/en/class.datetimeinterface.php
	 * @param mixed $value [reference]
	 * <p>The value to evaluate (validate and sanitize).</p>
	 * @param string|null $format [default = null]
	 * <p>The format to evaluate into, as supported by the PHP <code>date</code> function, 
	 * or as a <code>DateTime</code> or <code>DateTimeImmutable</code> class to instantiate.<br>
	 * If not set, then the given value is evaluated into an integer or float as a Unix timestamp.</p>
	 * @param bool $microseconds [default = false]
	 * <p>Evaluate the given value with microseconds.</p>
	 * @param string|null $timezone [default = null]
	 * <p>The timezone to evaluate into, as supported by the PHP <code>date_default_timezone_set</code> function.<br>
	 * If not set, then the currently set default timezone is used.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to evaluate as <code>null</code>.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value was successfully evaluated into a time.</p>
	 */
	final public static function evaluateTime(
		&$value, ?string $format = null, bool $microseconds = false, ?string $timezone = null, bool $nullable = false
	): bool
	{
		return self::processTimeCoercion($value, $format, $microseconds, $timezone, $nullable, true);
	}
	
	/**
	 * Coerce a given value into a time.
	 * 
	 * Only the following types and formats can be coerced into a time:<br>
	 * &nbsp; &#8226; &nbsp; an integer or float as the number of seconds, 
	 * such as: <code>50700</code> for <samp>14:05:00</samp>;<br>
	 * &nbsp; &#8226; &nbsp; a string as supported by the PHP <code>strtotime</code> function, 
	 * such as: <samp>2:05PM</samp> for <samp>14:05:00</samp>;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>DateTimeInterface</code> interface.
	 * 
	 * @see https://en.wikipedia.org/wiki/Unix_time
	 * @see https://en.wikipedia.org/wiki/Timestamp
	 * @see https://php.net/manual/en/function.strtotime.php
	 * @see https://php.net/manual/en/function.date.php
	 * @see https://php.net/manual/en/function.date-default-timezone-set.php
	 * @see https://php.net/manual/en/class.datetime.php
	 * @see https://php.net/manual/en/class.datetimeimmutable.php
	 * @see https://php.net/manual/en/class.datetimeinterface.php
	 * @param mixed $value
	 * <p>The value to coerce (validate and sanitize).</p>
	 * @param string|null $format [default = null]
	 * <p>The format to coerce into, as supported by the PHP <code>date</code> function, 
	 * or as a <code>DateTime</code> or <code>DateTimeImmutable</code> class to instantiate.<br>
	 * If not set, then the given value is coerced into an integer or float as a Unix timestamp.</p>
	 * @param bool $microseconds [default = false]
	 * <p>Coerce the given value with microseconds.</p>
	 * @param string|null $timezone [default = null]
	 * <p>The timezone to coerce into, as supported by the PHP <code>date_default_timezone_set</code> function.<br>
	 * If not set, then the currently set default timezone is used.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @throws \Dracodeum\Kit\Utilities\Time\Exceptions\TimeCoercionFailed
	 * @return int|float|string|\DateTime|\DateTimeImmutable|null
	 * <p>The given value coerced into a time.<br>
	 * If nullable, then <code>null</code> may also be returned.</p>
	 */
	final public static function coerceTime(
		$value, ?string $format = null, bool $microseconds = false, ?string $timezone = null, bool $nullable = false
	) {
		self::processTimeCoercion($value, $format, $microseconds, $timezone, $nullable);
		return $value;
	}
	
	/**
	 * Process the coercion of a given value into a time.
	 * 
	 * Only the following types and formats can be coerced into a time:<br>
	 * &nbsp; &#8226; &nbsp; an integer or float as the number of seconds, 
	 * such as: <code>50700</code> for <samp>14:05:00</samp>;<br>
	 * &nbsp; &#8226; &nbsp; a string as supported by the PHP <code>strtotime</code> function, 
	 * such as: <samp>2:05PM</samp> for <samp>14:05:00</samp>;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>DateTimeInterface</code> interface.
	 * 
	 * @see https://en.wikipedia.org/wiki/Unix_time
	 * @see https://en.wikipedia.org/wiki/Timestamp
	 * @see https://php.net/manual/en/function.strtotime.php
	 * @see https://php.net/manual/en/function.date.php
	 * @see https://php.net/manual/en/function.date-default-timezone-set.php
	 * @see https://php.net/manual/en/class.datetime.php
	 * @see https://php.net/manual/en/class.datetimeimmutable.php
	 * @see https://php.net/manual/en/class.datetimeinterface.php
	 * @param mixed $value [reference]
	 * <p>The value to process (validate and sanitize).</p>
	 * @param string|null $format [default = null]
	 * <p>The format to coerce into, as supported by the PHP <code>date</code> function, 
	 * or as a <code>DateTime</code> or <code>DateTimeImmutable</code> class to instantiate.<br>
	 * If not set, then the given value is coerced into an integer or float as a Unix timestamp.</p>
	 * @param bool $microseconds [default = false]
	 * <p>Coerce the given value with microseconds.</p>
	 * @param string|null $timezone [default = null]
	 * <p>The timezone to coerce into, as supported by the PHP <code>date_default_timezone_set</code> function.<br>
	 * If not set, then the currently set default timezone is used.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Utilities\Time\Exceptions\TimeCoercionFailed
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value was successfully coerced into a time.</p>
	 */
	final public static function processTimeCoercion(
		&$value, ?string $format = null, bool $microseconds = false, ?string $timezone = null, bool $nullable = false,
		bool $no_throw = false
	): bool
	{
		//nullable
		if (!isset($value)) {
			if ($nullable) {
				return true;
			} elseif ($no_throw) {
				return false;
			}
			throw new Exceptions\TimeCoercionFailed([
				'value' => $value,
				'error_code' => Exceptions\TimeCoercionFailed::ERROR_CODE_NULL,
				'error_message' => "A null value is not allowed."
			]);
		}
		
		//timestamp
		$timestamp = self::timestamp($value, $microseconds, true);
		if (isset($timestamp)) {
			$timestamp = $timestamp - (int)(floor($timestamp / ETime::T1_DAY) * ETime::T1_DAY);
			$value = isset($format) ? self::format($timestamp, $format, $timezone) : $timestamp;
			return true;
		}
		
		//finalize
		if ($no_throw) {
			return false;
		}
		throw new Exceptions\TimeCoercionFailed([
			'value' => $value,
			'error_code' => Exceptions\TimeCoercionFailed::ERROR_CODE_INVALID,
			'error_message' => "Only the following types and formats can be coerced into a time:\n" . 
				" - an integer or float as the number of seconds, such as: 50700 for \"14:05:00\";\n" . 
				" - a string as supported by the PHP \"strtotime\" function, " . 
				"such as: \"2:05PM\" for \"14:05:00\";\n" . 
				" - an object implementing the \"DateTimeInterface\" interface."
		]);
	}
	
	/**
	 * Generate a string from a given time.
	 * 
	 * The returning string represents the given time in order to be shown or printed out in messages.
	 * 
	 * @see https://php.net/manual/en/function.strtotime.php
	 * @see https://php.net/manual/en/class.datetimeinterface.php
	 * @param int|float|string|\DateTimeInterface $time
	 * <p>The time to generate from, as one of the following:<br>
	 * &nbsp; &#8226; &nbsp; a string as supported by the PHP <code>strtotime</code> function;<br> 
	 * &nbsp; &#8226; &nbsp; an integer or float as the number of seconds;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>DateTimeInterface</code> interface.</p>
	 * @param \Dracodeum\Kit\Options\Text|array|null $text_options [default = null]
	 * <p>The text options to use, as an instance or <samp>name => value</samp> pairs.</p>
	 * @return string
	 * <p>The generated string from the given time.</p>
	 */
	final public static function stringifyTime($time, $text_options = null): string
	{
		$text_options = TextOptions::coerce($text_options);
		$format = 'H:i:s e';
		
		//TODO: use Localization to get the correct time format
		
		return self::coerceTime($time, $format);
	}
	
	/**
	 * Calculate how long ago it has been, in a human-readable format, since a given timestamp.
	 * 
	 * The returning string represents how long ago it has been since a given timestamp, 
	 * in a human-readable format, as shown in the examples below, 
	 * for a given timestamp set as <samp>2017-01-01 12:00:00</samp>:<br>
	 * &nbsp; &#8226; &nbsp; <samp>2017-01-01 12:00:00</samp> returns <samp>just now</samp>.<br>
	 * &nbsp; &#8226; &nbsp; <samp>2017-01-01 11:06:23</samp> returns <samp>5 minutes ago</samp>.<br>
	 * &nbsp; &#8226; &nbsp; <samp>2017-01-01 09:45:00</samp> returns <samp>2 hours ago</samp>.<br>
	 * &nbsp; &#8226; &nbsp; <samp>2016-10-01 12:00:00</samp> returns <samp>3 months ago</samp>.<br>
	 * &nbsp; &#8226; &nbsp; <samp>2016-01-01 12:00:00</samp> returns <samp>1 year ago</samp>.
	 * 
	 * @see https://php.net/manual/en/function.strtotime.php
	 * @see https://php.net/manual/en/class.datetimeinterface.php
	 * @param int|float|string|\DateTimeInterface $timestamp
	 * <p>The timestamp to calculate from, as one of the following:<br>
	 * &nbsp; &#8226; &nbsp; a string as supported by the PHP <code>strtotime</code> function;<br> 
	 * &nbsp; &#8226; &nbsp; an integer or float as the number of seconds since 1970-01-01 00:00:00 UTC;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>DateTimeInterface</code> interface.</p>
	 * @param \Dracodeum\Kit\Options\Text|array|null $text_options [default = null]
	 * <p>The text options to use, as an instance or <samp>name => value</samp> pairs.</p>
	 * @return string
	 * <p>The calculated period on how long ago it has been, in a human-readable format, since the given timestamp.</p>
	 */
	final public static function ago($timestamp, $text_options = null): string
	{
		//initialize
		$timestamp = self::timestamp($timestamp);
		$text_options = TextOptions::coerce($text_options);
		$period = max(0, time() - $timestamp);
		
		//years
		if ($period >= ETime::T1_YEAR) {
			/**
			 * @description Human-readable time, on how long ago it has been, scaled in years.
			 * @placeholder number The number of years.
			 * @example 3 years ago
			 */
			return Text::plocalize(
				"{{number}} year ago", "{{number}} years ago",
				(int)($period / ETime::T1_YEAR), 'number', self::class, $text_options
			);
		}
		
		//months
		if ($period >= ETime::T1_MONTH) {
			/**
			 * @description Human-readable time, on how long ago it has been, scaled in months.
			 * @placeholder number The number of months.
			 * @example 3 months ago
			 */
			return Text::plocalize(
				"{{number}} month ago", "{{number}} months ago",
				(int)($period / ETime::T1_MONTH), 'number', self::class, $text_options
			);
		}
		
		//weeks
		if ($period >= ETime::T1_WEEK) {
			/**
			 * @description Human-readable time, on how long ago it has been, scaled in weeks.
			 * @placeholder number The number of weeks.
			 * @example 3 weeks ago
			 */
			return Text::plocalize(
				"{{number}} week ago", "{{number}} weeks ago",
				(int)($period / ETime::T1_WEEK), 'number', self::class, $text_options
			);
		}
		
		//days
		if ($period >= ETime::T1_DAY) {
			/**
			 * @description Human-readable time, on how long ago it has been, scaled in days.
			 * @placeholder number The number of days.
			 * @example 3 days ago
			 */
			return Text::plocalize(
				"{{number}} day ago", "{{number}} days ago",
				(int)($period / ETime::T1_DAY), 'number', self::class, $text_options
			);
		}
		
		//hours
		if ($period >= ETime::T1_HOUR) {
			/**
			 * @description Human-readable time, on how long ago it has been, scaled in hours.
			 * @placeholder number The number of hours.
			 * @example 3 hours ago
			 */
			return Text::plocalize(
				"{{number}} hour ago", "{{number}} hours ago",
				(int)($period / ETime::T1_HOUR), 'number', self::class, $text_options
			);
		}
		
		//minutes
		if ($period >= ETime::T1_MINUTE) {
			/**
			 * @description Human-readable time, on how long ago it has been, scaled in minutes.
			 * @placeholder number The number of minutes.
			 * @example 3 minutes ago
			 */
			return Text::plocalize(
				"{{number}} minute ago", "{{number}} minutes ago",
				(int)($period / ETime::T1_MINUTE), 'number', self::class, $text_options
			);
		}
		
		//seconds
		if ($period >= 1) {
			/**
			 * @description Human-readable time, on how long ago it has been, scaled in seconds.
			 * @placeholder number The number of seconds.
			 * @example 3 seconds ago
			 */
			return Text::plocalize(
				"{{number}} second ago", "{{number}} seconds ago",
				$period, 'number', self::class, $text_options
			);
		}
		
		//just now
		/** @description Human-readable time, on how long ago it has been, just now. */
		return Text::localize("just now", self::class, $text_options);
	}
	
	/**
	 * Get human-readable period from a given machine one.
	 * 
	 * The returning period represents the given one in a human-readable format, 
	 * by rounding it to the nearest most significant time multiples, as shown in the examples below:<br>
	 * &nbsp; &#8226; &nbsp; <code>12</code> returns <samp>12 seconds</samp>, 
	 * or <samp>12s</samp> in short form.<br>
	 * &nbsp; &#8226; &nbsp; <code>300.55</code> returns <samp>5 minutes and 55 milliseconds</samp>, 
	 * or <samp>5min 55ms</samp> in short form.<br>
	 * &nbsp; &#8226; &nbsp; <code>7268</code> returns <samp>2 hours, 1 minute and 8 seconds</samp>, 
	 * or <samp>2h 1min 8s</samp> in short form.<br>
	 * &nbsp; &#8226; &nbsp; <code>295500</code> returns <samp>3 days, 10 hours and 5 minutes</samp>, 
	 * or <samp>3D 10h 5min</samp> in short form.<br>
	 * <br>
	 * Whenever the short form is enabled with <var>$options->short = true</var>, the following symbols are used:<br>
	 * &nbsp; &#8226; &nbsp; <samp>Y</samp> : years<br>
	 * &nbsp; &#8226; &nbsp; <samp>M</samp> : months<br>
	 * &nbsp; &#8226; &nbsp; <samp>W</samp> : weeks<br>
	 * &nbsp; &#8226; &nbsp; <samp>D</samp> : days<br>
	 * &nbsp; &#8226; &nbsp; <samp>h</samp> : hours<br>
	 * &nbsp; &#8226; &nbsp; <samp>min</samp> : minutes<br>
	 * &nbsp; &#8226; &nbsp; <samp>s</samp> : seconds<br>
	 * &nbsp; &#8226; &nbsp; <samp>ms</samp> : milliseconds<br>
	 * &nbsp; &#8226; &nbsp; <samp>µs</samp> : microseconds<br>
	 * &nbsp; &#8226; &nbsp; <samp>ns</samp> : nanoseconds
	 * 
	 * @param float $period
	 * <p>The machine-readable period to get from, in seconds.</p>
	 * @param \Dracodeum\Kit\Options\Text|array|null $text_options [default = null]
	 * <p>The text options to use, as an instance or <samp>name => value</samp> pairs.</p>
	 * @param \Dracodeum\Kit\Utilities\Time\Options\Hperiod|array|null $options [default = null]
	 * <p>Additional options to use, as an instance or <samp>name => value</samp> pairs.</p>
	 * @return string
	 * <p>The human-readable period from the given machine one.</p>
	 */
	final public static function hperiod(float $period, $text_options = null, $options = null): string
	{
		//initialize
		$text_options = TextOptions::coerce($text_options);
		$options = Options\Hperiod::coerce($options);
		$precision = $options->precision;
		$limit = $options->limit;
		$min_multiple = $options->min_multiple;
		$sign = $period >= 0 ? '' : '-';
		$period = abs($period);
		
		//parts
		$parts = [];
		foreach (self::MULTIPLES_TABLE as $row) {
			//prepare
			if (isset($options->max_multiple) && $row['time'] > $options->max_multiple) {
				continue;
			} elseif ($period >= $row['time']) {
				if (!isset($precision)) {
					$precision = $row['precision'];
				}
				if (!isset($limit)) {
					$limit = $row['limit'];
				}
				if (!isset($min_multiple)) {
					$min_multiple = self::coerceMultiple($row['min_multiple']);
				}
			}
			
			//last
			$is_last = (isset($limit) && (count($parts) + 1) >= $limit && $period >= $row['time']) || 
				(isset($min_multiple) && $row['time'] <= $min_multiple);
			if (!$is_last && $period < $row['time']) {
				continue;
			}
			
			//calculate
			$number = $is_last ? round($period / $row['time'], $precision) : floor($period / $row['time']);
			if (!empty($number)) {
				if ($options->short) {
					$part = "{$number}{$row['symbol']}";
				} else {
					$part = $number === 1.0 ? "{$number} {$row['singular']}" : "{$number} {$row['plural']}";
					switch ($row['symbol']) {
						case 'ns':
							/**
							 * @description Human-readable time scaled in nanoseconds.
							 * @placeholder number The number of nanoseconds.
							 * @example 3 nanoseconds
							 */
							$part = Text::plocalize(
								"{{number}} nanosecond", "{{number}} nanoseconds",
								$number, 'number', self::class, $text_options
							);
							break;
						case 'µs':
							/**
							 * @description Human-readable time scaled in microseconds.
							 * @placeholder number The number of microseconds.
							 * @example 3 microseconds
							 */
							$part = Text::plocalize(
								"{{number}} microsecond", "{{number}} microseconds",
								$number, 'number', self::class, $text_options
							);
							break;
						case 'ms':
							/**
							 * @description Human-readable time scaled in milliseconds.
							 * @placeholder number The number of milliseconds.
							 * @example 3 milliseconds
							 */
							$part = Text::plocalize(
								"{{number}} millisecond", "{{number}} milliseconds",
								$number, 'number', self::class, $text_options
							);
							break;
						case 's':
							/**
							 * @description Human-readable time scaled in seconds.
							 * @placeholder number The number of seconds.
							 * @example 3 seconds
							 */
							$part = Text::plocalize(
								"{{number}} second", "{{number}} seconds",
								$number, 'number', self::class, $text_options
							);
							break;
						case 'min':
							/**
							 * @description Human-readable time scaled in minutes.
							 * @placeholder number The number of minutes.
							 * @example 3 minutes
							 */
							$part = Text::plocalize(
								"{{number}} minute", "{{number}} minutes",
								$number, 'number', self::class, $text_options
							);
							break;
						case 'h':
							/**
							 * @description Human-readable time scaled in hours.
							 * @placeholder number The number of hours.
							 * @example 3 hours
							 */
							$part = Text::plocalize(
								"{{number}} hour", "{{number}} hours",
								$number, 'number', self::class, $text_options
							);
							break;
						case 'D':
							/**
							 * @description Human-readable time scaled in days.
							 * @placeholder number The number of days.
							 * @example 3 days
							 */
							$part = Text::plocalize(
								"{{number}} day", "{{number}} days",
								$number, 'number', self::class, $text_options
							);
							break;
						case 'W':
							/**
							 * @description Human-readable time scaled in weeks.
							 * @placeholder number The number of weeks.
							 * @example 3 weeks
							 */
							$part = Text::plocalize(
								"{{number}} week", "{{number}} weeks",
								$number, 'number', self::class, $text_options
							);
							break;
						case 'M':
							/**
							 * @description Human-readable time scaled in months.
							 * @placeholder number The number of months.
							 * @example 3 months
							 */
							$part = Text::plocalize(
								"{{number}} month", "{{number}} months",
								$number, 'number', self::class, $text_options
							);
							break;
						case 'Y':
							/**
							 * @description Human-readable time scaled in years.
							 * @placeholder number The number of years.
							 * @example 3 years
							 */
							$part = Text::plocalize(
								"{{number}} year", "{{number}} years",
								$number, 'number', self::class, $text_options
							);
							break;
					}
				}
				
				//add part
				$parts[] = $part;
			}
			if ($is_last) {
				break;
			}
			$period -= $number * $row['time'];
		}
		
		//implode
		if (empty($parts)) {
			return '';
		}
		$parts[0] = $sign . $parts[0];
		if (count($parts) === 1) {
			return $parts[0];
		} elseif ($options->short) {
			return implode(' ', $parts);
		}
		$last_part = array_pop($parts);
		$parts_list = implode(', ', $parts);
		
		//return
		/**
		 * @description Usage of the "and" conjunction in a full human-readable time.
		 * @placeholder list The comma separated list of parts from the human-readable time.
		 * @placeholder last The last part from the human-readable time.
		 * @example 1 day, 12 hours and 35 minutes
		 */
		return Text::localize(
			"{{list}} and {{last}}",
			self::class, $text_options, ['parameters' => ['list' => $parts_list, 'last' => $last_part]]
		);
	}
	
	/**
	 * Get machine-readable period from a given human one.
	 * 
	 * The returning period represents the given one in a machine-readable format and in seconds, 
	 * by converting it as shown in the examples below:<br>
	 * &nbsp; &#8226; &nbsp; <samp>12 seconds</samp> 
	 * or <samp>12s</samp> returns <code>12</code>.<br>
	 * &nbsp; &#8226; &nbsp; <samp>5 minutes and 55 milliseconds</samp> 
	 * or <samp>5min 55ms</samp> returns <code>300.55</code>.<br>
	 * &nbsp; &#8226; &nbsp; <samp>2 hours, 1 minute and 8 seconds</samp> 
	 * or <samp>2h 1min 8s</samp> returns <code>7268</code>.<br>
	 * &nbsp; &#8226; &nbsp; <samp>3 days, 10 hours and 5 minutes</samp> 
	 * or <samp>3D 10h 5min</samp> returns <code>295500</code>.<br>
	 * <br>
	 * Whenever the short form is used, the following symbols are recognized:<br>
	 * &nbsp; &#8226; &nbsp; <samp>Y</samp> : years<br>
	 * &nbsp; &#8226; &nbsp; <samp>M</samp> : months<br>
	 * &nbsp; &#8226; &nbsp; <samp>W</samp> : weeks<br>
	 * &nbsp; &#8226; &nbsp; <samp>D</samp> : days<br>
	 * &nbsp; &#8226; &nbsp; <samp>h</samp> : hours<br>
	 * &nbsp; &#8226; &nbsp; <samp>min</samp> : minutes<br>
	 * &nbsp; &#8226; &nbsp; <samp>s</samp> : seconds<br>
	 * &nbsp; &#8226; &nbsp; <samp>ms</samp> : milliseconds<br>
	 * &nbsp; &#8226; &nbsp; <samp>µs</samp> : microseconds<br>
	 * &nbsp; &#8226; &nbsp; <samp>ns</samp> : nanoseconds
	 * 
	 * @param string $period
	 * <p>The human-readable period to get from.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Utilities\Time\Exceptions\Mperiod\InvalidPeriod
	 * @return float|null
	 * <p>The machine-readable period, in seconds, from the given human one.<br>
	 * If <var>$no_throw</var> is set to boolean <code>true</code>, 
	 * then <code>null</code> is returned if it could not be retrieved.</p>
	 */
	final public static function mperiod(string $period, bool $no_throw = false): ?float
	{
		//parse
		$pattern = '/(?P<signs>[\-+])?(?P<times>\d+(?:[\.,]\d+)?)\s*(?P<multiples>[^\s,]+)?/i';
		if (!preg_match_all($pattern, $period, $matches)) {
			if ($no_throw) {
				return null;
			}
			throw new Exceptions\Mperiod\InvalidPeriod([$period]);
		}
		
		//calculate
		$number = 0.0;
		foreach ($matches['times'] as $i => $time) {
			$time = str_replace(',', '.', $time);
			$multiple = empty($matches['multiples'][$i]) ? 's' : $matches['multiples'][$i];
			if (!self::evaluateMultiple($multiple)) {
				if ($no_throw) {
					return null;
				}
				throw new Exceptions\Mperiod\InvalidPeriod([$period]);
			}
			$number += (float)$time * $multiple * ($matches['signs'][$i] === '-' ? -1 : 1);
		}
		return $number;
	}
	
	/**
	 * Evaluate a given value as a multiple.
	 * 
	 * Only the following types and formats can be evaluated into a multiple:<br>
	 * &nbsp; &#8226; &nbsp; a number in seconds, such as: <code>3600</code> for hours;<br>
	 * &nbsp; &#8226; &nbsp; a symbol string, such as: <code>"h"</code> for hours;<br>
	 * &nbsp; &#8226; &nbsp; a name string in English, such as: <code>"hour"</code> or <code>"hours"</code> for hours.
	 * 
	 * @param mixed $value [reference]
	 * <p>The value to evaluate (validate and sanitize).</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to evaluate as <code>null</code>.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value was successfully evaluated into a multiple.</p>
	 */
	final public static function evaluateMultiple(&$value, bool $nullable = false): bool
	{
		return self::processMultipleCoercion($value, $nullable, true);
	}
	
	/**
	 * Coerce a given value into a multiple.
	 * 
	 * Only the following types and formats can be coerced into a multiple:<br>
	 * &nbsp; &#8226; &nbsp; a number in seconds, such as: <code>3600</code> for hours;<br>
	 * &nbsp; &#8226; &nbsp; a symbol string, such as: <code>"h"</code> for hours;<br>
	 * &nbsp; &#8226; &nbsp; a name string in English, such as: <code>"hour"</code> or <code>"hours"</code> for hours.
	 * 
	 * @param mixed $value
	 * <p>The value to coerce (validate and sanitize).</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @throws \Dracodeum\Kit\Utilities\Time\Exceptions\MultipleCoercionFailed
	 * @return int|float|null
	 * <p>The given value coerced into a multiple.<br>
	 * If nullable, then <code>null</code> may also be returned.</p>
	 */
	final public static function coerceMultiple($value, bool $nullable = false)
	{
		self::processMultipleCoercion($value, $nullable);
		return $value;
	}
	
	/**
	 * Process the coercion of a given value into a multiple.
	 * 
	 * Only the following types and formats can be coerced into a multiple:<br>
	 * &nbsp; &#8226; &nbsp; a number in seconds, such as: <code>3600</code> for hours;<br>
	 * &nbsp; &#8226; &nbsp; a symbol string, such as: <code>"h"</code> for hours;<br>
	 * &nbsp; &#8226; &nbsp; a name string in English, such as: <code>"hour"</code> or <code>"hours"</code> for hours.
	 * 
	 * @param mixed $value [reference]
	 * <p>The value to process (validate and sanitize).</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Utilities\Time\Exceptions\MultipleCoercionFailed
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value was successfully coerced into a multiple.</p>
	 */
	final public static function processMultipleCoercion(&$value, bool $nullable = false, bool $no_throw = false): bool
	{
		//nullable
		if (!isset($value)) {
			if ($nullable) {
				return true;
			} elseif ($no_throw) {
				return false;
			}
			throw new Exceptions\MultipleCoercionFailed([
				'value' => $value,
				'error_code' => Exceptions\MultipleCoercionFailed::ERROR_CODE_NULL,
				'error_message' => "A null value is not allowed."
			]);
		}
		
		//multiples
		if (empty(self::$multiples)) {
			foreach (self::MULTIPLES_TABLE as $row) {
				foreach (['time', 'symbol', 'singular', 'plural'] as $column) {
					self::$multiples[(string)$row[$column]] = $row['time'];
				}
			}
		}
		
		//validate
		if (!is_int($value) && !is_float($value) && !is_string($value)) {
			if ($no_throw) {
				return false;
			}
			throw new Exceptions\MultipleCoercionFailed([
				'value' => $value,
				'error_code' => Exceptions\MultipleCoercionFailed::ERROR_CODE_INVALID_TYPE,
				'error_message' => "Only a multiple given as an integer, float or string is allowed."
			]);
		}
		
		//coerce
		$multiple = (string)$value;
		if (isset(self::$multiples[$multiple])) {
			$value = self::$multiples[$multiple];
			return true;
		}
		
		//finalize
		if ($no_throw) {
			return false;
		}
		throw new Exceptions\MultipleCoercionFailed([
			'value' => $value,
			'error_code' => Exceptions\MultipleCoercionFailed::ERROR_CODE_INVALID,
			'error_message' => "Only the following types and formats can be coerced into a multiple:\n" . 
				" - a number in seconds, such as: 3600 for hours;\n" . 
				" - a symbol string, such as: \"h\" for hours;\n" . 
				" - a name string in English, such as: \"hour\" or \"hours\" for hours."
		]);
	}
	
	/**
	 * Generate a time series from a given start timestamp.
	 * 
	 * The returning time series is an array of times, starting as set by the <var>$start</var> parameter 
	 * and ending as set by the <var>$end</var> parameter, with a specific interval between them as set 
	 * by the <var>$interval</var> parameter.
	 * 
	 * @see https://php.net/manual/en/function.strtotime.php
	 * @see https://php.net/manual/en/class.datetimeinterface.php
	 * @param int|float|string|\DateTimeInterface $start
	 * <p>The start timestamp to generate from, as one of the following:<br>
	 * &nbsp; &#8226; &nbsp; a string as supported by the PHP <code>strtotime</code> function;<br> 
	 * &nbsp; &#8226; &nbsp; an integer or float as the number of seconds since 1970-01-01 00:00:00 UTC;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>DateTimeInterface</code> interface.</p>
	 * @param int|float|string|\DateTimeInterface|null $end [default = null]
	 * <p>The end timestamp to generate to, as one of the following:<br>
	 * &nbsp; &#8226; &nbsp; a string as supported by the PHP <code>strtotime</code> function;<br> 
	 * &nbsp; &#8226; &nbsp; an integer or float as the number of seconds since 1970-01-01 00:00:00 UTC;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>DateTimeInterface</code> interface.<br>
	 * <br>
	 * If not set, then the current system time is used.</p>
	 * @param int|float $interval [default = \Dracodeum\Kit\Enumerations\Time::T1_DAY]
	 * <p>The interval between values to generate with, in seconds.<br>
	 * It must be greater than <code>0</code>.</p>
	 * @param \Dracodeum\Kit\Utilities\Time\Options\Generate|array|null $options [default = null]
	 * <p>Additional options to use, as an instance or <samp>name => value</samp> pairs.</p>
	 * @return float[]|string[]|\DateTime[]|\DateTimeImmutable[]
	 * <p>The generated time series from the given start timestamp, as <samp>timestamp => timestamp</samp> pairs.</p>
	 */
	final public static function generate($start, $end = null, float $interval = ETime::T1_DAY, $options = null): array
	{
		//initialize
		$start_timestamp = $start;
		$end_timestamp = $end;
		if (!is_float($start_timestamp)) {
			$start_timestamp = self::timestamp($start_timestamp, true);
		}
		if (!isset($end_timestamp)) {
			$end_timestamp = microtime(true);
		} elseif (!is_float($end_timestamp)) {
			$end_timestamp = self::timestamp($end_timestamp, true);
		}
		Call::guardParameter('end', $end, $end_timestamp >= $start_timestamp, [
			'hint_message' => "Only a value before or at the given start {{start}} is allowed.",
			'parameters' => ['start' => $start]
		]);
		Call::guardParameter('interval', $interval, $interval > 0.0, [
			'hint_message' => "Only a value greater than 0 is allowed."
		]);
		$options = Options\Generate::coerce($options);
		
		//values
		$values = [];
		$current = $start_timestamp;
		do {
			$values[(string)$current] = $current;
			$current += $interval;
		} while ($current <= $end_timestamp);
		
		//format
		if (isset($options->format)) {
			foreach ($values as &$value) {
				$value = self::format($value, $options->format, $options->timezone);
			}
			unset($value);
			$values = array_unique($values, SORT_REGULAR);
		}
		
		//keys format
		if (isset($options->keys_format)) {
			$keys = array_keys($values);
			foreach ($keys as &$key) {
				$key = self::format($key, $options->keys_format, $options->keys_timezone);
			}
			unset($key);
			$values = array_combine($keys, $values);
		}
		
		//return
		return $values;
	}
}
