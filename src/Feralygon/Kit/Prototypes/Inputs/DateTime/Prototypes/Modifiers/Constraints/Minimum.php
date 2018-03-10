<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Prototypes\Inputs\DateTime\Prototypes\Modifiers\Constraints;

use Feralygon\Kit\Prototypes\Input\Prototypes\Modifiers\Constraints;
use Feralygon\Kit\Options\Text as TextOptions;
use Feralygon\Kit\Utilities\{
	Text as UText,
	Time as UTime
};

/**
 * @since 1.0.0
 * @see \Feralygon\Kit\Prototypes\Inputs\DateTime
 */
class Minimum extends Constraints\Minimum
{
	//Overridden public methods
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options) : string
	{
		return UText::localize("Minimum allowed date and time", self::class, $text_options);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options) : string
	{
		$value_string = $this->stringifyValue($this->value, $text_options);
		if ($this->exclusive) {
			/**
			 * @placeholder value The minimum allowed value.
			 * @example Only a date and time after 2017-01-15 12:45:00 is allowed.
			 */
			return UText::localize(
				"Only a date and time after {{value}} is allowed.",
				self::class, $text_options, [
					'parameters' => ['value' => $value_string]
				]
			);
		}
		/**
		 * @placeholder value The minimum allowed value.
		 * @example Only a date and time after or on 2017-01-15 12:45:00 is allowed.
		 */
		return UText::localize(
			"Only a date and time after or on {{value}} is allowed.",
			self::class, $text_options, [
				'parameters' => ['value' => $value_string]
			]
		);
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function evaluateValue(&$value) : bool
	{
		return UTime::evaluateDateTime($value);
	}
	
	/** {@inheritdoc} */
	protected function stringifyValue($value, TextOptions $text_options) : string
	{
		return UTime::stringifyDateTime($value, $text_options);
	}
}
