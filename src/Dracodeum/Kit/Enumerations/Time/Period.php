<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Enumerations\Time;

use Dracodeum\Kit\Enumeration;
use Dracodeum\Kit\Options\Text as TextOptions;
use Dracodeum\Kit\Utilities\{
	Time as UTime,
	Text as UText
};

/**
 * This enumeration represents time period values in seconds, with names using the ISO 8601 format.
 * 
 * @see https://en.wikipedia.org/wiki/ISO_8601
 */
class Period extends Enumeration
{
	//Public constants
	/** Period in seconds corresponding to 1 minute. */
	public const PT1M = 60;
	
	/** Period in seconds corresponding to 2 minutes. */
	public const PT2M = 120;
	
	/** Period in seconds corresponding to 3 minutes. */
	public const PT3M = 180;
	
	/** Period in seconds corresponding to 4 minutes. */
	public const PT4M = 240;
	
	/** Period in seconds corresponding to 5 minutes. */
	public const PT5M = 300;
	
	/** Period in seconds corresponding to 6 minutes. */
	public const PT6M = 360;
	
	/** Period in seconds corresponding to 7 minutes. */
	public const PT7M = 420;
	
	/** Period in seconds corresponding to 8 minutes. */
	public const PT8M = 480;
	
	/** Period in seconds corresponding to 9 minutes. */
	public const PT9M = 540;
	
	/** Period in seconds corresponding to 10 minutes. */
	public const PT10M = 600;
	
	/** Period in seconds corresponding to 15 minutes. */
	public const PT15M = 900;
	
	/** Period in seconds corresponding to 20 minutes. */
	public const PT20M = 1200;
	
	/** Period in seconds corresponding to 25 minutes. */
	public const PT25M = 1500;
	
	/** Period in seconds corresponding to 30 minutes. */
	public const PT30M = 1800;
	
	/** Period in seconds corresponding to 35 minutes. */
	public const PT35M = 2100;
	
	/** Period in seconds corresponding to 40 minutes. */
	public const PT40M = 2400;
	
	/** Period in seconds corresponding to 45 minutes. */
	public const PT45M = 2700;
	
	/** Period in seconds corresponding to 50 minutes. */
	public const PT50M = 3000;
	
	/** Period in seconds corresponding to 55 minutes. */
	public const PT55M = 3300;
	
	/** Period in seconds corresponding to 1 hour. */
	public const PT1H = 3600;
	
	/** Period in seconds corresponding to 2 hours. */
	public const PT2H = 7200;
	
	/** Period in seconds corresponding to 3 hours. */
	public const PT3H = 10800;
	
	/** Period in seconds corresponding to 4 hours. */
	public const PT4H = 14400;
	
	/** Period in seconds corresponding to 5 hours. */
	public const PT5H = 18000;
	
	/** Period in seconds corresponding to 6 hours. */
	public const PT6H = 21600;
	
	/** Period in seconds corresponding to 7 hours. */
	public const PT7H = 25200;
	
	/** Period in seconds corresponding to 8 hours. */
	public const PT8H = 28800;
	
	/** Period in seconds corresponding to 9 hours. */
	public const PT9H = 32400;
	
	/** Period in seconds corresponding to 10 hours. */
	public const PT10H = 36000;
	
	/** Period in seconds corresponding to 11 hours. */
	public const PT11H = 39600;
	
	/** Period in seconds corresponding to 12 hours. */
	public const PT12H = 43200;
	
	/** Period in seconds corresponding to 13 hours. */
	public const PT13H = 46800;
	
	/** Period in seconds corresponding to 14 hours. */
	public const PT14H = 50400;
	
	/** Period in seconds corresponding to 15 hours. */
	public const PT15H = 54000;
	
	/** Period in seconds corresponding to 16 hours. */
	public const PT16H = 57600;
	
	/** Period in seconds corresponding to 17 hours. */
	public const PT17H = 61200;
	
	/** Period in seconds corresponding to 18 hours. */
	public const PT18H = 64800;
	
	/** Period in seconds corresponding to 19 hours. */
	public const PT19H = 68400;
	
	/** Period in seconds corresponding to 20 hours. */
	public const PT20H = 72000;
	
	/** Period in seconds corresponding to 21 hours. */
	public const PT21H = 75600;
	
	/** Period in seconds corresponding to 22 hours. */
	public const PT22H = 79200;
	
	/** Period in seconds corresponding to 23 hours. */
	public const PT23H = 82800;
	
	/** Period in seconds corresponding to 1 day. */
	public const P1D = 86400;
	
	/** Period in seconds corresponding to 2 days. */
	public const P2D = 172800;
	
	/** Period in seconds corresponding to 3 days. */
	public const P3D = 259200;
	
	/** Period in seconds corresponding to 4 days. */
	public const P4D = 345600;
	
	/** Period in seconds corresponding to 5 days. */
	public const P5D = 432000;
	
	/** Period in seconds corresponding to 6 days. */
	public const P6D = 518400;

	/** Period in seconds corresponding to 1 week. */
	public const P1W = 604800;
	
	/** Period in seconds corresponding to 2 weeks. */
	public const P2W = 1209600;
	
	/** Period in seconds corresponding to 3 weeks. */
	public const P3W = 1814400;
	
	/** Period in seconds corresponding to 4 weeks. */
	public const P4W = 2419200;
	
	/** Period in seconds corresponding to 1 month. */
	public const P1M = 2592000;
	
	/** Period in seconds corresponding to 2 months. */
	public const P2M = 5270400;
	
	/** Period in seconds corresponding to 3 months. */
	public const P3M = 7905600;
	
	/** Period in seconds corresponding to 4 months. */
	public const P4M = 10540800;
	
	/** Period in seconds corresponding to 5 months. */
	public const P5M = 13176000;
	
	/** Period in seconds corresponding to 6 months. */
	public const P6M = 15811200;
	
	/** Period in seconds corresponding to 7 months. */
	public const P7M = 18446400;
	
	/** Period in seconds corresponding to 8 months. */
	public const P8M = 21081600;
	
	/** Period in seconds corresponding to 9 months. */
	public const P9M = 23716800;
	
	/** Period in seconds corresponding to 10 months. */
	public const P10M = 26352000;
	
	/** Period in seconds corresponding to 11 months. */
	public const P11M = 28987200;

	/** Period in seconds corresponding to 1 year. */
	public const P1Y = 31536000;
	
	/** Period in seconds corresponding to 2 years. */
	public const P2Y = 63072000;
	
	/** Period in seconds corresponding to 3 years. */
	public const P3Y = 94608000;
	
	/** Period in seconds corresponding to 4 years. */
	public const P4Y = 126144000;
	
	/** Period in seconds corresponding to 5 years. */
	public const P5Y = 157680000;
	
	/** Period in seconds corresponding to 10 years. */
	public const P10Y = 315360000;
	
	
	
	//Implemented protected static methods (Dracodeum\Kit\Enumeration\Traits\Information)
	/** {@inheritdoc} */
	protected static function returnLabel(string $name, TextOptions $text_options): ?string
	{
		return UTime::hperiod(static::getNameValue($name), $text_options, ['limit' => 1]);
	}
	
	/** {@inheritdoc} */
	protected static function returnDescription(string $name, TextOptions $text_options): ?string
	{
		$label = static::returnLabel($name, $text_options);
		if ($label !== null) {
			/**
			 * @placeholder period The time period.
			 * @example Period in seconds corresponding to 2 hours.
			 */
			return UText::localize(
				"Period in seconds corresponding to {{period}}.",
				self::class, $text_options, ['parameters' => ['period' => $label]]
			);
		}
		return null;
	}
}
