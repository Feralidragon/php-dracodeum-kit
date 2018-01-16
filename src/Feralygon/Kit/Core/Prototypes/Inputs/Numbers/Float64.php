<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Prototypes\Inputs\Numbers;

use Feralygon\Kit\Core\Prototypes\Inputs\Number;
use Feralygon\Kit\Core\Options\Text as TextOptions;
use Feralygon\Kit\Core\Components\Input\Options\Info as InfoOptions;
use Feralygon\Kit\Core\Enumerations\InfoScope as EInfoScope;
use Feralygon\Kit\Core\Utilities\{
	Text as UText,
	Type as UType
};

/**
 * Core float64 number input prototype class.
 * 
 * This input prototype represents a floating point number of 64 bits, for which only the following types of values may be evaluated as such:<br>
 * &nbsp; &#8226; &nbsp; an integer or float;<br>
 * &nbsp; &#8226; &nbsp; a numeric string, such as <code>"1000"</code>;<br>
 * &nbsp; &#8226; &nbsp; a numeric string in exponential notation, such as <code>"1e3"</code> or <code>"1E3"</code>;<br>
 * &nbsp; &#8226; &nbsp; a human-readable numeric string in English, such as <code>"1 thousand"</code> or <code>"1k"</code>;<br>
 * &nbsp; &#8226; &nbsp; a human-readable numeric string in bytes, such as <code>"1 kilobyte"</code> or <code>"1 kB"</code>.
 * 
 * @since 1.0.0
 * @see https://en.wikipedia.org/wiki/IEEE_floating_point
 */
class Float64 extends Number
{
	//Overridden public methods
	/** {@inheritdoc} */
	public function getName() : string
	{
		return 'float64';
	}
	
	/** {@inheritdoc} */
	public function evaluateValue(&$value) : bool
	{
		return UType::evaluateFloat($value);
	}
	
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options, InfoOptions $info_options) : string
	{
		if ($text_options->info_scope === EInfoScope::ENDUSER) {
			/** @tags end-user */
			return UText::localize("Real number", self::class, $text_options);
		} elseif ($text_options->info_scope === EInfoScope::TECHNICAL) {
			/** @tags technical */
			return UText::localize("Float", self::class, $text_options);
		}
		/** @tags non-end-user non-technical */
		return UText::localize("Float (64 bits)", self::class, $text_options);
	}
	
	/** {@inheritdoc} */
	public function getDescription(TextOptions $text_options, InfoOptions $info_options) : string
	{
		//end-user
		if ($text_options->info_scope === EInfoScope::ENDUSER) {
			/** @tags end-user */
			return UText::localize("A real number.", self::class, $text_options);
		}
		
		//non-end-user
		/**
		 * @placeholder notations The supported float64 number notation entries.
		 * @tags non-end-user
		 * @example A floating point IEEE 754 number of 64 bits, which may be given using any of the following notations:
		 *  &#8226; Standard (examples: "1000", "45.75", "-9553.5");
		 *  &#8226; Exponential string (examples: "1e3", "4575E-2", "-9.5535e3");
		 *  &#8226; Human-readable string in English (examples: "1 thousand", "0.04575k", "-9.5535 k").
		 */
		return UText::localize(
			"A floating point IEEE 754 number of 64 bits, which may be given using any of the following notations:\n{{notations}}", 
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
		//end-user
		if ($text_options->info_scope === EInfoScope::ENDUSER) {
			/** @tags end-user */
			return UText::localize("Only real numbers are allowed.", self::class, $text_options);
		}
		
		//non-end-user
		/**
		 * @placeholder notations The supported float64 number notation entries.
		 * @tags non-end-user
		 * @example Only floating point IEEE 754 numbers of 64 bits are allowed, which may be given using any of the following notations:
		 *  &#8226; Standard (examples: "1000", "45.75", "-9553.5");
		 *  &#8226; Exponential string (examples: "1e3", "4575E-2", "-9.5535e3");
		 *  &#8226; Human-readable string in English (examples: "1 thousand", "0.04575k", "-9.5535 k").
		 */
		return UText::localize(
			"Only floating point IEEE 754 numbers of 64 bits are allowed, which may be given using any of the following notations:\n{{notations}}", 
			self::class, $text_options, [
				'parameters' => [
					'notations' => UText::mbulletify($this->getNotationStrings($text_options), $text_options, ['merge' => true, 'punctuate' => true])
				]
			]
		);
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function getNotationStrings(TextOptions $text_options) : array
	{
		//initialize
		$strings = [];
		$example_text_options = TextOptions::load($text_options, true);
		$example_text_options->info_scope = EInfoScope::ENDUSER;
		
		//strings
		if ($text_options->info_scope !== EInfoScope::ENDUSER) {
			/**
			 * @description Standard notation string.
			 * @placeholder examples The list of float64 number examples in standard notation.
			 * @tags non-end-user
			 * @example Standard (examples: "1000", "45.75", "-9553.5")
			 */
			$strings[] = UText::localize("Standard (examples: {{examples}})", self::class, $text_options, [
				'parameters' => ['examples' => UText::stringify(['1000', '45.75', '-9553.5'], $example_text_options)]
			]);
			/**
			 * @description Exponential notation string.
			 * @placeholder examples The list of float64 number examples in exponential notation.
			 * @tags non-end-user
			 * @example Exponential string (examples: "1e3", "4575E-2", "-9.5535e3")
			 */
			$strings[] = UText::localize("Exponential string (examples: {{examples}})", self::class, $text_options, [
				'parameters' => ['examples' => UText::stringify(['1e3', '4575E-2', '-9.5535e3'], $example_text_options)]
			]);
			/**
			 * @description Human-readable notation string.
			 * @placeholder examples The list of float64 number examples in human-readable notation.
			 * @tags non-end-user
			 * @example Human-readable string in English (examples: "1 thousand", "0.04575k", "-9.5535 k")
			 */
			$strings[] = UText::localize("Human-readable string in English (examples: {{examples}})", self::class, $text_options, [
				'parameters' => ['examples' => UText::stringify(['1 thousand', '0.04575k', '-9.5535 k'], $example_text_options)]
			]);
		}
		
		//return
		return $strings;
	}
}
