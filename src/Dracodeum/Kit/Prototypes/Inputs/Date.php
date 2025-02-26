<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Prototypes\Inputs;

use Dracodeum\Kit\Prototypes\Input;
use Dracodeum\Kit\Prototypes\Input\Interfaces\{
	Information as IInformation,
	ValueStringifier as IValueStringifier,
	ConstraintProducer as IConstraintProducer,
	FilterProducer as IFilterProducer
};
use Dracodeum\Kit\Prototypes\Inputs\Date\Constraints;
use Dracodeum\Kit\Components\Input\Prototypes\Modifiers\Filters\Timestamp as TimestampFilters;
use Dracodeum\Kit\Options\Text as TextOptions;
use Dracodeum\Kit\Components\Input\Options\Info as InfoOptions;
use Dracodeum\Kit\Enumerations\InfoScope as EInfoScope;
use Dracodeum\Kit\Utilities\{
	Text as UText,
	Time as UTime
};

/**
 * This input prototype represents a date, as a Unix timestamp.
 * 
 * Only the following types of values may be evaluated as a date:<br>
 * &nbsp; &#8226; &nbsp; a Unix timestamp;<br>
 * &nbsp; &#8226; &nbsp; a custom string format as supported by the PHP <code>strtotime</code> function;<br>
 * &nbsp; &#8226; &nbsp; an object implementing the <code>DateTimeInterface</code> interface.
 * 
 * @see https://en.wikipedia.org/wiki/ISO_8601
 * @see https://en.wikipedia.org/wiki/Timestamp
 * @see https://php.net/manual/en/function.strtotime.php
 * @see https://php.net/manual/en/class.datetimeinterface.php
 */
