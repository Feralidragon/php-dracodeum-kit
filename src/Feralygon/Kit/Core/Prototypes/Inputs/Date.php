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
use Feralygon\Kit\Core\Prototypes\Inputs\Date\Prototypes\Modifiers\{
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
 * Core date input prototype class.
 * 
 * This input prototype represents a date, as an Unix timestamp, for which only the following types of values may be evaluated as such:<br>
 * &nbsp; &#8226; &nbsp; an Unix timestamp;<br>
 * &nbsp; &#8226; &nbsp; a custom string format as supported by the PHP core <code>strtotime</code> function.
 * 
 * @since 1.0.0
 * @see https://en.wikipedia.org/wiki/ISO_8601
 * @see https://en.wikipedia.org/wiki/Timestamp
 * @see https://php.net/manual/en/function.strtotime.php
 * @see \Feralygon\Kit\Core\Prototypes\Inputs\Date\Prototypes\Modifiers\Constraints\Values [modifier, name = 'constraints.values' or 'constraints.non_values']
 * @see \Feralygon\Kit\Core\Prototypes\Inputs\Date\Prototypes\Modifiers\Constraints\Minimum [modifier, name = 'constraints.minimum']
 * @see \Feralygon\Kit\Core\Prototypes\Inputs\Date\Prototypes\Modifiers\Constraints\Maximum [modifier, name = 'constraints.maximum']
 * @see \Feralygon\Kit\Core\Prototypes\Inputs\Date\Prototypes\Modifiers\Constraints\Range [modifier, name = 'constraints.range' or 'constraints.non_range']
 * @see \Feralygon\Kit\Core\Prototypes\Inputs\Date\Prototypes\Modifiers\Filters\Format [modifier, name = 'filters.format']
 * @see \Feralygon\Kit\Core\Prototypes\Inputs\Date\Prototypes\Modifiers\Filters\Iso8601 [modifier, name = 'filters.iso8601']
 */
class Date extends Input implements IInformation, IValueStringification, IModifiers
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getName() : string
	{
		return 'date';
	}
	
	/** {@inheritdoc} */
	public function evaluateValue(&$value) : bool
	{
		return UTime::evaluateDate($value);
	}
	
	
	
	//Implemented public methods (core input prototype information interface)
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options, InfoOptions $info_options) : string
	{
		return UText::localize("Date", self::class, $text_options);
	}
	
	/** {@inheritdoc} */
	public function getDescription(TextOptions $text_options, InfoOptions $info_options) : string
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
					'notations' => UText::mbulletify($this->getNotationStrings($text_options), $text_options, ['merge' => true, 'punctuate' => true])
				]
			]
		);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options, InfoOptions $info_options) : string
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
			return UTime::stringifyDate($value, $text_options);
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
			 * @placeholder example The date example in Unix timestamp notation.
			 * @tags non-end-user
			 * @example Unix timestamp (example: 1484438400)
			 */
			$strings[] = UText::localize("Unix timestamp (example: {{example}})", self::class, $text_options, [
				'parameters' => ['example' => 1484438400]
			]);
			/**
			 * @description ISO 8601 notation string.
			 * @placeholder example The date example in ISO 8601 notation.
			 * @tags non-end-user
			 * @example ISO 8601 (example: "2017-01-15")
			 */
			$strings[] = UText::localize("ISO 8601 (example: {{example}})", self::class, $text_options, [
				'parameters' => ['example' => '2017-01-15']
			]);
		}
		/**
		 * @description Year, month and day notation string.
		 * @placeholder example The date example in year, month and day notation.
		 * @example Year, month and day (example: "2017-01-15")
		 */
		$strings[] = UText::localize("Year, month and day (example: {{example}})", self::class, $text_options, [
			'parameters' => ['example' => '2017-01-15']
		]);
		/**
		 * @description American month, day and year notation string.
		 * @placeholder example The date example in American month, day and year notation.
		 * @example American month, day and year (example: "1/15/17")
		 */
		$strings[] = UText::localize("American month, day and year (example: {{example}})", self::class, $text_options, [
			'parameters' => ['example' => '1/15/17']
		]);
		/**
		 * @description Day, month and year in English notation string.
		 * @placeholder example The date example in day, month and year in English notation.
		 * @example Day, month and year in English (example: "15 January 2017")
		 */
		$strings[] = UText::localize("Day, month and year in English (example: {{example}})", self::class, $text_options, [
			'parameters' => ['example' => '15 January 2017']
		]);
		/**
		 * @description Relative time interval in English notation string.
		 * @placeholder examples The list of date examples in relative time interval in English notation.
		 * @example Relative time interval in English (examples: "today", "yesterday", "next Wednesday", "8 days ago")
		 */
		$strings[] = UText::localize("Relative time interval in English (examples: {{examples}})", self::class, $text_options, [
			'parameters' => ['examples' => UText::stringify(['today', 'yesterday', 'next Wednesday', '8 days ago'], $example_text_options)]
		]);
		if ($text_options->info_scope !== EInfoScope::ENDUSER) {
			/**
			 * @description Fixed date with time interval in English notation string.
			 * @placeholder examples The list of date examples in fixed date with time interval in English notation.
			 * @tags non-end-user
			 * @example Fixed date with time interval in English (examples: "2017-01-15 +5 days", "1/15/17 -3 weeks")
			 */
			$strings[] = UText::localize("Fixed date with time interval in English (examples: {{examples}})", self::class, $text_options, [
				'parameters' => ['examples' => UText::stringify(['2017-01-15 +5 days', '1/15/17 -3 weeks'], $example_text_options)]
			]);
		}
		
		//return
		return $strings;
	}
}
