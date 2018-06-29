<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Prototypes\Inputs;

use Feralygon\Kit\Prototypes\Input;
use Feralygon\Kit\Prototypes\Input\Interfaces\{
	Information as IInformation,
	ValueStringification as IValueStringification,
	Modifiers as IModifiers
};
use Feralygon\Kit\Components\Input\Components\Modifier;
use Feralygon\Kit\Prototypes\Inputs\DateTime\Prototypes\Modifiers\{
	Constraints,
	Filters
};
use Feralygon\Kit\Components\Input\Prototypes\Modifiers\Filters\Timestamp as TimestampFilters;
use Feralygon\Kit\Options\Text as TextOptions;
use Feralygon\Kit\Components\Input\Options\Info as InfoOptions;
use Feralygon\Kit\Enumerations\InfoScope as EInfoScope;
use Feralygon\Kit\Utilities\{
	Text as UText,
	Time as UTime
};

/**
 * This input prototype represents a date and time, as an Unix timestamp.
 * 
 * Only the following types of values may be evaluated as a date and time:<br>
 * &nbsp; &#8226; &nbsp; an Unix timestamp;<br>
 * &nbsp; &#8226; &nbsp; a custom string format as supported by the PHP <code>strtotime</code> function;<br>
 * &nbsp; &#8226; &nbsp; an object implementing the <code>DateTimeInterface</code> interface.
 * 
 * @since 1.0.0
 * @see https://en.wikipedia.org/wiki/ISO_8601
 * @see https://en.wikipedia.org/wiki/Timestamp
 * @see https://php.net/manual/en/function.strtotime.php
 * @see \Feralygon\Kit\Prototypes\Inputs\DateTime\Prototypes\Modifiers\Constraints\Values
 * [modifier, name = 'constraints.values' or 'values' or 'constraints.non_values' or 'non_values']
 * @see \Feralygon\Kit\Prototypes\Inputs\DateTime\Prototypes\Modifiers\Constraints\Minimum
 * [modifier, name = 'constraints.minimum' or 'minimum']
 * @see \Feralygon\Kit\Prototypes\Inputs\DateTime\Prototypes\Modifiers\Constraints\Maximum
 * [modifier, name = 'constraints.maximum' or 'maximum']
 * @see \Feralygon\Kit\Prototypes\Inputs\DateTime\Prototypes\Modifiers\Constraints\Range
 * [modifier, name = 'constraints.range' or 'range' or 'constraints.non_range' or 'non_range']
 * @see \Feralygon\Kit\Components\Input\Prototypes\Modifiers\Filters\Timestamp\Format
 * [modifier, name = 'filters.format']
 * @see \Feralygon\Kit\Prototypes\Inputs\DateTime\Prototypes\Modifiers\Filters\Iso8601
 * [modifier, name = 'filters.iso8601']
 */
