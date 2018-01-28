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
 * Core date input range constraint modifier prototype class.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Core\Prototypes\Inputs\Date
 */
class Range extends Constraints\Range
{
	//Overridden public methods
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options) : string
	{
		return $this->negate
			? UText::localize("Disallowed dates range", self::class, $text_options)
			: UText::localize("Allowed dates range", self::class, $text_options);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options) : string
	{
		$min_value_string = $this->stringifyValue($this->min_value, $text_options);
		$max_value_string = $this->stringifyValue($this->max_value, $text_options);
		if ($this->negate) {
			if ($this->min_exclusive && $this->max_exclusive) {
				/**
				 * @placeholder min_value The minimum disallowed value.
				 * @placeholder max_value The maximum disallowed value.
				 * @example Only a date before or on 2017-01-15 or after or on 2017-01-17 is allowed.
				 */
				return UText::localize(
					"Only a date before or on {{min_value}} or after or on {{max_value}} is allowed.", 
					self::class, $text_options, [
						'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
					]
				);
			} elseif ($this->min_exclusive) {
				/**
				 * @placeholder min_value The minimum disallowed value.
				 * @placeholder max_value The maximum disallowed value.
				 * @example Only a date before or on 2017-01-15 or after 2017-01-17 is allowed.
				 */
				return UText::localize(
					"Only a date before or on {{min_value}} or after {{max_value}} is allowed.", 
					self::class, $text_options, [
						'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
					]
				);
			} elseif ($this->max_exclusive) {
				/**
				 * @placeholder min_value The minimum disallowed value.
				 * @placeholder max_value The maximum disallowed value.
				 * @example Only a date before 2017-01-15 or after or on 2017-01-17 is allowed.
				 */
				return UText::localize(
					"Only a date before {{min_value}} or after or on {{max_value}} is allowed.", 
					self::class, $text_options, [
						'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
					]
				);
			}
			/**
			 * @placeholder min_value The minimum disallowed value.
			 * @placeholder max_value The maximum disallowed value.
			 * @example Only a date before 2017-01-15 or after 2017-01-17 is allowed.
			 */
			return UText::localize(
				"Only a date before {{min_value}} or after {{max_value}} is allowed.", 
				self::class, $text_options, [
					'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
				]
			);
		} elseif ($this->min_exclusive && $this->max_exclusive) {
			/**
			 * @placeholder min_value The minimum allowed value.
			 * @placeholder max_value The maximum allowed value.
			 * @example Only a date after 2017-01-15 and before 2017-01-17 is allowed.
			 */
			return UText::localize(
				"Only a date after {{min_value}} and before {{max_value}} is allowed.", 
				self::class, $text_options, [
					'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
				]
			);
		} elseif ($this->min_exclusive) {
			/**
			 * @placeholder min_value The minimum allowed value.
			 * @placeholder max_value The maximum allowed value.
			 * @example Only a date after 2017-01-15 and before or on 2017-01-17 is allowed.
			 */
			return UText::localize(
				"Only a date after {{min_value}} and before or on {{max_value}} is allowed.", 
				self::class, $text_options, [
					'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
				]
			);
		} elseif ($this->max_exclusive) {
			/**
			 * @placeholder min_value The minimum allowed value.
			 * @placeholder max_value The maximum allowed value.
			 * @example Only a date after or on 2017-01-15 and before 2017-01-17 is allowed.
			 */
			return UText::localize(
				"Only a date after or on {{min_value}} and before {{max_value}} is allowed.", 
				self::class, $text_options, [
					'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
				]
			);
		}
		/**
		 * @placeholder min_value The minimum allowed value.
		 * @placeholder max_value The maximum allowed value.
		 * @example Only a date after or on 2017-01-15 and before or on 2017-01-17 is allowed.
		 */
		return UText::localize(
			"Only a date after or on {{min_value}} and before or on {{max_value}} is allowed.", 
			self::class, $text_options, [
				'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
			]
		);
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function evaluateValue(&$value) : bool
	{
		return UTime::evaluateDate($value);
	}
	
	/** {@inheritdoc} */
	protected function stringifyValue($value, TextOptions $text_options) : string
	{
		return UTime::stringifyDate($value, $text_options);
	}
}
