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
 * Core time input range constraint modifier prototype class.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Core\Prototypes\Inputs\Time
 */
class Range extends Constraints\Range
{
	//Overridden public methods
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options) : string
	{
		if ($this->negate) {
			/**
			 * @description Core time input range constraint modifier prototype label (negate).
			 * @tags core prototype input time modifier constraint range label
			 */
			return UText::localize("Disallowed times range", 'core.prototypes.inputs.time.prototypes.modifiers.constraints.range', $text_options);
		}
		/**
		 * @description Core time input range constraint modifier prototype label.
		 * @tags core prototype input time modifier constraint range label
		 */
		return UText::localize("Allowed times range", 'core.prototypes.inputs.time.prototypes.modifiers.constraints.range', $text_options);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options) : string
	{
		$min_value_string = UTime::stringifyTime($this->min_value, $text_options);
		$max_value_string = UTime::stringifyTime($this->max_value, $text_options);
		if ($this->negate) {
			if ($this->min_exclusive && $this->max_exclusive) {
				/**
				 * @description Core time input range constraint modifier prototype message (negate exclusive minimum and maximum).
				 * @placeholder min_value The minimum allowed value.
				 * @placeholder max_value The maximum allowed value.
				 * @tags core prototype input time modifier constraint range message
				 * @example Only times before or at 12:45:00 or after or at 17:20:00 are allowed.
				 */
				return UText::localize(
					"Only times before or at {{min_value}} or after or at {{max_value}} are allowed.", 
					'core.prototypes.inputs.time.prototypes.modifiers.constraints.range', $text_options, [
						'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
					]
				);
			} elseif ($this->min_exclusive) {
				/**
				 * @description Core time input range constraint modifier prototype message (negate exclusive minimum).
				 * @placeholder min_value The minimum allowed value.
				 * @placeholder max_value The maximum allowed value.
				 * @tags core prototype input time modifier constraint range message
				 * @example Only times before or at 12:45:00 or after 17:20:00 are allowed.
				 */
				return UText::localize(
					"Only times before or at {{min_value}} or after {{max_value}} are allowed.", 
					'core.prototypes.inputs.time.prototypes.modifiers.constraints.range', $text_options, [
						'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
					]
				);
			} elseif ($this->max_exclusive) {
				/**
				 * @description Core time input range constraint modifier prototype message (negate exclusive maximum).
				 * @placeholder min_value The minimum allowed value.
				 * @placeholder max_value The maximum allowed value.
				 * @tags core prototype input time modifier constraint range message
				 * @example Only times before 12:45:00 or after or at 17:20:00 are allowed.
				 */
				return UText::localize(
					"Only times before {{min_value}} or after or at {{max_value}} are allowed.", 
					'core.prototypes.inputs.time.prototypes.modifiers.constraints.range', $text_options, [
						'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
					]
				);
			}
			/**
			 * @description Core time input range constraint modifier prototype message (negate).
			 * @placeholder min_value The minimum allowed value.
			 * @placeholder max_value The maximum allowed value.
			 * @tags core prototype input time modifier constraint range message
			 * @example Only times before 12:45:00 or after 17:20:00 are allowed.
			 */
			return UText::localize(
				"Only times before {{min_value}} or after {{max_value}} are allowed.", 
				'core.prototypes.inputs.time.prototypes.modifiers.constraints.range', $text_options, [
					'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
				]
			);
		} elseif ($this->min_exclusive && $this->max_exclusive) {
			/**
			 * @description Core time input range constraint modifier prototype message (exclusive minimum and maximum).
			 * @placeholder min_value The minimum allowed value.
			 * @placeholder max_value The maximum allowed value.
			 * @tags core prototype input time modifier constraint range message
			 * @example Only times after 12:45:00 and before 17:20:00 are allowed.
			 */
			return UText::localize(
				"Only times after {{min_value}} and before {{max_value}} are allowed.", 
				'core.prototypes.inputs.time.prototypes.modifiers.constraints.range', $text_options, [
					'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
				]
			);
		} elseif ($this->min_exclusive) {
			/**
			 * @description Core time input range constraint modifier prototype message (exclusive minimum).
			 * @placeholder min_value The minimum allowed value.
			 * @placeholder max_value The maximum allowed value.
			 * @tags core prototype input time modifier constraint range message
			 * @example Only times after 12:45:00 and before or at 17:20:00 are allowed.
			 */
			return UText::localize(
				"Only times after {{min_value}} and before or at {{max_value}} are allowed.", 
				'core.prototypes.inputs.time.prototypes.modifiers.constraints.range', $text_options, [
					'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
				]
			);
		} elseif ($this->max_exclusive) {
			/**
			 * @description Core time input range constraint modifier prototype message (exclusive maximum).
			 * @placeholder min_value The minimum allowed value.
			 * @placeholder max_value The maximum allowed value.
			 * @tags core prototype input time modifier constraint range message
			 * @example Only times after or at 12:45:00 and before 17:20:00 are allowed.
			 */
			return UText::localize(
				"Only times after or at {{min_value}} and before {{max_value}} are allowed.", 
				'core.prototypes.inputs.time.prototypes.modifiers.constraints.range', $text_options, [
					'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
				]
			);
		}
		/**
		 * @description Core time input range constraint modifier prototype message.
		 * @placeholder min_value The minimum allowed value.
		 * @placeholder max_value The maximum allowed value.
		 * @tags core prototype input time modifier constraint range message
		 * @example Only times after or at 12:45:00 and before or at 17:20:00 are allowed.
		 */
		return UText::localize(
			"Only times after or at {{min_value}} and before or at {{max_value}} are allowed.", 
			'core.prototypes.inputs.time.prototypes.modifiers.constraints.range', $text_options, [
				'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
			]
		);
	}
	
	/** {@inheritdoc} */
	public function getString(TextOptions $text_options) : string
	{
		$min_value_string = UTime::stringifyTime($this->min_value, $text_options);
		$max_value_string = UTime::stringifyTime($this->max_value, $text_options);
		if ($this->min_exclusive && $this->max_exclusive) {
			/**
			 * @description Core time input range constraint modifier prototype string (exclusive minimum and maximum).
			 * @placeholder min_value The minimum allowed value.
			 * @placeholder max_value The maximum allowed value.
			 * @tags core prototype input time modifier constraint range string
			 * @example 12:45:00 (exclusive) to 17:20:00 (exclusive)
			 */
			return UText::localize(
				"{{min_value}} (exclusive) to {{max_value}} (exclusive)", 
				'core.prototypes.inputs.time.prototypes.modifiers.constraints.range', $text_options, [
					'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
				]
			);
		} elseif ($this->min_exclusive) {
			/**
			 * @description Core time input range constraint modifier prototype string (exclusive minimum).
			 * @placeholder min_value The minimum allowed value.
			 * @placeholder max_value The maximum allowed value.
			 * @tags core prototype input time modifier constraint range string
			 * @example 12:45:00 (exclusive) to 17:20:00
			 */
			return UText::localize(
				"{{min_value}} (exclusive) to {{max_value}}", 
				'core.prototypes.inputs.time.prototypes.modifiers.constraints.range', $text_options, [
					'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
				]
			);
		} elseif ($this->max_exclusive) {
			/**
			 * @description Core time input range constraint modifier prototype string (exclusive maximum).
			 * @placeholder min_value The minimum allowed value.
			 * @placeholder max_value The maximum allowed value.
			 * @tags core prototype input time modifier constraint range string
			 * @example 12:45:00 to 17:20:00 (exclusive)
			 */
			return UText::localize(
				"{{min_value}} to {{max_value}} (exclusive)", 
				'core.prototypes.inputs.time.prototypes.modifiers.constraints.range', $text_options, [
					'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
				]
			);
		}
		/**
		 * @description Core time input range constraint modifier prototype string.
		 * @placeholder min_value The minimum allowed value.
		 * @placeholder max_value The maximum allowed value.
		 * @tags core prototype input time modifier constraint range string
		 * @example 12:45:00 to 17:20:00
		 */
		return UText::localize(
			"{{min_value}} to {{max_value}}", 
			'core.prototypes.inputs.time.prototypes.modifiers.constraints.range', $text_options, [
				'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
			]
		);
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function evaluateValue(&$value) : bool
	{
		return UTime::evaluateTime($value);
	}
}
