<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Prototypes\Inputs\Numbers;

use Feralygon\Kit\Prototypes\Inputs\Number;
use Feralygon\Kit\Prototypes\Input\Interfaces\ValueStringification as IValueStringification;
use Feralygon\Kit\Components\Input\Components\Modifier;
use Feralygon\Kit\Prototypes\Inputs\Numbers\Size\Prototypes\Modifiers\Constraints;
use Feralygon\Kit\Options\Text as TextOptions;
use Feralygon\Kit\Components\Input\Options\Info as InfoOptions;
use Feralygon\Kit\Enumerations\InfoScope as EInfoScope;
use Feralygon\Kit\Utilities\{
	Byte as UByte,
	Text as UText
};

/**
 * This input prototype represents a size in bytes.
 * 
 * Only the following types of values may be evaluated as a size in bytes:<br>
 * &nbsp; &#8226; &nbsp; an integer or float;<br>
 * &nbsp; &#8226; &nbsp; a numeric string, such as <code>"1000"</code>;<br>
 * &nbsp; &#8226; &nbsp; a numeric string in exponential notation, 
 * such as <code>"1e3"</code> or <code>"1E3"</code>;<br>
 * &nbsp; &#8226; &nbsp; a numeric string in octal notation, such as <code>"01750"</code>;<br>
 * &nbsp; &#8226; &nbsp; a numeric string in hexadecimal notation, 
 * such as <code>"0x03e8"</code> or <code>"0x03E8"</code>;<br>
 * &nbsp; &#8226; &nbsp; a human-readable numeric string in English, 
 * such as <code>"1 thousand"</code> or <code>"1k"</code>;<br>
 * &nbsp; &#8226; &nbsp; a human-readable numeric string in bytes, 
 * such as <code>"1 kilobyte"</code> or <code>"1 kB"</code>.
 * 
 * @since 1.0.0
 * @see https://en.wikipedia.org/wiki/Byte
 * @see https://en.wikipedia.org/wiki/File_size
 * @see \Feralygon\Kit\Prototypes\Inputs\Numbers\Size\Prototypes\Modifiers\Constraints\Values
 * [modifier, name = 'constraints.values' or 'values' or 'constraints.non_values' or 'non_values']
 * @see \Feralygon\Kit\Prototypes\Inputs\Numbers\Size\Prototypes\Modifiers\Constraints\Minimum
 * [modifier, name = 'constraints.minimum' or 'minimum' or 'constraints.positive' or 'positive']
 * @see \Feralygon\Kit\Prototypes\Inputs\Numbers\Size\Prototypes\Modifiers\Constraints\Maximum
 * [modifier, name = 'constraints.maximum' or 'maximum' or 'constraints.negative' or 'negative']
 * @see \Feralygon\Kit\Prototypes\Inputs\Numbers\Size\Prototypes\Modifiers\Constraints\Range
 * [modifier, name = 'constraints.range' or 'range' or 'constraints.non_range' or 'non_range']
 */
class Size extends Number implements IValueStringification
{
	//Implemented public methods (Feralygon\Kit\Prototypes\Input\Interfaces\ValueStringification)
	/** {@inheritdoc} */
	public function stringifyValue($value, TextOptions $text_options) : string
	{
		return UByte::hvalue($value);
	}
	
	
	
	//Overridden public methods
	/** {@inheritdoc} */
	public function getName() : string
	{
		return 'size';
	}
	
