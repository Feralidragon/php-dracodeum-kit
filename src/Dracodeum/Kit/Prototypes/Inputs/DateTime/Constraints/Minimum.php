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

class Minimum extends Constraints\Minimum implements ISubtype
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
		return UText::localize("Minimum allowed date and time", self::class, $text_options);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options): string
	{
		//initialize
		$value_string = $this->stringifyValue($this->value, $text_options);
		
		//exclusive
		if ($this->exclusive) {
			/**
			 * @placeholder value The minimum allowed value.
			 * @example Only a date and time after 2017-01-15 12:45:00 is allowed.
			 */
			return UText::localize(
				"Only a date and time after {{value}} is allowed.",
				self::class, $text_options, ['parameters' => ['value' => $value_string]]
			);
		}
		
		//default
		/**
		 * @placeholder value The minimum allowed value.
		 * @example Only a date and time after or on 2017-01-15 12:45:00 is allowed.
		 */
		return UText::localize(
			"Only a date and time after or on {{value}} is allowed.",
			self::class, $text_options, ['parameters' => ['value' => $value_string]]
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