class Date extends Input implements IInformation, IValueStringifier, IConstraintProducer, IFilterProducer
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getName(): string
	{
		return 'date';
	}
	
	/** {@inheritdoc} */
	public function isScalar(): bool
	{
		return true;
	}
	
	/** {@inheritdoc} */
	public function evaluateValue(&$value): bool
	{
		return UTime::evaluateDate($value);
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Prototypes\Input\Interfaces\Information)
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options, InfoOptions $info_options): string
	{
		return UText::localize("Date", self::class, $text_options);
	}
	
	/** {@inheritdoc} */
	public function getDescription(TextOptions $text_options, InfoOptions $info_options): string
	{
		/**
		 * @placeholder notations The supported date notation entries.
		 * @example A date, which may be given using any of the following notations:
		 *  &#8226; Unix timestamp (example: 1484438400);
		 *  &#8226; ISO 8601 (example: "2017-01-15");
		 *  &#8226; Year, month and day (example: "2017-01-15");
		 *  &#8226; American month, day and year (example: "1/15/17");
		 *  &#8226; Day, month and year in English (example: "15 January 2017");
		 *  &#8226; Relative time interval in English (examples: "today", "yesterday", "next Wednesday", "8 days ago");
		 *  &#8226; Fixed date with time interval in English (examples: "2017-01-15 +5 days", "1/15/17 -3 weeks").
		 */
		return UText::localize(
			"A date, which may be given using any of the following notations:\n{{notations}}", 
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
		 * @placeholder notations The supported date notation entries.
		 * @example Only a date is allowed, which may be given using any of the following notations:
		 *  &#8226; Unix timestamp (example: 1484438400);
		 *  &#8226; ISO 8601 (example: "2017-01-15");
		 *  &#8226; Year, month and day (example: "2017-01-15");
		 *  &#8226; American month, day and year (example: "1/15/17");
		 *  &#8226; Day, month and year in English (example: "15 January 2017");
		 *  &#8226; Relative time interval in English (examples: "today", "yesterday", "next Wednesday", "8 days ago");
		 *  &#8226; Fixed date with time interval in English (examples: "2017-01-15 +5 days", "1/15/17 -3 weeks").
		 */
		return UText::localize(
			"Only a date is allowed, which may be given using any of the following notations:\n{{notations}}", 
			self::class, $text_options, [
				'parameters' => [
					'notations' => UText::mbulletify(
						$this->getNotationStrings($text_options), $text_options, ['merge' => true, 'punctuate' => true]
					)
				]
			]
		);
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Prototypes\Input\Interfaces\ValueStringifier)
	/** {@inheritdoc} */
	public function stringifyValue($value, TextOptions $text_options): string
	{
		return is_string($value) ? $value : UTime::stringifyDate($value, $text_options);
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Prototypes\Input\Interfaces\ConstraintProducer)
	/** {@inheritdoc} */
	public function produceConstraint(string $name, array $properties)
	{
		switch ($name) {
			case 'values':
				return Constraints\Values::class;
			case 'non_values':
				return $this->createConstraint(Constraints\Values::class, ['negate' => true] + $properties);
			case 'minimum':
				return Constraints\Minimum::class;
			case 'maximum':
				return Constraints\Maximum::class;
			case 'range':
				return Constraints\Range::class;
			case 'non_range':
				return $this->createConstraint(Constraints\Range::class, ['negate' => true] + $properties);
		}
		return null;
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Prototypes\Input\Interfaces\FilterProducer)
	/** {@inheritdoc} */
	public function produceFilter(string $name, array $properties)
	{
		switch ($name) {
			case 'format':
				return TimestampFilters\Format::class;
		}
		return null;
	}
	
	
	
	//Protected methods
	/**
	 * Get notation strings.
	 * 
	 * @param \Dracodeum\Kit\Options\Text $text_options
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
			 * @placeholder example The date example in Unix timestamp notation.
			 * @tags non-end-user
			 * @example Unix timestamp (example: 1484438400)
			 */
			$strings[] = UText::localize(
				"Unix timestamp (example: {{example}})",
				self::class, $text_options, ['parameters' => ['example' => 1484438400]]
			);
			
			//iso 8601
			/**
			 * @description ISO 8601 notation string.
			 * @placeholder example The date example in ISO 8601 notation.
			 * @tags non-end-user
			 * @example ISO 8601 (example: "2017-01-15")
			 */
			$strings[] = UText::localize(
				"ISO 8601 (example: {{example}})",
				self::class, $text_options, [
					'parameters' => ['example' => '2017-01-15'],
					'string_options' => ['quote_strings' => true]
				]
			);
		}
		
		//year, month and day
		/**
		 * @description Year, month and day notation string.
		 * @placeholder example The date example in year, month and day notation.
		 * @example Year, month and day (example: "2017-01-15")
		 */
		$strings[] = UText::localize(
			"Year, month and day (example: {{example}})",
			self::class, $text_options, [
				'parameters' => ['example' => '2017-01-15'],
				'string_options' => ['quote_strings' => true]
			]
		);
		
		//american month, day and year
		/**
		 * @description American month, day and year notation string.
		 * @placeholder example The date example in American month, day and year notation.
		 * @example American month, day and year (example: "1/15/17")
		 */
		$strings[] = UText::localize(
			"American month, day and year (example: {{example}})",
			self::class, $text_options, [
				'parameters' => ['example' => '1/15/17'],
				'string_options' => ['quote_strings' => true]
			]
		);
		
		//day, month and year in english
		/**
		 * @description Day, month and year in English notation string.
		 * @placeholder example The date example in day, month and year in English notation.
		 * @example Day, month and year in English (example: "15 January 2017")
		 */
		$strings[] = UText::localize(
			"Day, month and year in English (example: {{example}})",
			self::class, $text_options, [
				'parameters' => ['example' => '15 January 2017'],
				'string_options' => ['quote_strings' => true]
			]
		);
		
		//relative time interval in english
		/**
		 * @description Relative time interval in English notation string.
		 * @placeholder examples The list of date examples in relative time interval in English notation.
		 * @example Relative time interval in English (examples: "today", "yesterday", "next Wednesday", "8 days ago")
		 */
		$strings[] = UText::localize(
			"Relative time interval in English (examples: {{examples}})",
			self::class, $text_options, [
				'parameters' => ['examples' => ['today', 'yesterday', 'next Wednesday', '8 days ago']],
				'string_options' => [
					'quote_strings' => true,
					'non_assoc_mode' => UText::STRING_NONASSOC_MODE_COMMA_LIST
				]
			]
		);
		
		//non-end-user
		if ($text_options->info_scope !== EInfoScope::ENDUSER) {
			//fixed date with time interval in english
			/**
			 * @description Fixed date with time interval in English notation string.
			 * @placeholder examples The list of date examples in fixed date with time interval in English notation.
			 * @tags non-end-user
			 * @example Fixed date with time interval in English (examples: "2017-01-15 +5 days", "1/15/17 -3 weeks")
			 */
			$strings[] = UText::localize(
				"Fixed date with time interval in English (examples: {{examples}})",
				self::class, $text_options, [
					'parameters' => ['examples' => ['2017-01-15 +5 days', '1/15/17 -3 weeks']],
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
