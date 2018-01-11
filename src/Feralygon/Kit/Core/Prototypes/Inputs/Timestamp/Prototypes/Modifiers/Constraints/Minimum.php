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
 * Core timestamp input minimum constraint modifier prototype class.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Core\Prototypes\Inputs\Timestamp
 */
class Minimum extends Constraints\Minimum
{
	//Overridden public methods
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options) : string
	{
		/**
		 * @description Core timestamp input minimum constraint modifier prototype label.
		 * @tags core prototype input timestamp modifier constraint minimum label
		 */
		return UText::localize("Minimum allowed timestamp", 'core.prototypes.inputs.timestamp.prototypes.modifiers.constraints.minimum', $text_options);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options) : string
	{
		$value_string = UTime::stringifyTimestamp($this->value, $text_options);
		if ($this->exclusive) {
			/**
			 * @description Core timestamp input minimum constraint modifier prototype message (exclusive).
			 * @placeholder value The minimum allowed value.
			 * @tags core prototype input timestamp modifier constraint minimum message
			 * @example Only timestamps after 2017-01-15 12:45:00 are allowed.
			 */
			return UText::localize(
				"Only timestamps after {{value}} are allowed.", 
				'core.prototypes.inputs.timestamp.prototypes.modifiers.constraints.minimum', $text_options, [
					'parameters' => ['value' => $value_string]
				]
			);
		}
		/**
		 * @description Core timestamp input minimum constraint modifier prototype message.
		 * @placeholder value The minimum allowed value.
		 * @tags core prototype input timestamp modifier constraint minimum message
		 * @example Only timestamps after or on 2017-01-15 12:45:00 are allowed.
		 */
		return UText::localize(
			"Only timestamps after or on {{value}} are allowed.", 
			'core.prototypes.inputs.timestamp.prototypes.modifiers.constraints.minimum', $text_options, [
				'parameters' => ['value' => $value_string]
			]
		);
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function evaluateValue(&$value) : bool
	{
		return UTime::evaluateTimestamp($value);
	}
	
	/** {@inheritdoc} */
	protected function stringifyValue($value, TextOptions $text_options) : string
	{
		return UTime::stringifyTimestamp($value, $text_options);
	}
}
