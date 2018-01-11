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
 * Core number input range constraint modifier prototype class.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Core\Prototypes\Inputs\Number
 */
class Range extends Constraints\Range
{
	//Overridden public methods
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options) : string
	{
		if ($this->negate) {
			/**
			 * @description Core number input range constraint modifier prototype label (negate).
			 * @tags core prototype input number modifier constraint range label
			 */
			return UText::localize("Disallowed numbers range", 'core.prototypes.inputs.number.prototypes.modifiers.constraints.range', $text_options);
		}
		/**
		 * @description Core number input range constraint modifier prototype label.
		 * @tags core prototype input number modifier constraint range label
		 */
		return UText::localize("Allowed numbers range", 'core.prototypes.inputs.number.prototypes.modifiers.constraints.range', $text_options);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options) : string
	{
		$min_value_string = $this->stringifyValue($this->min_value, $text_options);
		$max_value_string = $this->stringifyValue($this->max_value, $text_options);
		if ($this->negate) {
			if ($this->min_exclusive && $this->max_exclusive) {
				/**
				 * @description Core number input range constraint modifier prototype message (negate exclusive minimum and maximum).
				 * @placeholder min_value The minimum allowed value.
				 * @placeholder max_value The maximum allowed value.
				 * @tags core prototype input number modifier constraint range message
				 * @example Only numbers lesser than or equal to 100 or greater than or equal to 250 are allowed.
				 */
				return UText::localize(
					"Only numbers lesser than or equal to {{min_value}} or greater than or equal to {{max_value}} are allowed.", 
					'core.prototypes.inputs.number.prototypes.modifiers.constraints.range', $text_options, [
						'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
					]
				);
			} elseif ($this->min_exclusive) {
				/**
				 * @description Core number input range constraint modifier prototype message (negate exclusive minimum).
				 * @placeholder min_value The minimum allowed value.
				 * @placeholder max_value The maximum allowed value.
				 * @tags core prototype input number modifier constraint range message
				 * @example Only numbers lesser than or equal to 100 or greater than 250 are allowed.
				 */
				return UText::localize(
					"Only numbers lesser than or equal to {{min_value}} or greater than {{max_value}} are allowed.", 
					'core.prototypes.inputs.number.prototypes.modifiers.constraints.range', $text_options, [
						'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
					]
				);
			} elseif ($this->max_exclusive) {
				/**
				 * @description Core number input range constraint modifier prototype message (negate exclusive maximum).
				 * @placeholder min_value The minimum allowed value.
				 * @placeholder max_value The maximum allowed value.
				 * @tags core prototype input number modifier constraint range message
				 * @example Only numbers lesser than 100 or greater than or equal to 250 are allowed.
				 */
				return UText::localize(
					"Only numbers lesser than {{min_value}} or greater than or equal to {{max_value}} are allowed.", 
					'core.prototypes.inputs.number.prototypes.modifiers.constraints.range', $text_options, [
						'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
					]
				);
			}
			/**
			 * @description Core number input range constraint modifier prototype message (negate).
			 * @placeholder min_value The minimum allowed value.
			 * @placeholder max_value The maximum allowed value.
			 * @tags core prototype input number modifier constraint range message
			 * @example Only numbers lesser than 100 or greater than 250 are allowed.
			 */
			return UText::localize(
				"Only numbers lesser than {{min_value}} or greater than {{max_value}} are allowed.", 
				'core.prototypes.inputs.number.prototypes.modifiers.constraints.range', $text_options, [
					'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
				]
			);
		} elseif ($this->min_exclusive && $this->max_exclusive) {
			/**
			 * @description Core number input range constraint modifier prototype message (exclusive minimum and maximum).
			 * @placeholder min_value The minimum allowed value.
			 * @placeholder max_value The maximum allowed value.
			 * @tags core prototype input number modifier constraint range message
			 * @example Only numbers greater than 100 and lesser than 250 are allowed.
			 */
			return UText::localize(
				"Only numbers greater than {{min_value}} and lesser than {{max_value}} are allowed.", 
				'core.prototypes.inputs.number.prototypes.modifiers.constraints.range', $text_options, [
					'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
				]
			);
		} elseif ($this->min_exclusive) {
			/**
			 * @description Core number input range constraint modifier prototype message (exclusive minimum).
			 * @placeholder min_value The minimum allowed value.
			 * @placeholder max_value The maximum allowed value.
			 * @tags core prototype input number modifier constraint range message
			 * @example Only numbers greater than 100 and lesser than or equal to 250 are allowed.
			 */
			return UText::localize(
				"Only numbers greater than {{min_value}} and lesser than or equal to {{max_value}} are allowed.", 
				'core.prototypes.inputs.number.prototypes.modifiers.constraints.range', $text_options, [
					'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
				]
			);
		} elseif ($this->max_exclusive) {
			/**
			 * @description Core number input range constraint modifier prototype message (exclusive maximum).
			 * @placeholder min_value The minimum allowed value.
			 * @placeholder max_value The maximum allowed value.
			 * @tags core prototype input number modifier constraint range message
			 * @example Only numbers greater than or equal to 100 and lesser than 250 are allowed.
			 */
			return UText::localize(
				"Only numbers greater than or equal to {{min_value}} and lesser than {{max_value}} are allowed.", 
				'core.prototypes.inputs.number.prototypes.modifiers.constraints.range', $text_options, [
					'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
				]
			);
		}
		/**
		 * @description Core number input range constraint modifier prototype message.
		 * @placeholder min_value The minimum allowed value.
		 * @placeholder max_value The maximum allowed value.
		 * @tags core prototype input number modifier constraint range message
		 * @example Only numbers greater than or equal to 100 and lesser than or equal to 250 are allowed.
		 */
		return UText::localize(
			"Only numbers greater than or equal to {{min_value}} and lesser than or equal to {{max_value}} are allowed.", 
			'core.prototypes.inputs.number.prototypes.modifiers.constraints.range', $text_options, [
				'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
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
