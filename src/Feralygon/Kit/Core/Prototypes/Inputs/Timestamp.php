<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Prototypes\Inputs;

use Feralygon\Kit\Core\Prototypes\Input;
use Feralygon\Kit\Core\Prototypes\Input\Interfaces\{
	Information as IInformation,
	ValueStringification as IValueStringification,
	Modifiers as IModifiers
};
use Feralygon\Kit\Core\Components\Input\Components\Modifier;
use Feralygon\Kit\Core\Prototypes\Inputs\Timestamp\Prototypes\Modifiers\{
	Constraints,
	Filters
};
use Feralygon\Kit\Core\Options\Text as TextOptions;
use Feralygon\Kit\Core\Components\Input\Options\Info as InfoOptions;
use Feralygon\Kit\Core\Enumerations\InfoScope as EInfoScope;
use Feralygon\Kit\Core\Utilities\{
	Text as UText,
	Time as UTime
};

/**
 * Core timestamp input prototype class.
 * 
 * This input prototype represents a timestamp, as an Unix timestamp, for which only the following types of values may be evaluated as such:<br>
 * &nbsp; &#8226; &nbsp; an Unix timestamp;<br>
 * &nbsp; &#8226; &nbsp; a custom string format as supported by the PHP core <code>strtotime</code> function.
 * 
 * @since 1.0.0
 * @see https://en.wikipedia.org/wiki/ISO_8601
 * @see https://en.wikipedia.org/wiki/Timestamp
 * @see https://php.net/manual/en/function.strtotime.php
 * @see \Feralygon\Kit\Core\Prototypes\Inputs\Timestamp\Prototypes\Modifiers\Constraints\Values [modifier, name = 'constraints.values' or 'constraints.non_values']
 * @see \Feralygon\Kit\Core\Prototypes\Inputs\Timestamp\Prototypes\Modifiers\Constraints\Minimum [modifier, name = 'constraints.minimum']
 * @see \Feralygon\Kit\Core\Prototypes\Inputs\Timestamp\Prototypes\Modifiers\Constraints\Maximum [modifier, name = 'constraints.maximum']
 * @see \Feralygon\Kit\Core\Prototypes\Inputs\Timestamp\Prototypes\Modifiers\Constraints\Range [modifier, name = 'constraints.range' or 'constraints.non_range']
 * @see \Feralygon\Kit\Core\Prototypes\Inputs\Timestamp\Prototypes\Modifiers\Filters\Format [modifier, name = 'filters.format']
 * @see \Feralygon\Kit\Core\Prototypes\Inputs\Timestamp\Prototypes\Modifiers\Filters\Iso8601 [modifier, name = 'filters.iso8601']
 */
