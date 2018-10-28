<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Prototypes\Inputs;

use Feralygon\Kit\Prototypes\Input;
use Feralygon\Kit\Prototypes\Input\Interfaces\{
	Information as IInformation,
	ValueStringifier as IValueStringifier,
	ModifierBuilder as IModifierBuilder
};
use Feralygon\Kit\Components\Input\Components\Modifier;
use Feralygon\Kit\Prototypes\Inputs\Time\Prototypes\Modifiers\{
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
 * This input prototype represents a time, as an Unix timestamp.
 * 
 * Only the following types of values may be evaluated as a time:<br>
 * &nbsp; &#8226; &nbsp; an Unix timestamp;<br>
 * &nbsp; &#8226; &nbsp; a custom string format as supported by the PHP <code>strtotime</code> function;<br>
 * &nbsp; &#8226; &nbsp; an object implementing the <code>DateTimeInterface</code> interface.
 * 
 * @since 1.0.0
 * @see https://en.wikipedia.org/wiki/ISO_8601
 * @see https://en.wikipedia.org/wiki/Timestamp
 * @see https://php.net/manual/en/function.strtotime.php
 * @see \Feralygon\Kit\Prototypes\Inputs\Time\Prototypes\Modifiers\Constraints\Values
 * [modifier, name = 'constraints.values' or 'values' or 'constraints.non_values' or 'non_values']
 * @see \Feralygon\Kit\Prototypes\Inputs\Time\Prototypes\Modifiers\Constraints\Minimum
 * [modifier, name = 'constraints.minimum' or 'minimum']
 * @see \Feralygon\Kit\Prototypes\Inputs\Time\Prototypes\Modifiers\Constraints\Maximum
 * [modifier, name = 'constraints.maximum' or 'maximum']
 * @see \Feralygon\Kit\Prototypes\Inputs\Time\Prototypes\Modifiers\Constraints\Range
 * [modifier, name = 'constraints.range' or 'range' or 'constraints.non_range' or 'non_range']
 * @see \Feralygon\Kit\Components\Input\Prototypes\Modifiers\Filters\Timestamp\Format
 * [modifier, name = 'filters.format']
 * @see \Feralygon\Kit\Prototypes\Inputs\Time\Prototypes\Modifiers\Filters\Iso8601
 * [modifier, name = 'filters.iso8601']
 */
class Time extends Input implements IInformation, IValueStringifier, IModifierBuilder
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getName(): string
	{
		return 'time';
	}
	
	/** {@inheritdoc} */
	public function isScalar(): bool
	{
		return true;
	}
	
	/** {@inheritdoc} */
	public function evaluateValue(&$value): bool
	{
		return UTime::evaluateTime($value);
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Prototypes\Input\Interfaces\Information)
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options, InfoOptions $info_options): string
	{
		return UText::localize("Time", self::class, $text_options);
	}
	
	/** {@inheritdoc} */
	public function getDescription(TextOptions $text_options, InfoOptions $info_options): string
	{
		/**
		 * @placeholder notations The supported time notation entries.
		 * @example A time, which may be given using any of the following notations:
		 *  &#8226; Unix timestamp (example: 45900);
		 *  &#8226; ISO 8601 (examples: "12:45:00", "12:45", "13:45:00+01:00");
		 *  &#8226; Hours, minutes and seconds, \
		 *  optionally with timezone (examples: "12:45:00", "12:45AM", "07:45:00 GMT-5");
		 *  &#8226; Relative time interval in English (examples: "now", "8 hours ago");
		 *  &#8226; Fixed time with time interval in English (examples: "12:45:00 +5 hours", "12:45AM -15 minutes").
		 */
		return UText::localize(
			"A time, which may be given using any of the following notations:\n{{notations}}", 
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
		 * @placeholder notations The supported time notation entries.
		 * @example Only a time is allowed, which may be given using any of the following notations:
		 *  &#8226; Unix timestamp (example: 45900);
		 *  &#8226; ISO 8601 (examples: "12:45:00", "12:45", "13:45:00+01:00");
		 *  &#8226; Hours, minutes and seconds, \
		 *  optionally with timezone (examples: "12:45:00", "12:45AM", "07:45:00 GMT-5");
		 *  &#8226; Relative time interval in English (examples: "now", "8 hours ago");
		 *  &#8226; Fixed time with time interval in English (examples: "12:45:00 +5 hours", "12:45AM -15 minutes").
		 */
		return UText::localize(
			"Only a time is allowed, which may be given using any of the following notations:\n{{notations}}", 
			self::class, $text_options, [
				'parameters' => [
					'notations' => UText::mbulletify(
						$this->getNotationStrings($text_options), $text_options, ['merge' => true, 'punctuate' => true]
					)
				]
			]
		);
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Prototypes\Input\Interfaces\ValueStringifier)
	/** {@inheritdoc} */
	public function stringifyValue($value, TextOptions $text_options): string
	{
		return is_string($value) ? $value : UTime::stringifyTime($value, $text_options);
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Prototypes\Input\Interfaces\ModifierBuilder)
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
			 * @placeholder example The time example in Unix timestamp notation.
			 * @tags non-end-user
			 * @example Unix timestamp (example: 45900)
			 */
			$strings[] = UText::localize(
				"Unix timestamp (example: {{example}})",
				self::class, $text_options, ['parameters' => ['example' => 45900]]
			);
			
			//iso 8601
			/**
			 * @description ISO 8601 notation string.
			 * @placeholder examples The list of time examples in ISO 8601 notation.
			 * @tags non-end-user
			 * @example ISO 8601 (examples: "12:45:00", "12:45", "13:45:00+01:00")
			 */
			$strings[] = UText::localize(
				"ISO 8601 (examples: {{examples}})",
				self::class, $text_options, [
					'parameters' => ['examples' => ['12:45:00', '12:45', '13:45:00+01:00']],
					'string_options' => [
						'quote_strings' => true,
						'non_assoc_mode' => UText::STRING_NONASSOC_MODE_COMMA_LIST
					]
				]
			);
		}
		
		//hours, minutes and seconds (optionally with timezone)
		/**
		 * @description Hours, minutes and seconds (optionally with timezone) notation string.
		 * @placeholder examples The list of time examples in hours, \
		 * minutes and seconds (optionally with timezone) notation.
		 * @example Hours, minutes and seconds, \
		 * optionally with timezone (examples: "12:45:00", "12:45AM", "07:45:00 GMT-5")
		 */
		$strings[] = UText::localize(
			"Hours, minutes and seconds, optionally with timezone (examples: {{examples}})",
			self::class, $text_options, [
				'parameters' => ['examples' => ['12:45:00', '12:45AM', '07:45:00 GMT-5']],
				'string_options' => [
					'quote_strings' => true,
					'non_assoc_mode' => UText::STRING_NONASSOC_MODE_COMMA_LIST
				]
			]
		);
		
		//relative time interval in english
		/**
		 * @description Relative time interval in English notation string.
		 * @placeholder examples The list of time examples in relative time interval in English notation.
		 * @example Relative time interval in English (examples: "now", "8 hours ago")
		 */
		$strings[] = UText::localize(
			"Relative time interval in English (examples: {{examples}})",
			self::class, $text_options, [
				'parameters' => ['examples' => ['now', '8 hours ago']],
				'string_options' => [
					'quote_strings' => true,
					'non_assoc_mode' => UText::STRING_NONASSOC_MODE_COMMA_LIST
				]
			]
		);
		
		//non-end-user
		if ($text_options->info_scope !== EInfoScope::ENDUSER) {
			//fixed time with time interval in english
			/**
			 * @description Fixed time with time interval in English notation string.
			 * @placeholder examples The list of time examples in fixed time with time interval in English notation.
			 * @tags non-end-user
			 * @example Fixed time with time interval in English (examples: "12:45:00 +5 hours", "12:45AM -15 minutes")
			 */
			$strings[] = UText::localize(
				"Fixed time with time interval in English (examples: {{examples}})",
				self::class, $text_options, [
					'parameters' => ['examples' => ['12:45:00 +5 hours', '12:45AM -15 minutes']],
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
