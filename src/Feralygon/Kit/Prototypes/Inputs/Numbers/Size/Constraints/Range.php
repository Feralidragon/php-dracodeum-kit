<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Prototypes\Inputs\Numbers\Size\Constraints;

use Feralygon\Kit\Prototypes\Inputs\Number\Constraints;
use Feralygon\Kit\Options\Text as TextOptions;
use Feralygon\Kit\Utilities\{
	Byte as UByte,
	Text as UText
};

class Range extends Constraints\Range
{
	//Overridden public methods
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options): string
	{
		return $this->negate
			? UText::localize("Disallowed sizes range", self::class, $text_options)
			: UText::localize("Allowed sizes range", self::class, $text_options);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options): string
	{
		//initialize
		$min_value_string = $this->stringifyValue($this->min_value, $text_options);
		$max_value_string = $this->stringifyValue($this->max_value, $text_options);
		
		//negate
		if ($this->negate) {
			//min and max exclusive
			if ($this->min_exclusive && $this->max_exclusive) {
				/**
				 * @placeholder min_value The minimum disallowed value.
				 * @placeholder max_value The maximum disallowed value.
				 * @example Only a size less than or equal to 100 kB or greater than or equal to 250 MB is allowed.
				 */
				return UText::localize(
					"Only a size less than or equal to {{min_value}} or " . 
						"greater than or equal to {{max_value}} is allowed.", 
					self::class, $text_options, [
						'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
					]
				);
			}
			
			//min exclusive
			if ($this->min_exclusive) {
				/**
				 * @placeholder min_value The minimum disallowed value.
				 * @placeholder max_value The maximum disallowed value.
				 * @example Only a size less than or equal to 100 kB or greater than 250 MB is allowed.
				 */
				return UText::localize(
					"Only a size less than or equal to {{min_value}} or " . 
						"greater than {{max_value}} is allowed.", 
					self::class, $text_options, [
						'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
					]
				);
			}
			
			//max exclusive
			if ($this->max_exclusive) {
				/**
				 * @placeholder min_value The minimum disallowed value.
				 * @placeholder max_value The maximum disallowed value.
				 * @example Only a size less than 100 kB or greater than or equal to 250 MB is allowed.
				 */
				return UText::localize(
					"Only a size less than {{min_value}} or " . 
						"greater than or equal to {{max_value}} is allowed.", 
					self::class, $text_options, [
						'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
					]
				);
			}
			
			//default
			/**
			 * @placeholder min_value The minimum disallowed value.
			 * @placeholder max_value The maximum disallowed value.
			 * @example Only a size less than 100 kB or greater than 250 MB is allowed.
			 */
			return UText::localize(
				"Only a size less than {{min_value}} or " . 
					"greater than {{max_value}} is allowed.", 
				self::class, $text_options, [
					'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
				]
			);
		}
		
		//min and max exclusive
		if ($this->min_exclusive && $this->max_exclusive) {
			/**
			 * @placeholder min_value The minimum allowed value.
			 * @placeholder max_value The maximum allowed value.
			 * @example Only a size greater than 100 kB and less than 250 MB is allowed.
			 */
			return UText::localize(
				"Only a size greater than {{min_value}} and " . 
					"less than {{max_value}} is allowed.", 
				self::class, $text_options, [
					'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
				]
			);
		}
		
		//min exclusive
		if ($this->min_exclusive) {
			/**
			 * @placeholder min_value The minimum allowed value.
			 * @placeholder max_value The maximum allowed value.
			 * @example Only a size greater than 100 kB and less than or equal to 250 MB is allowed.
			 */
			return UText::localize(
				"Only a size greater than {{min_value}} and " . 
					"less than or equal to {{max_value}} is allowed.", 
				self::class, $text_options, [
					'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
				]
			);
		}
		
		//max exclusive
		if ($this->max_exclusive) {
			/**
			 * @placeholder min_value The minimum allowed value.
			 * @placeholder max_value The maximum allowed value.
			 * @example Only a size greater than or equal to 100 kB and less than 250 MB is allowed.
			 */
			return UText::localize(
				"Only a size greater than or equal to {{min_value}} and " . 
					"less than {{max_value}} is allowed.", 
				self::class, $text_options, [
					'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
				]
			);
		}
		
		//default
		/**
		 * @placeholder min_value The minimum allowed value.
		 * @placeholder max_value The maximum allowed value.
		 * @example Only a size greater than or equal to 100 kB and less than or equal to 250 MB is allowed.
		 */
		return UText::localize(
			"Only a size greater than or equal to {{min_value}} and " . 
				"less than or equal to {{max_value}} is allowed.", 
			self::class, $text_options, [
				'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
			]
		);
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function evaluateValue(&$value): bool
	{
		return UByte::evaluateSize($value);
	}
	
	/** {@inheritdoc} */
	protected function stringifyValue($value, TextOptions $text_options): string
	{
		return UByte::hvalue($value);
	}
}
