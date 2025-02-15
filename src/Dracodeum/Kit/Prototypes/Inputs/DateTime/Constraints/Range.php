<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Prototypes\Inputs\DateTime\Constraints;

use Dracodeum\Kit\Components\Input\Prototypes\Modifiers\Constraints;
use Dracodeum\Kit\Components\Input\Prototypes\Modifier\Interfaces\Subtype as ISubtype;
use Dracodeum\Kit\Options\Text as TextOptions;
use Dracodeum\Kit\Utilities\{
	Text as UText,
	Time as UTime
};

class Range extends Constraints\Range implements ISubtype
{
	//Implemented public methods (Dracodeum\Kit\Components\Input\Prototypes\Modifier\Interfaces\Subtype)
	/** {@inheritdoc} */
	public function getSubtype(): string
	{
		return 'datetime';
	}
	
	
	
	//Overridden public methods
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options): string
	{
		return $this->negate
			? UText::localize("Disallowed date and time range", self::class, $text_options)
			: UText::localize("Allowed date and time range", self::class, $text_options);
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
				 * @example Only a date and time before or on 2017-01-15 12:45:00 \
				 * or after or on 2017-01-17 17:20:00 is allowed.
				 */
				return UText::localize(
					"Only a date and time before or on {{min_value}} or after or on {{max_value}} is allowed.", 
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
				 * @example Only a date and time before or on 2017-01-15 12:45:00 \
				 * or after 2017-01-17 17:20:00 is allowed.
				 */
				return UText::localize(
					"Only a date and time before or on {{min_value}} or after {{max_value}} is allowed.", 
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
				 * @example Only a date and time before 2017-01-15 12:45:00 \
				 * or after or on 2017-01-17 17:20:00 is allowed.
				 */
				return UText::localize(
					"Only a date and time before {{min_value}} or after or on {{max_value}} is allowed.", 
					self::class, $text_options, [
						'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
					]
				);
			}
			
			//default
			/**
			 * @placeholder min_value The minimum disallowed value.
			 * @placeholder max_value The maximum disallowed value.
			 * @example Only a date and time before 2017-01-15 12:45:00 \
			 * or after 2017-01-17 17:20:00 is allowed.
			 */
			return UText::localize(
				"Only a date and time before {{min_value}} or after {{max_value}} is allowed.", 
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
			 * @example Only a date and time after 2017-01-15 12:45:00 \
			 * and before 2017-01-17 17:20:00 is allowed.
			 */
			return UText::localize(
				"Only a date and time after {{min_value}} and before {{max_value}} is allowed.", 
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
			 * @example Only a date and time after 2017-01-15 12:45:00 \
			 * and before or on 2017-01-17 17:20:00 is allowed.
			 */
			return UText::localize(
				"Only a date and time after {{min_value}} and before or on {{max_value}} is allowed.", 
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
			 * @example Only a date and time after or on 2017-01-15 12:45:00 \
			 * and before 2017-01-17 17:20:00 is allowed.
			 */
			return UText::localize(
				"Only a date and time after or on {{min_value}} and before {{max_value}} is allowed.", 
				self::class, $text_options, [
					'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
				]
			);
		}
		
		//default
		/**
		 * @placeholder min_value The minimum allowed value.
		 * @placeholder max_value The maximum allowed value.
		 * @example Only a date and time after or on 2017-01-15 12:45:00 \
		 * and before or on 2017-01-17 17:20:00 is allowed.
		 */
		return UText::localize(
			"Only a date and time after or on {{min_value}} and before or on {{max_value}} is allowed.", 
			self::class, $text_options, [
				'parameters' => ['min_value' => $min_value_string, 'max_value' => $max_value_string]
			]
		);
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function evaluateValue(&$value): bool
	{
		return UTime::evaluateDateTime($value);
	}
	
	/** {@inheritdoc} */
	protected function stringifyValue($value, TextOptions $text_options): string
	{
		return UTime::stringifyDateTime($value, $text_options);
	}
}
