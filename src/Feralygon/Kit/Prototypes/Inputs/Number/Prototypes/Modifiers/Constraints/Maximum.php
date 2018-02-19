<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Prototypes\Inputs\Number\Prototypes\Modifiers\Constraints;

use Feralygon\Kit\Prototypes\Input\Prototypes\Modifiers\Constraints;
use Feralygon\Kit\Options\Text as TextOptions;
use Feralygon\Kit\Utilities\{
	Text as UText,
	Type as UType
};

/**
 * Number input maximum constraint modifier prototype class.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Prototypes\Inputs\Number
 */
class Maximum extends Constraints\Maximum
{
	//Overridden public methods
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options) : string
	{
		return UText::localize("Maximum allowed number", self::class, $text_options);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options) : string
	{
		$value_string = $this->stringifyValue($this->value, $text_options);
		if ($this->exclusive) {
			/**
			 * @placeholder value The maximum allowed value.
			 * @example Only a number less than 250 is allowed.
			 */
			return UText::localize(
				"Only a number less than {{value}} is allowed.",
				self::class, $text_options, [
					'parameters' => ['value' => $value_string]
				]
			);
		}
		/**
		 * @placeholder value The maximum allowed value.
		 * @example Only a number less than or equal to 250 is allowed.
		 */
		return UText::localize(
			"Only a number less than or equal to {{value}} is allowed.",
			self::class, $text_options, [
				'parameters' => ['value' => $value_string]
			]
		);
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function evaluateValue(&$value) : bool
	{
		return UType::evaluateNumber($value);
	}
}
