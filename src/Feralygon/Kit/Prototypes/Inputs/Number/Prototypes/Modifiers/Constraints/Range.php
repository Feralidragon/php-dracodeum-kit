<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Prototypes\Inputs\Number\Prototypes\Modifiers\Constraints;

use Feralygon\Kit\Prototypes\Input\Prototypes\Modifiers\Constraints;
use Feralygon\Kit\Options\Text as TextOptions;
use Feralygon\Kit\Utilities\{
	Text as UText,
	Type as UType
};

/**
 * Number input range constraint modifier prototype class.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Prototypes\Inputs\Number
 */
class Range extends Constraints\Range
{
	//Overridden public methods
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options) : string
	{
		return $this->negate
			? UText::localize("Disallowed numbers range", self::class, $text_options)
			: UText::localize("Allowed numbers range", self::class, $text_options);
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
				 * @example Only a number less than or equal to 100 or greater than or equal to 250 is allowed.
				 */
				return UText::localize(
					"Only a number less than or equal to {{min_value}} or " . 
						"greater than or equal to {{max_value}} is allowed.", 
					self::class, $text_options, [
						'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
					]
				);
			} elseif ($this->min_exclusive) {
				/**
				 * @placeholder min_value The minimum disallowed value.
				 * @placeholder max_value The maximum disallowed value.
				 * @example Only a number less than or equal to 100 or greater than 250 is allowed.
				 */
				return UText::localize(
					"Only a number less than or equal to {{min_value}} or " . 
						"greater than {{max_value}} is allowed.", 
					self::class, $text_options, [
						'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
					]
				);
			} elseif ($this->max_exclusive) {
				/**
				 * @placeholder min_value The minimum disallowed value.
				 * @placeholder max_value The maximum disallowed value.
				 * @example Only a number less than 100 or greater than or equal to 250 is allowed.
				 */
				return UText::localize(
					"Only a number less than {{min_value}} or " . 
						"greater than or equal to {{max_value}} is allowed.", 
					self::class, $text_options, [
						'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
					]
				);
			}
			/**
			 * @placeholder min_value The minimum disallowed value.
			 * @placeholder max_value The maximum disallowed value.
			 * @example Only a number less than 100 or greater than 250 is allowed.
			 */
			return UText::localize(
				"Only a number less than {{min_value}} or " . 
					"greater than {{max_value}} is allowed.", 
				self::class, $text_options, [
					'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
				]
			);
		} elseif ($this->min_exclusive && $this->max_exclusive) {
			/**
			 * @placeholder min_value The minimum allowed value.
			 * @placeholder max_value The maximum allowed value.
			 * @example Only a number greater than 100 and less than 250 is allowed.
			 */
			return UText::localize(
				"Only a number greater than {{min_value}} and " . 
					"less than {{max_value}} is allowed.", 
				self::class, $text_options, [
					'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
				]
			);
		} elseif ($this->min_exclusive) {
			/**
			 * @placeholder min_value The minimum allowed value.
			 * @placeholder max_value The maximum allowed value.
			 * @example Only a number greater than 100 and less than or equal to 250 is allowed.
			 */
			return UText::localize(
				"Only a number greater than {{min_value}} and " . 
					"less than or equal to {{max_value}} is allowed.", 
				self::class, $text_options, [
					'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
				]
			);
		} elseif ($this->max_exclusive) {
			/**
			 * @placeholder min_value The minimum allowed value.
			 * @placeholder max_value The maximum allowed value.
			 * @example Only a number greater than or equal to 100 and less than 250 is allowed.
			 */
			return UText::localize(
				"Only a number greater than or equal to {{min_value}} and " . 
					"less than {{max_value}} is allowed.", 
				self::class, $text_options, [
					'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
				]
			);
		}
		/**
		 * @placeholder min_value The minimum allowed value.
		 * @placeholder max_value The maximum allowed value.
		 * @example Only a number greater than or equal to 100 and less than or equal to 250 is allowed.
		 */
		return UText::localize(
			"Only a number greater than or equal to {{min_value}} and " . 
				"less than or equal to {{max_value}} is allowed.", 
			self::class, $text_options, [
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
