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
use Feralygon\Kit\Core\Prototypes\Inputs\Number\Prototypes\Modifiers\Constraints;
use Feralygon\Kit\Core\Options\Text as TextOptions;
use Feralygon\Kit\Core\Components\Input\Options\Info as InfoOptions;
use Feralygon\Kit\Core\Enumerations\InfoScope as EInfoScope;
use Feralygon\Kit\Core\Utilities\{
	Text as UText,
	Type as UType
};

/**
 * Core number input prototype class.
 * 
 * This input prototype represents a number, for which only the following types of values may be evaluated as such:<br>
 * &nbsp; &#8226; &nbsp; an integer or float;<br>
 * &nbsp; &#8226; &nbsp; a numeric string, such as <code>"1000"</code>;<br>
 * &nbsp; &#8226; &nbsp; a numeric string in exponential notation, such as <code>"1e3"</code> or <code>"1E3"</code>;<br>
 * &nbsp; &#8226; &nbsp; a numeric string in octal notation, such as <code>"01750"</code>;<br>
 * &nbsp; &#8226; &nbsp; a numeric string in hexadecimal notation, such as <code>"0x03e8"</code> or <code>"0x03E8"</code>;<br>
 * &nbsp; &#8226; &nbsp; a human-readable numeric string in English, such as <code>"1 thousand"</code> or <code>"1k"</code>;<br>
 * &nbsp; &#8226; &nbsp; a human-readable numeric string in bytes, such as <code>"1 kilobyte"</code> or <code>"1 kB"</code>.
 * 
 * @since 1.0.0
 * @see https://en.wikipedia.org/wiki/Number
 * @see \Feralygon\Kit\Core\Prototypes\Inputs\Number\Prototypes\Modifiers\Constraints\Values [modifier, name = 'constraints.values' or 'constraints.non_values']
 * @see \Feralygon\Kit\Core\Prototypes\Inputs\Number\Prototypes\Modifiers\Constraints\Minimum [modifier, name = 'constraints.minimum' or 'constraints.positive']
 * @see \Feralygon\Kit\Core\Prototypes\Inputs\Number\Prototypes\Modifiers\Constraints\Maximum [modifier, name = 'constraints.maximum' or 'constraints.negative']
 * @see \Feralygon\Kit\Core\Prototypes\Inputs\Number\Prototypes\Modifiers\Constraints\Range [modifier, name = 'constraints.range' or 'constraints.non_range']
 * @see \Feralygon\Kit\Core\Prototypes\Inputs\Number\Prototypes\Modifiers\Constraints\Multiples [modifier, name = 'constraints.multiples' or 'constraints.non_multiples' or 'constraints.even' or 'constraints.odd']
 * @see \Feralygon\Kit\Core\Prototypes\Inputs\Number\Prototypes\Modifiers\Constraints\Powers [modifier, name = 'constraints.powers' or 'constraints.non_powers']
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
	
	
	
	//Implemented public methods (core input prototype information interface)
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options, InfoOptions $info_options) : string
	{
		/**
		 * @description Core number input prototype label.
		 * @tags core prototype input number label
		 */
		return UText::localize("Number", 'core.prototypes.inputs.number', $text_options);
	}
	
	/** {@inheritdoc} */
	public function getDescription(TextOptions $text_options, InfoOptions $info_options) : string
	{
		//end-user
		if ($text_options->info_scope === EInfoScope::ENDUSER) {
			/**
			 * @description Core number input prototype description (end-user).
			 * @tags core prototype input number description end-user
			 */
			return UText::localize("A number.", 'core.prototypes.inputs.number', $text_options);
		}
		
		//non-end-user
		/**
		 * @description Core number input prototype description.
		 * @placeholder notations The supported number notation entries.
		 * @tags core prototype input number description non-end-user
		 * @example A number, which may be given using any of the following notations:
		 *  &#8226; Standard (examples: "1000", "45.75", "-9553.5");
		 *  &#8226; Exponential string (examples: "1e3", "4575E-2", "-9.5535e3");
		 *  &#8226; Octal string (examples: "01750", "055", "022521");
		 *  &#8226; Hexadecimal string (examples: "0x03e8", "0x2D", "0x2551");
		 *  &#8226; Human-readable string in English (examples: "1 thousand", "0.04575k", "-9.5535 k").
		 */
		return UText::localize(
			"A number, which may be given using any of the following notations:\n{{notations}}", 
			'core.prototypes.inputs.number', $text_options, [
				'parameters' => [
					'notations' => UText::mbulletify($this->getNotationStrings($text_options), $text_options, ['merge' => true, 'punctuate' => true])
				]
			]
		);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options, InfoOptions $info_options) : string
	{
		//end-user
		if ($text_options->info_scope === EInfoScope::ENDUSER) {
			/**
			 * @description Core number input prototype message (end-user).
			 * @tags core prototype input number message end-user
			 */
			return UText::localize("The given value must be a number.", 'core.prototypes.inputs.number', $text_options);
		}
		
		//non-end-user
		/**
		 * @description Core number input prototype message.
		 * @placeholder notations The supported number notation entries.
		 * @tags core prototype input number message non-end-user
		 * @example The given value must be a number, which may be given using any of the following notations:
		 *  &#8226; Standard (examples: "1000", "45.75", "-9553.5");
		 *  &#8226; Exponential string (examples: "1e3", "4575E-2", "-9.5535e3");
		 *  &#8226; Octal string (examples: "01750", "055", "022521");
		 *  &#8226; Hexadecimal string (examples: "0x03e8", "0x2D", "0x2551");
		 *  &#8226; Human-readable string in English (examples: "1 thousand", "0.04575k", "-9.5535 k").
		 */
		return UText::localize(
			"The given value must be a number, which may be given using any of the following notations:\n{{notations}}", 
			'core.prototypes.inputs.number', $text_options, [
				'parameters' => [
					'notations' => UText::mbulletify($this->getNotationStrings($text_options), $text_options, ['merge' => true, 'punctuate' => true])
				]
			]
		);
	}
	
	
	
	//Implemented public methods (core input prototype modifiers interface)
	/** {@inheritdoc} */
	public function buildModifier(string $name, array $prototype_properties = [], array $properties = []) : ?Modifier
	{
		switch ($name) {
			case 'constraints.values':
				return $this->createConstraint(Constraints\Values::class, $prototype_properties, $properties);
			case 'constraints.non_values':
				return $this->createConstraint(Constraints\Values::class, ['negate' => true] + $prototype_properties, $properties);
			case 'constraints.minimum':
				return $this->createConstraint(Constraints\Minimum::class, $prototype_properties, $properties);
			case 'constraints.positive':
				return $this->createConstraint(Constraints\Minimum::class, ['value' => 0, 'exclusive' => true] + $prototype_properties, $properties);
			case 'constraints.maximum':
				return $this->createConstraint(Constraints\Maximum::class, $prototype_properties, $properties);
			case 'constraints.negative':
				return $this->createConstraint(Constraints\Maximum::class, ['value' => 0, 'exclusive' => true] + $prototype_properties, $properties);
			case 'constraints.range':
				return $this->createConstraint(Constraints\Range::class, $prototype_properties, $properties);
			case 'constraints.non_range':
				return $this->createConstraint(Constraints\Range::class, ['negate' => true] + $prototype_properties, $properties);
			case 'constraints.multiples':
				return $this->createConstraint(Constraints\Multiples::class, $prototype_properties, $properties);
			case 'constraints.non_multiples':
				return $this->createConstraint(Constraints\Multiples::class, ['negate' => true] + $prototype_properties, $properties);
			case 'constraints.even':
				return $this->createConstraint(Constraints\Multiples::class, ['multiples' => [2]] + $prototype_properties, $properties);
			case 'constraints.odd':
				return $this->createConstraint(Constraints\Multiples::class, ['multiples' => [2], 'negate' => true] + $prototype_properties, $properties);
			case 'constraints.powers':
				return $this->createConstraint(Constraints\Powers::class, $prototype_properties, $properties);
			case 'constraints.non_powers':
				return $this->createConstraint(Constraints\Powers::class, ['negate' => true] + $prototype_properties, $properties);
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
			 * @description Core number input prototype standard notation string.
			 * @placeholder examples The list of number examples in standard notation.
			 * @tags core prototype input number notation string non-end-user
			 * @example Standard (examples: "1000", "45.75", "-9553.5")
			 */
			$strings[] = UText::localize("Standard (examples: {{examples}})", 'core.prototypes.inputs.number', $text_options, [
				'parameters' => ['examples' => UText::stringify(['1000', '45.75', '-9553.5'], $example_text_options)]
			]);
			/**
			 * @description Core number input prototype exponential notation string.
			 * @placeholder examples The list of number examples in exponential notation.
			 * @tags core prototype input number notation string non-end-user
			 * @example Exponential string (examples: "1e3", "4575E-2", "-9.5535e3")
			 */
			$strings[] = UText::localize("Exponential string (examples: {{examples}})", 'core.prototypes.inputs.number', $text_options, [
				'parameters' => ['examples' => UText::stringify(['1e3', '4575E-2', '-9.5535e3'], $example_text_options)]
			]);
			/**
			 * @description Core number input prototype octal notation string.
			 * @placeholder examples The list of number examples in octal notation.
			 * @tags core prototype input number notation string non-end-user
			 * @example Octal string (examples: "01750", "055", "022521")
			 */
			$strings[] = UText::localize("Octal string (examples: {{examples}})", 'core.prototypes.inputs.number', $text_options, [
				'parameters' => ['examples' => UText::stringify(['01750', '055', '022521'], $example_text_options)]
			]);
			/**
			 * @description Core number input prototype hexadecimal notation string.
			 * @placeholder examples The list of number examples in hexadecimal notation.
			 * @tags core prototype input number notation string non-end-user
			 * @example Hexadecimal string (examples: "0x03e8", "0x2D", "0x2551")
			 */
			$strings[] = UText::localize("Hexadecimal string (examples: {{examples}})", 'core.prototypes.inputs.number', $text_options, [
				'parameters' => ['examples' => UText::stringify(['0x03e8', '0x2D', '0x2551'], $example_text_options)]
			]);
			/**
			 * @description Core number input prototype human-readable notation string.
			 * @placeholder examples The list of number examples in human-readable notation.
			 * @tags core prototype input number notation string non-end-user
			 * @example Human-readable string in English (examples: "1 thousand", "0.04575k", "-9.5535 k")
			 */
			$strings[] = UText::localize("Human-readable string in English (examples: {{examples}})", 'core.prototypes.inputs.number', $text_options, [
				'parameters' => ['examples' => UText::stringify(['1 thousand', '0.04575k', '-9.5535 k'], $example_text_options)]
			]);
		}
		
		//return
		return $strings;
	}
}