class DateTime extends Input implements IInformation, IValueStringification, IModifiers
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getName(): string
	{
		return 'datetime';
	}
	
	/** {@inheritdoc} */
	public function evaluateValue(&$value): bool
	{
		return UTime::evaluateDateTime($value);
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Prototypes\Input\Interfaces\Information)
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options, InfoOptions $info_options): string
	{
		return UText::localize("Date and time", self::class, $text_options);
	}
	
	/** {@inheritdoc} */
	public function getDescription(TextOptions $text_options, InfoOptions $info_options): string
	{
		/**
		 * @placeholder notations The supported date and time notation entries.
		 * @example A date and time, which may be given using any of the following notations:
		 *  &#8226; Unix timestamp (example: 1484484300);
		 *  &#8226; ISO 8601 (examples: "2017-01-15", "2017-01-15T12:45:00", "2017-01-15T13:45:00+01:00");
		 *  &#8226; Year, month and day, optionally with time and timezone \
		 *  (examples: "2017-01-15", "2017-01-15 12:45:00", "2017-01-15 07:45:00 GMT-5");
		 *  &#8226; American month, day and year, optionally with time and timezone \
		 *  (examples: "1/15/17", "1/15/17 12:45:00", "1/15/17 7:45:00 EST");
		 *  &#8226; Day, month and year in English, optionally with time and timezone \
		 *  (examples: "15 January 2017", "15 January 2017 12:45:00", "15 Jan 2017 15:45:00 GMT+3");
		 *  &#8226; Relative time interval in English \
		 *  (examples: "now", "yesterday", "next Wednesday", "8 days ago");
		 *  &#8226; Fixed date and time with time interval in English \
		 *  (examples: "2017-01-15 +5 days", "1/15/17 12:45:00 -3 hours").
		 */
		return UText::localize(
			"A date and time, which may be given using any of the following notations:\n{{notations}}", 
			self::class, $text_options, [
				'parameters' => [
					'notations' => UText::mbulletify(
						$this->getNotationStrings($text_options), $text_options, ['merge' => true, 'punctuate' => true]
					)
				]
			]
		);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options, InfoOptions $info_options): string
	{
		/**
		 * @placeholder notations The supported date and time notation entries.
		 * @example Only a date and time is allowed, which may be given using any of the following notations:
		 *  &#8226; Unix timestamp (example: 1484484300);
		 *  &#8226; ISO 8601 (examples: "2017-01-15", "2017-01-15T12:45:00", "2017-01-15T13:45:00+01:00");
		 *  &#8226; Year, month and day, optionally with time and timezone \
		 *  (examples: "2017-01-15", "2017-01-15 12:45:00", "2017-01-15 07:45:00 GMT-5");
		 *  &#8226; American month, day and year, optionally with time and timezone \
		 *  (examples: "1/15/17", "1/15/17 12:45:00", "1/15/17 7:45:00 EST");
		 *  &#8226; Day, month and year in English, optionally with time and timezone \
		 *  (examples: "15 January 2017", "15 January 2017 12:45:00", "15 Jan 2017 15:45:00 GMT+3");
		 *  &#8226; Relative time interval in English \
		 *  (examples: "now", "yesterday", "next Wednesday", "8 days ago");
		 *  &#8226; Fixed date and time with time interval in English \
		 *  (examples: "2017-01-15 +5 days", "1/15/17 12:45:00 -3 hours").
		 */
		return UText::localize(
			"Only a date and time is allowed, which may be given using any of the following notations:\n{{notations}}", 
			self::class, $text_options, [
				'parameters' => [
					'notations' => UText::mbulletify(
						$this->getNotationStrings($text_options), $text_options, ['merge' => true, 'punctuate' => true]
					)
				]
			]
		);
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Prototypes\Input\Interfaces\ValueStringification)
	/** {@inheritdoc} */
	public function stringifyValue($value, TextOptions $text_options): string
	{
		return is_string($value) ? $value : UTime::stringifyDateTime($value, $text_options);
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Prototypes\Input\Interfaces\Modifiers)
	/** {@inheritdoc} */
	public function buildModifier(string $name, array $properties = []): ?Modifier
	{
		switch ($name) {
			//constraints
			case 'constraints.values':
				//no break
			case 'values':
				return $this->createConstraint(Constraints\Values::class, $properties);
			case 'constraints.non_values':
				//no break
			case 'non_values':
				return $this->createConstraint(Constraints\Values::class, ['negate' => true] + $properties);
			case 'constraints.minimum':
				//no break
			case 'minimum':
				return $this->createConstraint(Constraints\Minimum::class, $properties);
			case 'constraints.maximum':
				//no break
			case 'maximum':
				return $this->createConstraint(Constraints\Maximum::class, $properties);
			case 'constraints.range':
				//no break
			case 'range':
				return $this->createConstraint(Constraints\Range::class, $properties);
			case 'constraints.non_range':
				//no break
			case 'non_range':
				return $this->createConstraint(Constraints\Range::class, ['negate' => true] + $properties);
			
			//filters
			case 'filters.format':
				return $this->createFilter(TimestampFilters\Format::class, $properties);
			case 'filters.iso8601':
				return $this->createFilter(Filters\Iso8601::class, $properties);
		}
		return null;
	}
	
	
	
	//Protected methods
	/**
	 * Get notation strings.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Options\Text $text_options
	 * <p>The text options instance to use.</p>
	 * @return string[]
	 * <p>The notation strings.</p>
	 */
	protected function getNotationStrings(TextOptions $text_options): array
	{
		//initialize
		$strings = [];
		
		//non-end-user
		if ($text_options->info_scope !== EInfoScope::ENDUSER) {
			//unix timestamp
			/**
			 * @description Unix timestamp notation string.
			 * @placeholder example The date and time example in Unix timestamp notation.
			 * @tags non-end-user
			 * @example Unix timestamp (example: 1484484300)
			 */
			$strings[] = UText::localize(
				"Unix timestamp (example: {{example}})",
				self::class, $text_options, ['parameters' => ['example' => 1484484300]]
			);
			
			//iso 8601
			/**
			 * @description ISO 8601 notation string.
			 * @placeholder examples The list of date and time examples in ISO 8601 notation.
			 * @tags non-end-user
			 * @example ISO 8601 (examples: "2017-01-15", "2017-01-15T12:45:00", "2017-01-15T13:45:00+01:00")
			 */
			$strings[] = UText::localize(
				"ISO 8601 (examples: {{examples}})",
				self::class, $text_options, [
					'parameters' => ['examples' => ['2017-01-15', '2017-01-15T12:45:00', '2017-01-15T13:45:00+01:00']],
					'string_options' => [
						'quote_strings' => true,
						'non_assoc_mode' => UText::STRING_NONASSOC_MODE_COMMA_LIST
					]
				]
			);
		}
		
		//year, month and day (optionally with time and timezone)
		/**
		 * @description Year, month and day (optionally with time and timezone) notation string.
		 * @placeholder examples The list of date and time examples in year, \
		 * month and day (optionally with time and timezone) notation.
		 * @example Year, month and day, optionally with time and timezone \
		 * (examples: "2017-01-15", "2017-01-15 12:45:00", "2017-01-15 07:45:00 GMT-5")
		 */
		$strings[] = UText::localize(
			"Year, month and day, optionally with time and timezone (examples: {{examples}})",
			self::class, $text_options, [
				'parameters' => ['examples' => ['2017-01-15', '2017-01-15 12:45:00', '2017-01-15 07:45:00 GMT-5']],
				'string_options' => [
					'quote_strings' => true,
					'non_assoc_mode' => UText::STRING_NONASSOC_MODE_COMMA_LIST
				]
			]
		);
		
		//american month, day and year (optionally with time and timezone)
		/**
		 * @description American month, day and year (optionally with time and timezone) notation string.
		 * @placeholder examples The list of date and time examples in American month, \
		 * day and year (optionally with time and timezone) notation.
		 * @example American month, day and year, optionally with time and timezone \
		 * (examples: "1/15/17", "1/15/17 12:45:00", "1/15/17 7:45:00 EST")
		 */
		$strings[] = UText::localize(
			"American month, day and year, optionally with time and timezone (examples: {{examples}})",
			self::class, $text_options, [
				'parameters' => ['examples' => ['1/15/17', '1/15/17 12:45:00', '1/15/17 7:45:00 EST']],
				'string_options' => [
					'quote_strings' => true,
					'non_assoc_mode' => UText::STRING_NONASSOC_MODE_COMMA_LIST
				]
			]
		);
		
		//day, month and year in english (optionally with time and timezone)
		/**
		 * @description Day, month and year in English (optionally with time and timezone) notation string.
		 * @placeholder examples The list of date and time examples in day, month and year \
		 * in English (optionally with time and timezone) notation.
		 * @example Day, month and year in English, optionally with time and timezone \
		 * (examples: "15 January 2017", "15 January 2017 12:45:00", "15 Jan 2017 15:45:00 GMT+3")
		 */
		$strings[] = UText::localize(
			"Day, month and year in English, optionally with time and timezone (examples: {{examples}})",
			self::class, $text_options, [
				'parameters' => [
					'examples' => ['15 January 2017', '15 January 2017 12:45:00', '15 Jan 2017 15:45:00 GMT+3']
				],
				'string_options' => [
					'quote_strings' => true,
					'non_assoc_mode' => UText::STRING_NONASSOC_MODE_COMMA_LIST
				]
			]
		);
		
		//relative time interval in english
		/**
		 * @description Relative time interval in English notation string.
		 * @placeholder examples The list of date and time examples in relative time interval in English notation.
		 * @example Relative time interval in English (examples: "now", "yesterday", "next Wednesday", "8 days ago")
		 */
		$strings[] = UText::localize(
			"Relative time interval in English (examples: {{examples}})",
			self::class, $text_options, [
				'parameters' => ['examples' => ['now', 'yesterday', 'next Wednesday', '8 days ago']],
				'string_options' => [
					'quote_strings' => true,
					'non_assoc_mode' => UText::STRING_NONASSOC_MODE_COMMA_LIST
				]
			]
		);
		
		//non-end-user
		if ($text_options->info_scope !== EInfoScope::ENDUSER) {
			//fixed date and time with time interval in English
			/**
			 * @description Fixed date and time with time interval in English notation string.
			 * @placeholder examples The list of date and time examples in fixed date and time with time interval \
			 * in English notation.
			 * @tags non-end-user
			 * @example Fixed date and time with time interval in English \
			 * (examples: "2017-01-15 +5 days", "1/15/17 12:45:00 -3 hours")
			 */
			$strings[] = UText::localize(
				"Fixed date and time with time interval in English (examples: {{examples}})",
				self::class, $text_options, [
					'parameters' => ['examples' => ['2017-01-15 +5 days', '1/15/17 12:45:00 -3 hours']],
					'string_options' => [
						'quote_strings' => true,
						'non_assoc_mode' => UText::STRING_NONASSOC_MODE_COMMA_LIST
					]
				]
			);
		}
		
		//return
		return $strings;
	}
}
