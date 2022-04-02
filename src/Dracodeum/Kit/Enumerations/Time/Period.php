<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
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
	/** 1 minute. */
	public const PT1M = 60;
	
	/** 2 minutes. */
	public const PT2M = 120;
	
	/** 3 minutes. */
	public const PT3M = 180;
	
	/** 4 minutes. */
	public const PT4M = 240;
	
	/** 5 minutes. */
	public const PT5M = 300;
	
	/** 6 minutes. */
	public const PT6M = 360;
	
	/** 7 minutes. */
	public const PT7M = 420;
	
	/** 8 minutes. */
	public const PT8M = 480;
	
	/** 9 minutes. */
	public const PT9M = 540;
	
	/** 10 minutes. */
	public const PT10M = 600;
	
	/** 15 minutes. */
	public const PT15M = 900;
	
	/** 20 minutes. */
	public const PT20M = 1200;
	
	/** 25 minutes. */
	public const PT25M = 1500;
	
	/** 30 minutes. */
	public const PT30M = 1800;
	
	/** 35 minutes. */
	public const PT35M = 2100;
	
	/** 40 minutes. */
	public const PT40M = 2400;
	
	/** 45 minutes. */
	public const PT45M = 2700;
	
	/** 50 minutes. */
	public const PT50M = 3000;
	
	/** 55 minutes. */
	public const PT55M = 3300;
	
	/** 1 hour. */
	public const PT1H = 3600;
	
	/** 2 hours. */
	public const PT2H = 7200;
	
	/** 3 hours. */
	public const PT3H = 10800;
	
	/** 4 hours. */
	public const PT4H = 14400;
	
	/** 5 hours. */
	public const PT5H = 18000;
	
	/** 6 hours. */
	public const PT6H = 21600;
	
	/** 7 hours. */
	public const PT7H = 25200;
	
	/** 8 hours. */
	public const PT8H = 28800;
	
	/** 9 hours. */
	public const PT9H = 32400;
	
	/** 10 hours. */
	public const PT10H = 36000;
	
	/** 11 hours. */
	public const PT11H = 39600;
	
	/** 12 hours. */
	public const PT12H = 43200;
	
	/** 13 hours. */
	public const PT13H = 46800;
	
	/** 14 hours. */
	public const PT14H = 50400;
	
	/** 15 hours. */
	public const PT15H = 54000;
	
	/** 16 hours. */
	public const PT16H = 57600;
	
	/** 17 hours. */
	public const PT17H = 61200;
	
	/** 18 hours. */
	public const PT18H = 64800;
	
	/** 19 hours. */
	public const PT19H = 68400;
	
	/** 20 hours. */
	public const PT20H = 72000;
	
	/** 21 hours. */
	public const PT21H = 75600;
	
	/** 22 hours. */
	public const PT22H = 79200;
	
	/** 23 hours. */
	public const PT23H = 82800;
	
	/** 1 day. */
	public const P1D = 86400;
	
	/** 2 days. */
	public const P2D = 172800;
	
	/** 3 days. */
	public const P3D = 259200;
	
	/** 4 days. */
	public const P4D = 345600;
	
	/** 5 days. */
	public const P5D = 432000;
	
	/** 6 days. */
	public const P6D = 518400;

	/** 1 week. */
	public const P1W = 604800;
	
	/** 2 weeks. */
	public const P2W = 1209600;
	
	/** 3 weeks. */
	public const P3W = 1814400;
	
	/** 4 weeks. */
	public const P4W = 2419200;
	
	/** 1 month. */
	public const P1M = 2592000;
	
	/** 2 months. */
	public const P2M = 5270400;
	
	/** 3 months. */
	public const P3M = 7905600;
	
	/** 4 months. */
	public const P4M = 10540800;
	
	/** 5 months. */
	public const P5M = 13176000;
	
	/** 6 months. */
	public const P6M = 15811200;
	
	/** 7 months. */
	public const P7M = 18446400;
	
	/** 8 months. */
	public const P8M = 21081600;
	
	/** 9 months. */
	public const P9M = 23716800;
	
	/** 10 months. */
	public const P10M = 26352000;
	
	/** 11 months. */
	public const P11M = 28987200;

	/** 1 year. */
	public const P1Y = 31536000;
	
	/** 2 years. */
	public const P2Y = 63072000;
	
	/** 3 years. */
	public const P3Y = 94608000;
	
	/** 4 years. */
	public const P4Y = 126144000;
	
	/** 5 years. */
	public const P5Y = 157680000;
	
	/** 10 years. */
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