	/** {@inheritdoc} */
	public function evaluateValue(&$value) : bool
	{
		return UByte::evaluateSize($value);
	}
	
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options, InfoOptions $info_options) : string
	{
		return UText::localize("Size", self::class, $text_options);
	}
	
	/** {@inheritdoc} */
	public function getDescription(TextOptions $text_options, InfoOptions $info_options) : string
	{
		//end-user
		if ($text_options->info_scope === EInfoScope::ENDUSER) {
			/**
			 * @placeholder examples The list of size examples.
			 * @tags end-user
			 * @example A size in bytes (examples: "15 bytes", "1kB", "2.2 megabytes").
			 */
			return UText::localize("A size in bytes (examples: {{examples}}).", self::class, $text_options, [
				'parameters' => ['examples' => ['15 bytes', '1kB', '2.2 megabytes']],
				'string_options' => [
					'quote_strings' => true,
					'non_assoc_mode' => UText::STRING_NONASSOC_MODE_COMMA_LIST
				]
			]);
		}
		
		//non-end-user
		/**
		 * @placeholder notations The supported size notation entries.
		 * @tags non-end-user
		 * @example A size in bytes, which may be given using any of the following notations:
		 *  &#8226; Standard (examples: "15", "1000", "2200000");
		 *  &#8226; Exponential string (examples: "15e0", "1e3", "2.2E6");
		 *  &#8226; Octal string (examples: "017", "01750", "010310700");
		 *  &#8226; Hexadecimal string (examples: "0x0f", "0x03e8", "0x2191C0");
		 *  &#8226; Human-readable string in English (examples: "15", "1k", "2.2 million");
		 *  &#8226; Human-readable numeric string in bytes (examples: "15 bytes", "1kB", "2.2 megabytes").
		 */
		return UText::localize(
			"A size in bytes, which may be given using any of the following notations:\n{{notations}}", 
			self::class, $text_options, [
				'parameters' => [
					'notations' => UText::mbulletify(
						$this->getNotationStrings($text_options), $text_options, [
							'merge' => true,
							'punctuate' => true
						]
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
			/**
			 * @placeholder examples The list of size examples.
			 * @tags end-user
			 * @example Only a size in bytes is allowed (examples: "15 bytes", "1kB", "2.2 megabytes").
			 */
			return UText::localize(
				"Only a size in bytes is allowed (examples: {{examples}}).",
				self::class, $text_options, [
					'parameters' => ['examples' => ['15 bytes', '1kB', '2.2 megabytes']],
					'string_options' => [
						'quote_strings' => true,
						'non_assoc_mode' => UText::STRING_NONASSOC_MODE_COMMA_LIST
					]
				]
			);
		}
		
		//non-end-user
		/**
		 * @placeholder notations The supported size notation entries.
		 * @tags non-end-user
		 * @example Only a size in bytes is allowed, which may be given using any of the following notations:
		 *  &#8226; Standard (examples: "15", "1000", "2200000");
		 *  &#8226; Exponential string (examples: "15e0", "1e3", "2.2E6");
		 *  &#8226; Octal string (examples: "017", "01750", "010310700");
		 *  &#8226; Hexadecimal string (examples: "0x0f", "0x03e8", "0x2191C0");
		 *  &#8226; Human-readable string in English (examples: "15", "1k", "2.2 million");
		 *  &#8226; Human-readable numeric string in bytes (examples: "15 bytes", "1kB", "2.2 megabytes").
		 */
		return UText::localize(
			"Only a size in bytes is allowed, which may be given using any of the following notations:\n{{notations}}", 
			self::class, $text_options, [
				'parameters' => [
					'notations' => UText::mbulletify(
						$this->getNotationStrings($text_options), $text_options, [
							'merge' => true,
							'punctuate' => true
						]
					)
				]
			]
		);
	}
	
	/** {@inheritdoc} */
	public function buildModifier(string $name, array $properties = []) : ?Modifier
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
			case 'constraints.positive':
				//no break
			case 'positive':
				return $this->createConstraint(
					Constraints\Minimum::class, ['value' => 0, 'exclusive' => true] + $properties
				);
			case 'constraints.maximum':
				//no break
			case 'maximum':
				return $this->createConstraint(Constraints\Maximum::class, $properties);
			case 'constraints.negative':
				//no break
			case 'negative':
				return $this->createConstraint(
					Constraints\Maximum::class, ['value' => 0, 'exclusive' => true] + $properties
				);
			case 'constraints.range':
				//no break
			case 'range':
				return $this->createConstraint(Constraints\Range::class, $properties);
			case 'constraints.non_range':
				//no break
			case 'non_range':
				return $this->createConstraint(Constraints\Range::class, ['negate' => true] + $properties);
		}
		return parent::buildModifier($name, $properties);
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function getNotationStrings(TextOptions $text_options) : array
	{
		$strings = [];
		if ($text_options->info_scope !== EInfoScope::ENDUSER) {
			//standard
			/**
			 * @description Standard notation string.
			 * @placeholder examples The list of size examples in standard notation.
			 * @tags non-end-user
			 * @example Standard (examples: "15", "1000", "2200000")
			 */
			$strings[] = UText::localize(
				"Standard (examples: {{examples}})",
				self::class, $text_options, [
					'parameters' => ['examples' => ['15', '1000', '2200000']],
					'string_options' => [
						'quote_strings' => true,
						'non_assoc_mode' => UText::STRING_NONASSOC_MODE_COMMA_LIST
					]
				]
			);
			
			//exponential
			/**
			 * @description Exponential notation string.
			 * @placeholder examples The list of size examples in exponential notation.
			 * @tags non-end-user
			 * @example Exponential string (examples: "15e0", "1e3", "2.2E6")
			 */
			$strings[] = UText::localize(
				"Exponential string (examples: {{examples}})",
				self::class, $text_options, [
					'parameters' => ['examples' => ['15e0', '1e3', '2.2E6']],
					'string_options' => [
						'quote_strings' => true,
						'non_assoc_mode' => UText::STRING_NONASSOC_MODE_COMMA_LIST
					]
				]
			);
			
			//octal
			/**
			 * @description Octal notation string.
			 * @placeholder examples The list of size examples in octal notation.
			 * @tags non-end-user
			 * @example Octal string (examples: "017", "01750", "010310700")
			 */
			$strings[] = UText::localize(
				"Octal string (examples: {{examples}})",
				self::class, $text_options, [
					'parameters' => ['examples' => ['017', '01750', '010310700']],
					'string_options' => [
						'quote_strings' => true,
						'non_assoc_mode' => UText::STRING_NONASSOC_MODE_COMMA_LIST
					]
				]
			);
			
			//hexadecimal
			/**
			 * @description Hexadecimal notation string.
			 * @placeholder examples The list of size examples in hexadecimal notation.
			 * @tags non-end-user
			 * @example Hexadecimal string (examples: "0x0f", "0x03e8", "0x2191C0")
			 */
			$strings[] = UText::localize(
				"Hexadecimal string (examples: {{examples}})",
				self::class, $text_options, [
					'parameters' => ['examples' => ['0x0f', '0x03e8', '0x2191C0']],
					'string_options' => [
						'quote_strings' => true,
						'non_assoc_mode' => UText::STRING_NONASSOC_MODE_COMMA_LIST
					]
				]
			);
			
			//human-readable
			/**
			 * @description Human-readable notation string.
			 * @placeholder examples The list of size examples in human-readable notation.
			 * @tags non-end-user
			 * @example Human-readable string in English (examples: "15", "1k", "2.2 million")
			 */
			$strings[] = UText::localize(
				"Human-readable string in English (examples: {{examples}})",
				self::class, $text_options, [
					'parameters' => ['examples' => ['15', '1k', '2.2 million']],
					'string_options' => [
						'quote_strings' => true,
						'non_assoc_mode' => UText::STRING_NONASSOC_MODE_COMMA_LIST
					]
				]
			);
			
			//human-readable (bytes)
			/**
			 * @description Human-readable bytes notation string.
			 * @placeholder examples The list of size examples in human-readable bytes notation.
			 * @tags non-end-user
			 * @example Human-readable numeric string in bytes (examples: "15 bytes", "1kB", "2.2 megabytes")
			 */
			$strings[] = UText::localize(
				"Human-readable numeric string in bytes (examples: {{examples}})",
				self::class, $text_options, [
					'parameters' => ['examples' => ['15 bytes', '1kB', '2.2 megabytes']],
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
