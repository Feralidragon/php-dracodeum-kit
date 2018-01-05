<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Enumerations;

use Feralygon\Kit\Core\Enumeration;
use Feralygon\Kit\Core\Options\Text as TextOptions;
use Feralygon\Kit\Core\Utilities\{
	Time as UTime,
	Text as UText
};

/**
 * Core time enumeration class.
 * 
 * This enumeration represents time values in seconds.
 * 
 * @since 1.0.0
 */
class Time extends Enumeration
{
	//Public constants
	/** Time in seconds equivalent to: 1 minute. */
	public const T1_MINUTE = 60;
	
	/** Time in seconds equivalent to: 2 minutes. */
	public const T2_MINUTES = 120;
	
	/** Time in seconds equivalent to: 3 minutes. */
	public const T3_MINUTES = 180;
	
	/** Time in seconds equivalent to: 4 minutes. */
	public const T4_MINUTES = 240;
	
	/** Time in seconds equivalent to: 5 minutes. */
	public const T5_MINUTES = 300;
	
	/** Time in seconds equivalent to: 6 minutes. */
	public const T6_MINUTES = 360;
	
	/** Time in seconds equivalent to: 7 minutes. */
	public const T7_MINUTES = 420;
	
	/** Time in seconds equivalent to: 8 minutes. */
	public const T8_MINUTES = 480;
	
	/** Time in seconds equivalent to: 9 minutes. */
	public const T9_MINUTES = 540;
	
	/** Time in seconds equivalent to: 10 minutes. */
	public const T10_MINUTES = 600;
	
	/** Time in seconds equivalent to: 15 minutes. */
	public const T15_MINUTES = 900;
	
	/** Time in seconds equivalent to: 20 minutes. */
	public const T20_MINUTES = 1200;
	
	/** Time in seconds equivalent to: 25 minutes. */
	public const T25_MINUTES = 1500;
	
	/** Time in seconds equivalent to: 30 minutes. */
	public const T30_MINUTES = 1800;
	
	/** Time in seconds equivalent to: 35 minutes. */
	public const T35_MINUTES = 2100;
	
	/** Time in seconds equivalent to: 40 minutes. */
	public const T40_MINUTES = 2400;
	
	/** Time in seconds equivalent to: 45 minutes. */
	public const T45_MINUTES = 2700;
	
	/** Time in seconds equivalent to: 50 minutes. */
	public const T50_MINUTES = 3000;
	
	/** Time in seconds equivalent to: 55 minutes. */
	public const T55_MINUTES = 3300;
	
	/** Time in seconds equivalent to: 1 hour. */
	public const T1_HOUR = 3600;
	
	/** Time in seconds equivalent to: 2 hours. */
	public const T2_HOURS = 7200;
	
	/** Time in seconds equivalent to: 3 hours. */
	public const T3_HOURS = 10800;
	
	/** Time in seconds equivalent to: 4 hours. */
	public const T4_HOURS = 14400;
	
	/** Time in seconds equivalent to: 5 hours. */
	public const T5_HOURS = 18000;
	
	/** Time in seconds equivalent to: 6 hours. */
	public const T6_HOURS = 21600;
	
	/** Time in seconds equivalent to: 7 hours. */
	public const T7_HOURS = 25200;
	
	/** Time in seconds equivalent to: 8 hours. */
	public const T8_HOURS = 28800;
	
	/** Time in seconds equivalent to: 9 hours. */
	public const T9_HOURS = 32400;
	
	/** Time in seconds equivalent to: 10 hours. */
	public const T10_HOURS = 36000;
	
	/** Time in seconds equivalent to: 11 hours. */
	public const T11_HOURS = 39600;
	
	/** Time in seconds equivalent to: 12 hours. */
	public const T12_HOURS = 43200;
	
	/** Time in seconds equivalent to: 13 hours. */
	public const T13_HOURS = 46800;
	
	/** Time in seconds equivalent to: 14 hours. */
	public const T14_HOURS = 50400;
	
