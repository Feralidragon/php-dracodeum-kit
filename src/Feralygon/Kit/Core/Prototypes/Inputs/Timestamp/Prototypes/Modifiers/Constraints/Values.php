<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Prototypes\Inputs\Timestamp\Prototypes\Modifiers\Constraints;

use Feralygon\Kit\Core\Prototypes\Input\Prototypes\Modifiers\Constraints;
use Feralygon\Kit\Core\Options\Text as TextOptions;
use Feralygon\Kit\Core\Utilities\{
	Text as UText,
	Time as UTime
};

/**
 * Core timestamp input values constraint modifier prototype class.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Core\Prototypes\Inputs\Timestamp
 */
class Values extends Constraints\Values
{	
	//Overridden public methods
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options) : string
	{
		if ($this->negate) {
			/**
			 * @description Core timestamp input values constraint modifier prototype label (negate).
			 * @tags core prototype input timestamp modifier constraint values label
			 */
			return UText::plocalize(
				"Disallowed timestamp", "Disallowed timestamps",
				count($this->values), null,
				'core.prototypes.inputs.timestamp.prototypes.modifiers.constraints.values', $text_options
			);
		}
		/**
		 * @description Core timestamp input values constraint modifier prototype label.
		 * @tags core prototype input timestamp modifier constraint values label
		 */
		return UText::plocalize(
			"Allowed timestamp", "Allowed timestamps",
			count($this->values), null,
			'core.prototypes.inputs.timestamp.prototypes.modifiers.constraints.values', $text_options
		);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options) : string
	{
		if ($this->negate) {
			/**
			 * @description Core timestamp input values constraint modifier prototype message (negate).
			 * @placeholder values The list of disallowed timestamp values.
			 * @tags core prototype input timestamp modifier constraint values message
			 * @example The following timestamps are not allowed: 2017-01-15 12:45:00, 2017-01-17 17:20:00 and 2017-01-18 03:00:00.
			 */
			return UText::plocalize(
				"The following timestamp is not allowed: {{values}}.",
				"The following timestamps are not allowed: {{values}}.",
				count($this->values), null,
				'core.prototypes.inputs.timestamp.prototypes.modifiers.constraints.values', $text_options, [
					'parameters' => ['values' => $this->getString($text_options)]
				]
			);
		}
		/**
		 * @description Core timestamp input values constraint modifier prototype message.
		 * @placeholder values The list of allowed timestamp values.
		 * @tags core prototype input timestamp modifier constraint values message
		 * @example Only the following timestamps are allowed: 2017-01-15 12:45:00, 2017-01-17 17:20:00 and 2017-01-18 03:00:00.
		 */
		return UText::plocalize(
			"Only the following timestamp is allowed: {{values}}.",
			"Only the following timestamps are allowed: {{values}}.",
			count($this->values), null,
			'core.prototypes.inputs.timestamp.prototypes.modifiers.constraints.values', $text_options, [
				'parameters' => ['values' => $this->getString($text_options)]
			]
		);
	}
	
	/** {@inheritdoc} */
	public function getString(TextOptions $text_options) : string
	{
		$strings = [];
		foreach ($this->values as $value) {
			$strings[] = UTime::stringifyTimestamp($value, $text_options);
		}
		return UText::stringify($strings, $text_options, ['flags' => UText::STRING_NONASSOC_CONJUNCTION_AND | UText::STRING_NO_QUOTES]);
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function evaluateValue(&$value) : bool
	{
		return UTime::evaluateTimestamp($value);
	}
}
