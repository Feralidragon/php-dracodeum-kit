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
 * Core number input values constraint modifier prototype class.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Core\Prototypes\Inputs\Number
 */
class Values extends Constraints\Values
{
	//Overridden public methods
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options) : string
	{
		if ($this->negate) {
			/**
			 * @description Core number input values constraint modifier prototype label (negate).
			 * @tags core prototype input number modifier constraint values label
			 */
			return UText::plocalize(
				"Disallowed number", "Disallowed numbers",
				count($this->values), null,
				'core.prototypes.inputs.number.prototypes.modifiers.constraints.values', $text_options
			);
		}
		/**
		 * @description Core number input values constraint modifier prototype label.
		 * @tags core prototype input number modifier constraint values label
		 */
		return UText::plocalize(
			"Allowed number", "Allowed numbers",
			count($this->values), null,
			'core.prototypes.inputs.number.prototypes.modifiers.constraints.values', $text_options
		);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options) : string
	{
		if ($this->negate) {
			/**
			 * @description Core number input values constraint modifier prototype message (negate).
			 * @placeholder values The list of disallowed number values.
			 * @tags core prototype input number modifier constraint values message
			 * @example The following numbers are not allowed: 3, 8 and 27.
			 */
			return UText::plocalize(
				"The following number is not allowed: {{values}}.",
				"The following numbers are not allowed: {{values}}.",
				count($this->values), null,
				'core.prototypes.inputs.number.prototypes.modifiers.constraints.values', $text_options, [
					'parameters' => ['values' => $this->getString($text_options)]
				]
			);
		}
		/**
		 * @description Core number input values constraint modifier prototype message.
		 * @placeholder values The list of allowed number values.
		 * @tags core prototype input number modifier constraint values message
		 * @example Only the following numbers are allowed: 3, 8 and 27.
		 */
		return UText::plocalize(
			"Only the following number is allowed: {{values}}.",
			"Only the following numbers are allowed: {{values}}.",
			count($this->values), null,
			'core.prototypes.inputs.number.prototypes.modifiers.constraints.values', $text_options, [
				'parameters' => ['values' => $this->getString($text_options)]
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
