<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Prototypes\Inputs\DateTime\Constraints;

use Dracodeum\Kit\Components\Input\Prototypes\Modifiers\Constraints;
use Dracodeum\Kit\Components\Input\Prototypes\Modifier\Interfaces\Subtype as ISubtype;
use Dracodeum\Kit\Options\Text as TextOptions;
use Dracodeum\Kit\Utilities\{
	Text as UText,
	Time as UTime
};

class Values extends Constraints\Values implements ISubtype
{	
	//Implemented public methods (Dracodeum\Kit\Components\Input\Prototypes\Modifier\Interfaces\Subtype)
	/** {@inheritdoc} */
	public function getSubtype(): string
	{
		return 'datetime';
	}
	
	
	
	//Overridden public methods
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options): string
	{
		return $this->negate
			? UText::plocalize(
				"Disallowed date and time", "Disallowed dates and times",
				count($this->values), null, self::class, $text_options
			)
			: UText::plocalize(
				"Allowed date and time", "Allowed dates and times",
				count($this->values), null, self::class, $text_options
			);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options): string
	{
		//negate
		if ($this->negate) {
			/**
			 * @placeholder values The list of disallowed date and time values.
			 * @example The following dates and times are not allowed: \
			 * 2017-01-15 12:45:00, 2017-01-17 17:20:00 and 2017-01-18 03:00:00.
			 */
			return UText::plocalize(
				"The following date and time is not allowed: {{values}}.",
				"The following dates and times are not allowed: {{values}}.",
				count($this->values), null, self::class, $text_options, [
					'parameters' => ['values' => $this->getString($text_options)]
				]
			);
		}
		
		//default
		/**
		 * @placeholder values The list of allowed date and time values.
		 * @example Only the following dates and times are allowed: \
		 * 2017-01-15 12:45:00, 2017-01-17 17:20:00 or 2017-01-18 03:00:00.
		 */
		return UText::plocalize(
			"Only the following date and time is allowed: {{values}}.",
			"Only the following dates and times are allowed: {{values}}.",
			count($this->values), null, self::class, $text_options, [
				'parameters' => ['values' => $this->getString($text_options)]
			]
		);
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function evaluateValue(&$value): bool
	{
		return UTime::evaluateDateTime($value);
	}
	
	/** {@inheritdoc} */
	protected function stringifyValue($value, TextOptions $text_options): string
	{
		return UTime::stringifyDateTime($value, $text_options);
	}
}
