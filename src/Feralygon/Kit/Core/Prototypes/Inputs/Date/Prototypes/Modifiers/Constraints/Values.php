<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Prototypes\Inputs\Date\Prototypes\Modifiers\Constraints;

use Feralygon\Kit\Core\Prototypes\Input\Prototypes\Modifiers\Constraints;
use Feralygon\Kit\Core\Options\Text as TextOptions;
use Feralygon\Kit\Core\Utilities\{
	Text as UText,
	Time as UTime
};

/**
 * Core date input values constraint modifier prototype class.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Core\Prototypes\Inputs\Date
 */
class Values extends Constraints\Values
{
	//Overridden public methods
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options) : string
	{
		if ($this->negate) {
			/**
			 * @description Core date input values constraint modifier prototype label (negate).
			 * @tags core prototype input date modifier constraint values label
			 */
			return UText::plocalize(
				"Disallowed date", "Disallowed dates",
				count($this->values), null,
				'core.prototypes.inputs.date.prototypes.modifiers.constraints.values', $text_options
			);
		}
		/**
		 * @description Core date input values constraint modifier prototype label.
		 * @tags core prototype input date modifier constraint values label
		 */
		return UText::plocalize(
			"Allowed date", "Allowed dates",
			count($this->values), null,
			'core.prototypes.inputs.date.prototypes.modifiers.constraints.values', $text_options
		);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options) : string
	{
		if ($this->negate) {
			/**
			 * @description Core date input values constraint modifier prototype message (negate).
			 * @placeholder values The list of disallowed date values.
			 * @tags core prototype input date modifier constraint values message
			 * @example The following dates are not allowed: 2017-01-15, 2017-01-17 and 2017-01-18.
			 */
			return UText::plocalize(
				"The following date is not allowed: {{values}}.",
				"The following dates are not allowed: {{values}}.",
				count($this->values), null,
				'core.prototypes.inputs.date.prototypes.modifiers.constraints.values', $text_options, [
					'parameters' => ['values' => $this->getString($text_options)]
				]
			);
		}
		/**
		 * @description Core date input values constraint modifier prototype message.
		 * @placeholder values The list of allowed date values.
		 * @tags core prototype input date modifier constraint values message
		 * @example Only the following dates are allowed: 2017-01-15, 2017-01-17 and 2017-01-18.
		 */
		return UText::plocalize(
			"Only the following date is allowed: {{values}}.",
			"Only the following dates are allowed: {{values}}.",
			count($this->values), null,
			'core.prototypes.inputs.date.prototypes.modifiers.constraints.values', $text_options, [
				'parameters' => ['values' => $this->getString($text_options)]
			]
		);
	}
	
	/** {@inheritdoc} */
	public function getString(TextOptions $text_options) : string
	{
		$strings = [];
		foreach ($this->values as $value) {
			$strings[] = UTime::stringifyDate($value, $text_options);
		}
		return UText::stringify($strings, $text_options, ['flags' => UText::STRING_NONASSOC_CONJUNCTION_AND | UText::STRING_NO_QUOTES]);
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function evaluateValue(&$value) : bool
	{
		return UTime::evaluateDate($value);
	}
}
