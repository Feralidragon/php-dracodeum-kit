<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Prototypes\Inputs\Time\Prototypes\Modifiers\Constraints;

use Feralygon\Kit\Core\Prototypes\Input\Prototypes\Modifiers\Constraints;
use Feralygon\Kit\Core\Options\Text as TextOptions;
use Feralygon\Kit\Core\Utilities\{
	Text as UText,
	Time as UTime
};

/**
 * Core time input values constraint modifier prototype class.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Core\Prototypes\Inputs\Time
 */
class Values extends Constraints\Values
{
	//Overridden public methods
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options) : string
	{
		if ($this->negate) {
			/**
			 * @description Core time input values constraint modifier prototype label (negate).
			 * @tags core prototype input time modifier constraint values label
			 */
			return UText::plocalize(
				"Disallowed time", "Disallowed times",
				count($this->values), null,
				'core.prototypes.inputs.time.prototypes.modifiers.constraints.values', $text_options
			);
		}
		/**
		 * @description Core time input values constraint modifier prototype label.
		 * @tags core prototype input time modifier constraint values label
		 */
		return UText::plocalize(
			"Allowed time", "Allowed times",
			count($this->values), null,
			'core.prototypes.inputs.time.prototypes.modifiers.constraints.values', $text_options
		);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options) : string
	{
		if ($this->negate) {
			/**
			 * @description Core time input values constraint modifier prototype message (negate).
			 * @placeholder values The list of disallowed time values.
			 * @tags core prototype input time modifier constraint values message
			 * @example The following times are not allowed: 03:00:00, 12:45:00 and 17:20:00.
			 */
			return UText::plocalize(
				"The following time is not allowed: {{values}}.",
				"The following times are not allowed: {{values}}.",
				count($this->values), null,
				'core.prototypes.inputs.time.prototypes.modifiers.constraints.values', $text_options, [
					'parameters' => ['values' => $this->getString($text_options)]
				]
			);
		}
		/**
		 * @description Core time input values constraint modifier prototype message.
		 * @placeholder values The list of allowed time values.
		 * @tags core prototype input time modifier constraint values message
		 * @example Only the following times are allowed: 03:00:00, 12:45:00 and 17:20:00.
		 */
		return UText::plocalize(
			"Only the following time is allowed: {{values}}.",
			"Only the following times are allowed: {{values}}.",
			count($this->values), null,
			'core.prototypes.inputs.time.prototypes.modifiers.constraints.values', $text_options, [
				'parameters' => ['values' => $this->getString($text_options)]
			]
		);
	}
	
	/** {@inheritdoc} */
	public function getString(TextOptions $text_options) : string
	{
		$strings = [];
		foreach ($this->values as $value) {
			$strings[] = UTime::stringifyTime($value, $text_options);
		}
		return UText::stringify($strings, $text_options, ['flags' => UText::STRING_NONASSOC_CONJUNCTION_AND | UText::STRING_NO_QUOTES]);
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function evaluateValue(&$value) : bool
	{
		return UTime::evaluateTime($value);
	}
}
