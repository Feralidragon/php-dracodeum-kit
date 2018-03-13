<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Prototypes\Inputs;

use Feralygon\Kit\Prototypes\Input;
use Feralygon\Kit\Prototypes\Input\Interfaces\{
	Information as IInformation,
	Modifiers as IModifiers
};
use Feralygon\Kit\Components\Input\Components\Modifier;
use Feralygon\Kit\Prototypes\Inputs\Number\Prototypes\Modifiers\Constraints;
use Feralygon\Kit\Options\Text as TextOptions;
use Feralygon\Kit\Components\Input\Options\Info as InfoOptions;
use Feralygon\Kit\Enumerations\InfoScope as EInfoScope;
use Feralygon\Kit\Utilities\{
	Text as UText,
	Type as UType
};

/**
 * This input prototype represents a number.
 * 
 * Only the following types of values may be evaluated as a number:<br>
 * &nbsp; &#8226; &nbsp; an integer or float;<br>
 * &nbsp; &#8226; &nbsp; a numeric string, such as <code>"1000"</code>;<br>
 * &nbsp; &#8226; &nbsp; a numeric string in exponential notation, such as <code>"1e3"</code> or <code>"1E3"</code>;<br>
 * &nbsp; &#8226; &nbsp; a numeric string in octal notation, such as <code>"01750"</code>;<br>
 * &nbsp; &#8226; &nbsp; a numeric string in hexadecimal notation, 
 * such as <code>"0x03e8"</code> or <code>"0x03E8"</code>;<br>
 * &nbsp; &#8226; &nbsp; a human-readable numeric string in English, 
 * such as <code>"1 thousand"</code> or <code>"1k"</code>;<br>
 * &nbsp; &#8226; &nbsp; a human-readable numeric string in bytes, 
 * such as <code>"1 kilobyte"</code> or <code>"1 kB"</code>.
 * 
 * @since 1.0.0
 * @see https://en.wikipedia.org/wiki/Number
 * @see \Feralygon\Kit\Prototypes\Inputs\Number\Prototypes\Modifiers\Constraints\Values
 * [modifier, name = 'constraints.values' or 'constraints.non_values']
 * @see \Feralygon\Kit\Prototypes\Inputs\Number\Prototypes\Modifiers\Constraints\Minimum
 * [modifier, name = 'constraints.minimum' or 'constraints.positive']
 * @see \Feralygon\Kit\Prototypes\Inputs\Number\Prototypes\Modifiers\Constraints\Maximum
 * [modifier, name = 'constraints.maximum' or 'constraints.negative']
 * @see \Feralygon\Kit\Prototypes\Inputs\Number\Prototypes\Modifiers\Constraints\Range
 * [modifier, name = 'constraints.range' or 'constraints.non_range']
 * @see \Feralygon\Kit\Prototypes\Inputs\Number\Prototypes\Modifiers\Constraints\Multiples
 * [modifier, name = 'constraints.multiples' or 'constraints.non_multiples' or 'constraints.even' or 'constraints.odd']
 * @see \Feralygon\Kit\Prototypes\Inputs\Number\Prototypes\Modifiers\Constraints\Powers
 * [modifier, name = 'constraints.powers' or 'constraints.non_powers']
 */
