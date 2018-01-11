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
 * Core time input minimum constraint modifier prototype class.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Core\Prototypes\Inputs\Time
 */
class Minimum extends Constraints\Minimum
{
	//Overridden public methods
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options) : string
	{
		/**
		 * @description Core time input minimum constraint modifier prototype label.
		 * @tags core prototype input time modifier constraint minimum label
		 */
		return UText::localize("Minimum allowed time", 'core.prototypes.inputs.time.prototypes.modifiers.constraints.minimum', $text_options);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options) : string
	{
		$value_string = $this->stringifyValue($this->value, $text_options);
		if ($this->exclusive) {
			/**
			 * @description Core time input minimum constraint modifier prototype message (exclusive).
			 * @placeholder value The minimum allowed value.
			 * @tags core prototype input time modifier constraint minimum message
			 * @example Only times after 12:45:00 are allowed.
			 */
			return UText::localize(
				"Only times after {{value}} are allowed.", 
				'core.prototypes.inputs.time.prototypes.modifiers.constraints.minimum', $text_options, [
					'parameters' => ['value' => $value_string]
				]
			);
		}
		/**
		 * @description Core time input minimum constraint modifier prototype message.
		 * @placeholder value The minimum allowed value.
		 * @tags core prototype input time modifier constraint minimum message
		 * @example Only times after or at 12:45:00 are allowed.
		 */
		return UText::localize(
			"Only times after or at {{value}} are allowed.", 
			'core.prototypes.inputs.time.prototypes.modifiers.constraints.minimum', $text_options, [
				'parameters' => ['value' => $value_string]
			]
		);
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function evaluateValue(&$value) : bool
	{
		return UTime::evaluateTime($value);
	}
	
	/** {@inheritdoc} */
	protected function stringifyValue($value, TextOptions $text_options) : string
	{
		return UTime::stringifyTime($value, $text_options);
	}
}