class Timestamp extends Input implements IInformation, IValueStringification, IModifiers
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getName() : string
	{
		return 'timestamp';
	}
	
	/** {@inheritdoc} */
	public function evaluateValue(&$value) : bool
	{
		return UTime::evaluateTimestamp($value);
	}
	
	
	
	//Implemented public methods (core input prototype information interface)
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options, InfoOptions $info_options) : string
	{
		return UText::localize("Timestamp", self::class, $text_options);
	}
	
	/** {@inheritdoc} */
	public function getDescription(TextOptions $text_options, InfoOptions $info_options) : string
	{
		/**
		 * @placeholder notations The supported timestamp notation entries.
		 * @example A timestamp, which may be given using any of the following notations:
		 *  &#8226; Unix timestamp (example: 1484484300);
		 *  &#8226; ISO 8601 (examples: "2017-01-15", "2017-01-15T12:45:00", "2017-01-15T13:45:00+01:00");
		 *  &#8226; Year, month and day, optionally with time and timezone (examples: "2017-01-15", "2017-01-15 12:45:00", "2017-01-15 07:45:00 GMT-5");
		 *  &#8226; American month, day and year, optionally with time and timezone (examples: "1/15/17", "1/15/17 12:45:00", "1/15/17 7:45:00 EST");
		 *  &#8226; Day, month and year in English, optionally with time and timezone (examples: "15 January 2017", "15 January 2017 12:45:00", "15 Jan 2017 15:45:00 GMT+3");
		 *  &#8226; Relative time interval in English (examples: "now", "yesterday", "next Wednesday", "8 days ago");
		 *  &#8226; Fixed timestamp with time interval in English (examples: "2017-01-15 +5 days", "1/15/17 12:45:00 -3 hours").
		 */
		return UText::localize(
			"A timestamp, which may be given using any of the following notations:\n{{notations}}", 
			self::class, $text_options, [
				'parameters' => [
					'notations' => UText::mbulletify($this->getNotationStrings($text_options), $text_options, ['merge' => true, 'punctuate' => true])
				]
			]
		);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options, InfoOptions $info_options) : string
	{
		/**
		 * @placeholder notations The supported timestamp notation entries.
		 * @example Only a timestamp is allowed, which may be given using any of the following notations:
		 *  &#8226; Unix timestamp (example: 1484484300);
		 *  &#8226; ISO 8601 (examples: "2017-01-15", "2017-01-15T12:45:00", "2017-01-15T13:45:00+01:00");
		 *  &#8226; Year, month and day, optionally with time and timezone (examples: "2017-01-15", "2017-01-15 12:45:00", "2017-01-15 07:45:00 GMT-5");
		 *  &#8226; American month, day and year, optionally with time and timezone (examples: "1/15/17", "1/15/17 12:45:00", "1/15/17 7:45:00 EST");
		 *  &#8226; Day, month and year in English, optionally with time and timezone (examples: "15 January 2017", "15 January 2017 12:45:00", "15 Jan 2017 15:45:00 GMT+3");
		 *  &#8226; Relative time interval in English (examples: "now", "yesterday", "next Wednesday", "8 days ago");
		 *  &#8226; Fixed timestamp with time interval in English (examples: "2017-01-15 +5 days", "1/15/17 12:45:00 -3 hours").
		 */
		return UText::localize(
			"Only a timestamp is allowed, which may be given using any of the following notations:\n{{notations}}", 
			self::class, $text_options, [
				'parameters' => [
					'notations' => UText::mbulletify($this->getNotationStrings($text_options), $text_options, ['merge' => true, 'punctuate' => true])
				]
			]
		);
	}
	
	
	
	//Implemented public methods (core input prototype value stringification interface)
	/** {@inheritdoc} */
	public function stringifyValue($value, TextOptions $text_options) : string
	{
		if (is_int($value)) {
			return UTime::stringifyTimestamp($value, $text_options);
		} elseif (is_string($value)) {
			return $value;
		}
		return UText::stringify($value, $text_options);
	}
	
	
	
	//Implemented public methods (core input prototype modifiers interface)
	/** {@inheritdoc} */
	public function buildModifier(string $name, array $prototype_properties = [], array $properties = []) : ?Modifier
	{
		switch ($name) {
			//constraints
			case 'constraints.values':
				return $this->createConstraint(Constraints\Values::class, $prototype_properties, $properties);
			case 'constraints.non_values':
				return $this->createConstraint(Constraints\Values::class, ['negate' => true] + $prototype_properties, $properties);
			case 'constraints.minimum':
				return $this->createConstraint(Constraints\Minimum::class, $prototype_properties, $properties);
			case 'constraints.maximum':
				return $this->createConstraint(Constraints\Maximum::class, $prototype_properties, $properties);
			case 'constraints.range':
				return $this->createConstraint(Constraints\Range::class, $prototype_properties, $properties);
			case 'constraints.non_range':
				return $this->createConstraint(Constraints\Range::class, ['negate' => true] + $prototype_properties, $properties);
			
			//filters
			case 'filters.format':
				return $this->createFilter(Filters\Format::class, $prototype_properties, $properties);
			case 'filters.iso8601':
				return $this->createFilter(Filters\Iso8601::class, $prototype_properties, $properties);
		}
		return null;
	}
	
	
	
	//Protected methods
	/**
	 * Get notation strings.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Core\Options\Text $text_options <p>The text options instance to use.</p>
	 * @return string[] <p>The notation strings.</p>
	 */
	protected function getNotationStrings(TextOptions $text_options) : array
	{
		//initialize
		$strings = [];
		$example_text_options = TextOptions::load($text_options, true);
		$example_text_options->info_scope = EInfoScope::ENDUSER;
		
		//strings
		if ($text_options->info_scope !== EInfoScope::ENDUSER) {
			/**
			 * @description Unix timestamp notation string.
			 * @placeholder example The timestamp example in Unix timestamp notation.
			 * @tags non-end-user
			 * @example Unix timestamp (example: 1484484300)
			 */
			$strings[] = UText::localize("Unix timestamp (example: {{example}})", self::class, $text_options, [
				'parameters' => ['example' => 1484484300]
			]);
			/**
			 * @description ISO 8601 notation string.
			 * @placeholder examples The list of timestamp examples in ISO 8601 notation.
			 * @tags non-end-user
			 * @example ISO 8601 (examples: "2017-01-15", "2017-01-15T12:45:00", "2017-01-15T13:45:00+01:00")
			 */
			$strings[] = UText::localize("ISO 8601 (examples: {{examples}})", self::class, $text_options, [
				'parameters' => ['examples' => UText::stringify(['2017-01-15', '2017-01-15T12:45:00', '2017-01-15T13:45:00+01:00'], $example_text_options)]
			]);
		}
		/**
		 * @description Year, month and day (optionally with time and timezone) notation string.
		 * @placeholder examples The list of timestamp examples in year, month and day (optionally with time and timezone) notation.
		 * @example Year, month and day, optionally with time and timezone (examples: "2017-01-15", "2017-01-15 12:45:00", "2017-01-15 07:45:00 GMT-5")
		 */
		$strings[] = UText::localize("Year, month and day, optionally with time and timezone (examples: {{examples}})", self::class, $text_options, [
			'parameters' => ['examples' => UText::stringify(['2017-01-15', '2017-01-15 12:45:00', '2017-01-15 07:45:00 GMT-5'], $example_text_options)]
		]);
		/**
		 * @description American month, day and year (optionally with time and timezone) notation string.
		 * @placeholder examples The list of timestamp examples in American month, day and year (optionally with time and timezone) notation.
		 * @example American month, day and year, optionally with time and timezone (examples: "1/15/17", "1/15/17 12:45:00", "1/15/17 7:45:00 EST")
		 */
		$strings[] = UText::localize("American month, day and year, optionally with time and timezone (examples: {{examples}})", self::class, $text_options, [
			'parameters' => ['examples' => UText::stringify(['1/15/17', '1/15/17 12:45:00', '1/15/17 7:45:00 EST'], $example_text_options)]
		]);
		/**
		 * @description Day, month and year in English (optionally with time and timezone) notation string.
		 * @placeholder examples The list of timestamp examples in day, month and year in English (optionally with time and timezone) notation.
		 * @example Day, month and year in English, optionally with time and timezone (examples: "15 January 2017", "15 January 2017 12:45:00", "15 Jan 2017 15:45:00 GMT+3")
		 */
		$strings[] = UText::localize("Day, month and year in English, optionally with time and timezone (examples: {{examples}})", self::class, $text_options, [
			'parameters' => ['examples' => UText::stringify(['15 January 2017', '15 January 2017 12:45:00', '15 Jan 2017 15:45:00 GMT+3'], $example_text_options)]
		]);
		/**
		 * @description Relative time interval in English notation string.
		 * @placeholder examples The list of timestamp examples in relative time interval in English notation.
		 * @example Relative time interval in English (examples: "now", "yesterday", "next Wednesday", "8 days ago")
		 */
		$strings[] = UText::localize("Relative time interval in English (examples: {{examples}})", self::class, $text_options, [
			'parameters' => ['examples' => UText::stringify(['now', 'yesterday', 'next Wednesday', '8 days ago'], $example_text_options)]
		]);
		if ($text_options->info_scope !== EInfoScope::ENDUSER) {
			/**
			 * @description Fixed timestamp with time interval in English notation string.
			 * @placeholder examples The list of timestamp examples in fixed timestamp with time interval in English notation.
			 * @tags non-end-user
			 * @example Fixed timestamp with time interval in English (examples: "2017-01-15 +5 days", "1/15/17 12:45:00 -3 hours")
			 */
			$strings[] = UText::localize("Fixed timestamp with time interval in English (examples: {{examples}})", self::class, $text_options, [
				'parameters' => ['examples' => UText::stringify(['2017-01-15 +5 days', '1/15/17 12:45:00 -3 hours'], $example_text_options)]
			]);
		}
		
		//return
		return $strings;
	}
}
