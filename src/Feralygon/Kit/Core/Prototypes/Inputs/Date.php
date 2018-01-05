<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Prototypes\Inputs;

use Feralygon\Kit\Core\Prototypes\Input;
use Feralygon\Kit\Core\Prototypes\Input\Interfaces\{
	Information as IInformation,
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
class Date extends Input implements IInformation, IModifiers
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
		/**
		 * @description Core date input prototype label.
		 * @tags core prototype input date label
		 */
		return UText::localize("Date", 'core.prototypes.inputs.date', $text_options);
	}
	
	/** {@inheritdoc} */
	public function getDescription(TextOptions $text_options, InfoOptions $info_options) : string
	{
		/**
		 * @description Core date input prototype description.
		 * @placeholder notations The supported date notation entries.
		 * @tags core prototype input date description
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
			'core.prototypes.inputs.date', $text_options, [
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
		 * @description Core date input prototype message.
		 * @placeholder notations The supported date notation entries.
		 * @tags core prototype input date message
		 * @example The given value must be a date, which may be given using any of the following notations:
		 *  &#8226; Unix timestamp (example: 1484438400);
		 *  &#8226; ISO 8601 (example: "2017-01-15");
		 *  &#8226; Year, month and day (example: "2017-01-15");
		 *  &#8226; American month, day and year (example: "1/15/17");
		 *  &#8226; Day, month and year in English (example: "15 January 2017");
		 *  &#8226; Relative time interval in English (examples: "today", "yesterday", "next Wednesday", "8 days ago");
		 *  &#8226; Fixed date with time interval in English (examples: "2017-01-15 +5 days", "1/15/17 -3 weeks").
		 */
		return UText::localize(
			"The given value must be a date, which may be given using any of the following notations:\n{{notations}}", 
			'core.prototypes.inputs.date', $text_options, [
				'parameters' => [
					'notations' => UText::mbulletify($this->getNotationStrings($text_options), $text_options, ['merge' => true, 'punctuate' => true])
				]
			]
		);
	}
	
	
	
	//Implemented public methods (core input prototype modifiers interface)
	/** {@inheritdoc} */
	public function buildModifier(string $name, array $properties = []) : ?Modifier
	{
		switch ($name) {
			case 'constraints.values':
				return $this->createConstraint(Constraints\Values::class, [], $properties);
			case 'constraints.non_values':
				return $this->createConstraint(Constraints\Values::class, [], ['negate' => true] + $properties);
			case 'constraints.minimum':
				return $this->createConstraint(Constraints\Minimum::class, [], $properties);
			case 'constraints.maximum':
				return $this->createConstraint(Constraints\Maximum::class, [], $properties);
			case 'constraints.range':
				return $this->createConstraint(Constraints\Range::class, [], $properties);
			case 'constraints.non_range':
				return $this->createConstraint(Constraints\Range::class, [], ['negate' => true] + $properties);
			case 'filters.format':
				return $this->createFilter(Filters\Format::class, [], $properties);
			case 'filters.iso8601':
				return $this->createFilter(Filters\Iso8601::class, [], $properties);
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
			 * @description Core date input prototype Unix timestamp notation string.
			 * @placeholder example The date example in Unix timestamp notation.
			 * @tags core prototype input date notation string non-end-user
			 * @example Unix timestamp (example: 1484438400)
			 */
			$strings[] = UText::localize("Unix timestamp (example: {{example}})", 'core.prototypes.inputs.date', $text_options, [
				'parameters' => ['example' => 1484438400]
			]);
			/**
			 * @description Core date input prototype ISO 8601 notation string.
			 * @placeholder example The date example in ISO 8601 notation.
			 * @tags core prototype input date notation string non-end-user
			 * @example ISO 8601 (example: "2017-01-15")
			 */
			$strings[] = UText::localize("ISO 8601 (example: {{example}})", 'core.prototypes.inputs.date', $text_options, [
				'parameters' => ['example' => '2017-01-15']
			]);
		}
		/**
		 * @description Core date input prototype year, month and day notation string.
		 * @placeholder example The date example in year, month and day notation.
		 * @tags core prototype input date notation string
		 * @example Year, month and day (example: "2017-01-15")
		 */
		$strings[] = UText::localize("Year, month and day (example: {{example}})", 'core.prototypes.inputs.date', $text_options, [
			'parameters' => ['example' => '2017-01-15']
		]);
		/**
		 * @description Core date input prototype American month, day and year notation string.
		 * @placeholder example The date example in American month, day and year notation.
		 * @tags core prototype input date notation string
		 * @example American month, day and year (example: "1/15/17")
		 */
		$strings[] = UText::localize("American month, day and year (example: {{example}})", 'core.prototypes.inputs.date', $text_options, [
			'parameters' => ['example' => '1/15/17']
		]);
		/**
		 * @description Core date input prototype day, month and year in English notation string.
		 * @placeholder example The date example in day, month and year in English notation.
		 * @tags core prototype input date notation string
		 * @example Day, month and year in English (example: "15 January 2017")
		 */
		$strings[] = UText::localize("Day, month and year in English (example: {{example}})", 'core.prototypes.inputs.date', $text_options, [
			'parameters' => ['example' => '15 January 2017']
		]);
		/**
		 * @description Core date input prototype relative time interval in English notation string.
		 * @placeholder examples The list of date examples in relative time interval in English notation.
		 * @tags core prototype input date notation string
		 * @example Relative time interval in English (examples: "today", "yesterday", "next Wednesday", "8 days ago")
		 */
		$strings[] = UText::localize("Relative time interval in English (examples: {{examples}})", 'core.prototypes.inputs.date', $text_options, [
			'parameters' => ['examples' => UText::stringify(['today', 'yesterday', 'next Wednesday', '8 days ago'], $example_text_options)]
		]);
		if ($text_options->info_scope !== EInfoScope::ENDUSER) {
			/**
			 * @description Core date input prototype fixed date with time interval in English notation string.
			 * @placeholder examples The list of date examples in fixed date with time interval in English notation.
			 * @tags core prototype input date notation string non-end-user
			 * @example Fixed date with time interval in English (examples: "2017-01-15 +5 days", "1/15/17 -3 weeks")
			 */
			$strings[] = UText::localize("Fixed date with time interval in English (examples: {{examples}})", 'core.prototypes.inputs.date', $text_options, [
				'parameters' => ['examples' => UText::stringify(['2017-01-15 +5 days', '1/15/17 -3 weeks'], $example_text_options)]
			]);
		}
		
		//return
		return $strings;
	}
}
