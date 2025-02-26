<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Prototypes\Inputs\Date\Constraints;

use Dracodeum\Kit\Components\Input\Prototypes\Modifiers\Constraints;
use Dracodeum\Kit\Components\Input\Prototypes\Modifier\Interfaces\Subtype as ISubtype;
use Dracodeum\Kit\Options\Text as TextOptions;
use Dracodeum\Kit\Utilities\{
	Text as UText,
	Time as UTime
};

class Maximum extends Constraints\Maximum implements ISubtype
{
	//Implemented public methods (Dracodeum\Kit\Components\Input\Prototypes\Modifier\Interfaces\Subtype)
	/** {@inheritdoc} */
	public function getSubtype(): string
	{
		return 'date';
	}
	
	
	
	//Overridden public methods
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options): string
	{
		return UText::localize("Maximum allowed date", self::class, $text_options);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options): string
	{
		//initialize
		$value_string = $this->stringifyValue($this->value, $text_options);
		
		//exclusive
		if ($this->exclusive) {
			/**
			 * @placeholder value The maximum allowed value.
			 * @example Only a date before 2017-01-17 is allowed.
			 */
			return UText::localize(
				"Only a date before {{value}} is allowed.",
				self::class, $text_options, ['parameters' => ['value' => $value_string]]
			);
		}
		
		//default
		/**
		 * @placeholder value The maximum allowed value.
		 * @example Only a date before or on 2017-01-17 is allowed.
		 */
		return UText::localize(
			"Only a date before or on {{value}} is allowed.",
			self::class, $text_options, ['parameters' => ['value' => $value_string]]
		);
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function evaluateValue(&$value): bool
	{
		return UTime::evaluateDate($value);
	}
	
	/** {@inheritdoc} */
	protected function stringifyValue($value, TextOptions $text_options): string
	{
		return UTime::stringifyDate($value, $text_options);
	}
}