	/** Time in seconds equivalent to: 15 hours. */
	public const T15_HOURS = 54000;
	
	/** Time in seconds equivalent to: 16 hours. */
	public const T16_HOURS = 57600;
	
	/** Time in seconds equivalent to: 17 hours. */
	public const T17_HOURS = 61200;
	
	/** Time in seconds equivalent to: 18 hours. */
	public const T18_HOURS = 64800;
	
	/** Time in seconds equivalent to: 19 hours. */
	public const T19_HOURS = 68400;
	
	/** Time in seconds equivalent to: 20 hours. */
	public const T20_HOURS = 72000;
	
	/** Time in seconds equivalent to: 21 hours. */
	public const T21_HOURS = 75600;
	
	/** Time in seconds equivalent to: 22 hours. */
	public const T22_HOURS = 79200;
	
	/** Time in seconds equivalent to: 23 hours. */
	public const T23_HOURS = 82800;
	
	/** Time in seconds equivalent to: 1 day. */
	public const T1_DAY = 86400;
	
	/** Time in seconds equivalent to: 2 days. */
	public const T2_DAYS = 172800;
	
	/** Time in seconds equivalent to: 3 days. */
	public const T3_DAYS = 259200;
	
	/** Time in seconds equivalent to: 4 days. */
	public const T4_DAYS = 345600;
	
	/** Time in seconds equivalent to: 5 days. */
	public const T5_DAYS = 432000;
	
	/** Time in seconds equivalent to: 6 days. */
	public const T6_DAYS = 518400;

	/** Time in seconds equivalent to: 1 week. */
	public const T1_WEEK = 604800;
	
	/** Time in seconds equivalent to: 2 weeks. */
	public const T2_WEEKS = 1209600;
	
	/** Time in seconds equivalent to: 3 weeks. */
	public const T3_WEEKS = 1814400;
	
	/** Time in seconds equivalent to: 4 weeks. */
	public const T4_WEEKS = 2419200;
	
	/** Time in seconds equivalent to: 1 month. */
	public const T1_MONTH = 2592000;
	
	/** Time in seconds equivalent to: 2 months. */
	public const T2_MONTHS = 5270400;
	
	/** Time in seconds equivalent to: 3 months. */
	public const T3_MONTHS = 7948800;
	
	/** Time in seconds equivalent to: 4 months. */
	public const T4_MONTHS = 10368000;
	
	/** Time in seconds equivalent to: 5 months. */
	public const T5_MONTHS = 12960000;
	
	/** Time in seconds equivalent to: 6 months. */
	public const T6_MONTHS = 15811200;

	/** Time in seconds equivalent to: 1 year. */
	public const T1_YEAR = 31536000;
	
	/** Time in seconds equivalent to: 2 years. */
	public const T2_YEARS = 63072000;
	
	/** Time in seconds equivalent to: 3 years. */
	public const T3_YEARS = 94608000;
	
	/** Time in seconds equivalent to: 4 years. */
	public const T4_YEARS = 126144000;
	
	/** Time in seconds equivalent to: 5 years. */
	public const T5_YEARS = 157680000;
	
	/** Time in seconds equivalent to: 10 years. */
	public const T10_YEARS = 315360000;
	
	
	
	//Implemented protected static methods (core enumeration information trait)
	/** {@inheritdoc} */
	protected static function retrieveLabel(string $name, TextOptions $text_options) : ?string
	{
		return UTime::hperiod(static::getNameValue($name), $text_options);
	}
	
	/** {@inheritdoc} */
	protected static function retrieveDescription(string $name, TextOptions $text_options) : ?string
	{
		$label = static::retrieveLabel($name, $text_options);
		if (isset($label)) {
			/**
			 * @description Core time enumeration description.
			 * @placeholder time The human-readable time.
			 * @tags core enumeration time description
			 * @example Time in seconds equivalent to 2 hours.
			 */
			return UText::localize("Time in seconds equivalent to {{time}}.", 'core.enumerations.time', $text_options, ['parameters' => ['time' => $label]]);
		}
		return null;
	}
}
