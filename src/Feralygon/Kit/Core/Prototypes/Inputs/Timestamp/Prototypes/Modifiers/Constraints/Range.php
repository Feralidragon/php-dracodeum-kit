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
 * Core timestamp input range constraint modifier prototype class.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Core\Prototypes\Inputs\Timestamp
 */
class Range extends Constraints\Range
{
	//Overridden public methods
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options) : string
	{
		return $this->negate
			? UText::localize("Disallowed timestamps range", self::class, $text_options)
			: UText::localize("Allowed timestamps range", self::class, $text_options);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options) : string
	{
		$min_value_string = $this->stringifyValue($this->min_value, $text_options);
		$max_value_string = $this->stringifyValue($this->max_value, $text_options);
		if ($this->negate) {
			if ($this->min_exclusive && $this->max_exclusive) {
				/**
				 * @placeholder min_value The minimum allowed value.
				 * @placeholder max_value The maximum allowed value.
				 * @example Only timestamps before or on 2017-01-15 12:45:00 or after or on 2017-01-17 17:20:00 are allowed.
				 */
				return UText::localize(
					"Only timestamps before or on {{min_value}} or after or on {{max_value}} are allowed.", 
					self::class, $text_options, ['parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]]
				);
			} elseif ($this->min_exclusive) {
				/**
				 * @placeholder min_value The minimum allowed value.
				 * @placeholder max_value The maximum allowed value.
				 * @example Only timestamps before or on 2017-01-15 12:45:00 or after 2017-01-17 17:20:00 are allowed.
				 */
				return UText::localize(
					"Only timestamps before or on {{min_value}} or after {{max_value}} are allowed.", 
					self::class, $text_options, ['parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]]
				);
			} elseif ($this->max_exclusive) {
				/**
				 * @placeholder min_value The minimum allowed value.
				 * @placeholder max_value The maximum allowed value.
				 * @example Only timestamps before 2017-01-15 12:45:00 or after or on 2017-01-17 17:20:00 are allowed.
				 */
				return UText::localize(
					"Only timestamps before {{min_value}} or after or on {{max_value}} are allowed.", 
					self::class, $text_options, ['parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]]
				);
			}
			/**
			 * @placeholder min_value The minimum allowed value.
			 * @placeholder max_value The maximum allowed value.
			 * @example Only timestamps before 2017-01-15 12:45:00 or after 2017-01-17 17:20:00 are allowed.
			 */
			return UText::localize(
				"Only timestamps before {{min_value}} or after {{max_value}} are allowed.", 
				self::class, $text_options, ['parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]]
			);
		} elseif ($this->min_exclusive && $this->max_exclusive) {
			/**
			 * @placeholder min_value The minimum allowed value.
			 * @placeholder max_value The maximum allowed value.
			 * @example Only timestamps after 2017-01-15 12:45:00 and before 2017-01-17 17:20:00 are allowed.
			 */
			return UText::localize(
				"Only timestamps after {{min_value}} and before {{max_value}} are allowed.", 
				self::class, $text_options, ['parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]]
			);
		} elseif ($this->min_exclusive) {
			/**
			 * @placeholder min_value The minimum allowed value.
			 * @placeholder max_value The maximum allowed value.
			 * @example Only timestamps after 2017-01-15 12:45:00 and before or on 2017-01-17 17:20:00 are allowed.
			 */
			return UText::localize(
				"Only timestamps after {{min_value}} and before or on {{max_value}} are allowed.", 
				self::class, $text_options, ['parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]]
			);
		} elseif ($this->max_exclusive) {
			/**
			 * @placeholder min_value The minimum allowed value.
			 * @placeholder max_value The maximum allowed value.
			 * @example Only timestamps after or on 2017-01-15 12:45:00 and before 2017-01-17 17:20:00 are allowed.
			 */
			return UText::localize(
				"Only timestamps after or on {{min_value}} and before {{max_value}} are allowed.", 
				self::class, $text_options, ['parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]]
			);
		}
		/**
		 * @placeholder min_value The minimum allowed value.
		 * @placeholder max_value The maximum allowed value.
		 * @example Only timestamps after or on 2017-01-15 12:45:00 and before or on 2017-01-17 17:20:00 are allowed.
		 */
		return UText::localize(
			"Only timestamps after or on {{min_value}} and before or on {{max_value}} are allowed.", 
			self::class, $text_options, ['parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]]
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