class Number extends Input implements IInformation, IModifiers
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getName() : string
	{
		return 'number';
	}
	
	/** {@inheritdoc} */
	public function evaluateValue(&$value) : bool
	{
		return UType::evaluateNumber($value);
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Prototypes\Input\Interfaces\Information)
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options, InfoOptions $info_options) : string
	{
		return UText::localize("Number", self::class, $text_options);
	}
	
	/** {@inheritdoc} */
	public function getDescription(TextOptions $text_options, InfoOptions $info_options) : string
	{
		//end-user
		if ($text_options->info_scope === EInfoScope::ENDUSER) {
			/** @tags end-user */
			return UText::localize("A number.", self::class, $text_options);
		}
		
		//non-end-user
		/**
		 * @placeholder notations The supported number notation entries.
		 * @tags non-end-user
		 * @example A number, which may be given using any of the following notations:
		 *  &#8226; Standard (examples: "1000", "45.75", "-9553.5");
		 *  &#8226; Exponential string (examples: "1e3", "4575E-2", "-9.5535e3");
		 *  &#8226; Octal string (examples: "01750", "055", "022521");
		 *  &#8226; Hexadecimal string (examples: "0x03e8", "0x2D", "0x2551");
		 *  &#8226; Human-readable string in English (examples: "1 thousand", "0.04575k", "-9.5535 k").
		 */
		return UText::localize(
			"A number, which may be given using any of the following notations:\n{{notations}}", 
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
	public function getMessage(TextOptions $text_options, InfoOptions $info_options) : string
	{
		//end-user
		if ($text_options->info_scope === EInfoScope::ENDUSER) {
			/** @tags end-user */
			return UText::localize("Only a number is allowed.", self::class, $text_options);
		}
		
		//non-end-user
		/**
		 * @placeholder notations The supported number notation entries.
		 * @tags non-end-user
		 * @example Only a number is allowed, which may be given using any of the following notations:
		 *  &#8226; Standard (examples: "1000", "45.75", "-9553.5");
		 *  &#8226; Exponential string (examples: "1e3", "4575E-2", "-9.5535e3");
		 *  &#8226; Octal string (examples: "01750", "055", "022521");
		 *  &#8226; Hexadecimal string (examples: "0x03e8", "0x2D", "0x2551");
		 *  &#8226; Human-readable string in English (examples: "1 thousand", "0.04575k", "-9.5535 k").
		 */
		return UText::localize(
			"Only a number is allowed, which may be given using any of the following notations:\n{{notations}}", 
			self::class, $text_options, [
				'parameters' => [
					'notations' => UText::mbulletify(
						$this->getNotationStrings($text_options), $text_options, ['merge' => true, 'punctuate' => true]
					)
				]
			]
		);
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Prototypes\Input\Interfaces\Modifiers)
	/** {@inheritdoc} */
	public function buildModifier(string $name, array $properties = []) : ?Modifier
	{
		switch ($name) {
			//constraints
			case 'constraints.values':
				return $this->createConstraint(Constraints\Values::class, $properties);
			case 'constraints.non_values':
				return $this->createConstraint(Constraints\Values::class, ['negate' => true] + $properties);
			case 'constraints.minimum':
				return $this->createConstraint(Constraints\Minimum::class, $properties);
			case 'constraints.positive':
				return $this->createConstraint(
					Constraints\Minimum::class, ['value' => 0, 'exclusive' => true] + $properties
				);
			case 'constraints.maximum':
				return $this->createConstraint(Constraints\Maximum::class, $properties);
			case 'constraints.negative':
				return $this->createConstraint(
					Constraints\Maximum::class, ['value' => 0, 'exclusive' => true] + $properties
				);
			case 'constraints.range':
				return $this->createConstraint(Constraints\Range::class, $properties);
			case 'constraints.non_range':
				return $this->createConstraint(Constraints\Range::class, ['negate' => true] + $properties);
			case 'constraints.multiples':
				return $this->createConstraint(Constraints\Multiples::class, $properties);
			case 'constraints.non_multiples':
				return $this->createConstraint(Constraints\Multiples::class, ['negate' => true] + $properties);
			case 'constraints.even':
				return $this->createConstraint(Constraints\Multiples::class, ['multiples' => [2]] + $properties);
			case 'constraints.odd':
				return $this->createConstraint(
					Constraints\Multiples::class, ['multiples' => [2], 'negate' => true] + $properties
				);
			case 'constraints.powers':
				return $this->createConstraint(Constraints\Powers::class, $properties);
			case 'constraints.non_powers':
				return $this->createConstraint(Constraints\Powers::class, ['negate' => true] + $properties);
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
	protected function getNotationStrings(TextOptions $text_options) : array
	{
		$strings = [];
		if ($text_options->info_scope !== EInfoScope::ENDUSER) {
			//standard
			/**
			 * @description Standard notation string.
			 * @placeholder examples The list of number examples in standard notation.
			 * @tags non-end-user
			 * @example Standard (examples: "1000", "45.75", "-9553.5")
			 */
			$strings[] = UText::localize(
				"Standard (examples: {{examples}})",
				self::class, $text_options, [
					'parameters' => ['examples' => ['1000', '45.75', '-9553.5']],
					'string_options' => [
						'quote_strings' => true,
						'non_assoc_mode' => UText::STRING_NONASSOC_MODE_COMMA_LIST
					]
				]
			);
			
			//exponential
			/**
			 * @description Exponential notation string.
			 * @placeholder examples The list of number examples in exponential notation.
			 * @tags non-end-user
			 * @example Exponential string (examples: "1e3", "4575E-2", "-9.5535e3")
			 */
			$strings[] = UText::localize(
				"Exponential string (examples: {{examples}})",
				self::class, $text_options, [
					'parameters' => ['examples' => ['1e3', '4575E-2', '-9.5535e3']],
					'string_options' => [
						'quote_strings' => true,
						'non_assoc_mode' => UText::STRING_NONASSOC_MODE_COMMA_LIST
					]
				]
			);
			
			//octal
			/**
			 * @description Octal notation string.
			 * @placeholder examples The list of number examples in octal notation.
			 * @tags non-end-user
			 * @example Octal string (examples: "01750", "055", "022521")
			 */
			$strings[] = UText::localize(
				"Octal string (examples: {{examples}})",
				self::class, $text_options, [
					'parameters' => ['examples' => ['01750', '055', '022521']],
					'string_options' => [
						'quote_strings' => true,
						'non_assoc_mode' => UText::STRING_NONASSOC_MODE_COMMA_LIST
					]
				]
			);
			
			//hexadecimal
			/**
			 * @description Hexadecimal notation string.
			 * @placeholder examples The list of number examples in hexadecimal notation.
			 * @tags non-end-user
			 * @example Hexadecimal string (examples: "0x03e8", "0x2D", "0x2551")
			 */
			$strings[] = UText::localize(
				"Hexadecimal string (examples: {{examples}})",
				self::class, $text_options, [
					'parameters' => ['examples' => ['0x03e8', '0x2D', '0x2551']],
					'string_options' => [
						'quote_strings' => true,
						'non_assoc_mode' => UText::STRING_NONASSOC_MODE_COMMA_LIST
					]
				]
			);
			
			//human-readable
			/**
			 * @description Human-readable notation string.
			 * @placeholder examples The list of number examples in human-readable notation.
			 * @tags non-end-user
			 * @example Human-readable string in English (examples: "1 thousand", "0.04575k", "-9.5535 k")
			 */
			$strings[] = UText::localize(
				"Human-readable string in English (examples: {{examples}})",
				self::class, $text_options, [
					'parameters' => ['examples' => ['1 thousand', '0.04575k', '-9.5535 k']],
					'string_options' => [
						'quote_strings' => true,
						'non_assoc_mode' => UText::STRING_NONASSOC_MODE_COMMA_LIST
					]
				]
			);
		}
		return $strings;
	}
}
