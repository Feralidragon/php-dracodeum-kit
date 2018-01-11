<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Prototypes\Inputs\Number\Prototypes\Modifiers\Constraints;

use Feralygon\Kit\Core\Prototypes\Input\Prototypes\Modifiers\Constraints;
use Feralygon\Kit\Core\Options\Text as TextOptions;
use Feralygon\Kit\Core\Utilities\{
	Text as UText,
	Type as UType
};

/**
 * Core number input minimum constraint modifier prototype class.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Core\Prototypes\Inputs\Number
 */
class Minimum extends Constraints\Minimum
{
	//Overridden public methods
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options) : string
	{
		/**
		 * @description Core number input minimum constraint modifier prototype label.
		 * @tags core prototype input number modifier constraint minimum label
		 */
		return UText::localize("Minimum allowed number", 'core.prototypes.inputs.number.prototypes.modifiers.constraints.minimum', $text_options);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options) : string
	{
		$value_string = $this->stringifyValue($this->value, $text_options);
		if ($this->exclusive) {
			/**
			 * @description Core number input minimum constraint modifier prototype message (exclusive).
			 * @placeholder value The minimum allowed value.
			 * @tags core prototype input number modifier constraint minimum message
			 * @example Only numbers greater than 250 are allowed.
			 */
			return UText::localize(
				"Only numbers greater than {{value}} are allowed.", 
				'core.prototypes.inputs.number.prototypes.modifiers.constraints.minimum', $text_options, [
					'parameters' => ['value' => $value_string]
				]
			);
		}
		/**
		 * @description Core number input minimum constraint modifier prototype message.
		 * @placeholder value The minimum allowed value.
		 * @tags core prototype input number modifier constraint minimum message
		 * @example Only numbers greater than or equal to 250 are allowed.
		 */
		return UText::localize(
			"Only numbers greater than or equal to {{value}} are allowed.", 
			'core.prototypes.inputs.number.prototypes.modifiers.constraints.minimum', $text_options, [
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
