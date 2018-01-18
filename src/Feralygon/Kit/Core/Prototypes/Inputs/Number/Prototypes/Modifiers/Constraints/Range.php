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
				 * @example Only numbers lesser than or equal to 100 or greater than or equal to 250 are allowed.
				 */
				return UText::localize(
					"Only numbers lesser than or equal to {{min_value}} or greater than or equal to {{max_value}} are allowed.", 
					self::class, $text_options, ['parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]]
				);
			} elseif ($this->min_exclusive) {
				/**
				 * @placeholder min_value The minimum disallowed value.
				 * @placeholder max_value The maximum disallowed value.
				 * @example Only numbers lesser than or equal to 100 or greater than 250 are allowed.
				 */
				return UText::localize(
					"Only numbers lesser than or equal to {{min_value}} or greater than {{max_value}} are allowed.", 
					self::class, $text_options, ['parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]]
				);
			} elseif ($this->max_exclusive) {
				/**
				 * @placeholder min_value The minimum disallowed value.
				 * @placeholder max_value The maximum disallowed value.
				 * @example Only numbers lesser than 100 or greater than or equal to 250 are allowed.
				 */
				return UText::localize(
					"Only numbers lesser than {{min_value}} or greater than or equal to {{max_value}} are allowed.", 
					self::class, $text_options, ['parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]]
				);
			}
			/**
			 * @placeholder min_value The minimum disallowed value.
			 * @placeholder max_value The maximum disallowed value.
			 * @example Only numbers lesser than 100 or greater than 250 are allowed.
			 */
			return UText::localize(
				"Only numbers lesser than {{min_value}} or greater than {{max_value}} are allowed.", 
				self::class, $text_options, ['parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]]
			);
		} elseif ($this->min_exclusive && $this->max_exclusive) {
			/**
			 * @placeholder min_value The minimum allowed value.
			 * @placeholder max_value The maximum allowed value.
			 * @example Only numbers greater than 100 and lesser than 250 are allowed.
			 */
			return UText::localize(
				"Only numbers greater than {{min_value}} and lesser than {{max_value}} are allowed.", 
				self::class, $text_options, ['parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]]
			);
		} elseif ($this->min_exclusive) {
			/**
			 * @placeholder min_value The minimum allowed value.
			 * @placeholder max_value The maximum allowed value.
			 * @example Only numbers greater than 100 and lesser than or equal to 250 are allowed.
			 */
			return UText::localize(
				"Only numbers greater than {{min_value}} and lesser than or equal to {{max_value}} are allowed.", 
				self::class, $text_options, ['parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]]
			);
		} elseif ($this->max_exclusive) {
			/**
			 * @placeholder min_value The minimum allowed value.
			 * @placeholder max_value The maximum allowed value.
			 * @example Only numbers greater than or equal to 100 and lesser than 250 are allowed.
			 */
			return UText::localize(
				"Only numbers greater than or equal to {{min_value}} and lesser than {{max_value}} are allowed.", 
				self::class, $text_options, ['parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]]
			);
		}
		/**
		 * @placeholder min_value The minimum allowed value.
		 * @placeholder max_value The maximum allowed value.
		 * @example Only numbers greater than or equal to 100 and lesser than or equal to 250 are allowed.
		 */
		return UText::localize(
			"Only numbers greater than or equal to {{min_value}} and lesser than or equal to {{max_value}} are allowed.", 
			self::class, $text_options, ['parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]]
		);
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function evaluateValue(&$value) : bool
	{
		return UType::evaluateNumber($value);
	}
}
