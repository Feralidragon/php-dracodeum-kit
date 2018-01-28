<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Prototypes\Inputs\Numbers;

use Feralygon\Kit\Core\Prototypes\Inputs\Number;
use Feralygon\Kit\Core\Prototype\Interfaces\{
	Initialization as IPrototypeInitialization,
	Properties as IPrototypeProperties
};
use Feralygon\Kit\Core\Prototypes\Input\Interfaces\SchemaData as ISchemaData;
use Feralygon\Kit\Core\Prototypes\Inputs\Numbers\Integer\Exceptions;
use Feralygon\Kit\Core\Traits\ExtendedProperties\Objects\Property;
use Feralygon\Kit\Core\Options\Text as TextOptions;
use Feralygon\Kit\Core\Components\Input\Options\Info as InfoOptions;
use Feralygon\Kit\Core\Enumerations\InfoScope as EInfoScope;
use Feralygon\Kit\Core\Utilities\{
	Math as UMath,
	Text as UText,
	Type as UType
};

/**
 * Core integer number input prototype class.
 * 
 * This input prototype represents an integer number, 
 * for which only the following types of values may be evaluated as such:<br>
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
 * @property-read bool $unsigned [default = false] <p>Set as an unsigned integer.</p>
 * @property-read int|null $bits [default = null] <p>The number of bits to use.<br>
 * For signed integers, the maximum allowed number is <code>64</code>, 
 * while for unsigned integers this number is <code>63</code>.<br>
 * If not set, the number of bits to use becomes system dependent.</p>
 * @see https://en.wikipedia.org/wiki/Integer_(computer_science)
 */
class Integer extends Number implements IPrototypeInitialization, IPrototypeProperties, ISchemaData
{
	//Public constants
	/** Maximum supported number of bits (signed). */
	public const BITS_MAX_SIGNED = 64;
	
	/** Maximum supported number of bits (unsigned). */
	public const BITS_MAX_UNSIGNED = 63;
	
	
	
	//Private constants
	/** All supported bits fully on. */
	private const BITS_FULL = 0x7fffffffffffffff;
	
	
	
	//Private properties
	/** @var bool */
	private $unsigned = false;
	
	/** @var int|null */
	private $bits = null;
	
	/** @var int|null */
	private $minimum = null;
	
	/** @var int|null */
	private $maximum = null;
	
	
	
	//Implemented public methods (core prototype initialization interface)
	/**
	 * {@inheritdoc}
	 * @throws \Feralygon\Kit\Core\Prototypes\Inputs\Numbers\Integer\Exceptions\InvalidBits
	 */
	public function initialize() : void
	{
		if ($this->unsigned) {
			$this->minimum = 0;
			if (isset($this->bits)) {
				if ($this->bits > self::BITS_MAX_UNSIGNED) {
					throw new Exceptions\InvalidBits([
						'bits' => $this->bits,
						'max_bits' => self::BITS_MAX_UNSIGNED,
						'prototype' => $this,
						'unsigned' => true
					]);
				}
				$this->maximum = self::BITS_FULL >> (self::BITS_MAX_UNSIGNED - $this->bits);
			}
		} elseif (isset($this->bits)) {
			if ($this->bits > self::BITS_MAX_SIGNED) {
				throw new Exceptions\InvalidBits([
					'bits' => $this->bits,
					'max_bits' => self::BITS_MAX_SIGNED,
					'prototype' => $this
				]);
			}
			$this->maximum = self::BITS_FULL >> (self::BITS_MAX_SIGNED - $this->bits);
			$this->minimum = -$this->maximum - 1;
		}
	}
	
	
	
	//Implemented public methods (core prototype properties interface)
	/** {@inheritdoc} */
	public function buildProperty(string $name) : ?Property
	{
		switch ($name) {
			case 'unsigned':
				return $this->createProperty()
					->setMode('r')
					->setEvaluator(function (&$value) : bool {
						return UType::evaluateBoolean($value);
					})
					->setGetter(function () : bool {
						return $this->unsigned;
					})
					->setSetter(function (bool $unsigned) : void {
						$this->unsigned = $unsigned;
					})
				;
			case 'bits':
				return $this->createProperty()
					->setMode('r')
					->setEvaluator(function (&$value) : bool {
						return UType::evaluateInteger($value, true) && (!isset($value) || $value > 0);
					})
					->setGetter(function () : ?int {
						return $this->bits;
					})
					->setSetter(function (?int $bits) : void {
						$this->bits = $bits;
					})
				;
		}
		return null;
	}
	
	
	
	//Implemented public static methods (core prototype properties interface)
	/** {@inheritdoc} */
	public static function getRequiredPropertyNames() : array
	{
		return [];
	}
	
	
	
	//Implemented public methods (core input prototype schema data interface)
	/** {@inheritdoc} */
	public function getSchemaData()
	{
		return [
			'unsigned' => $this->unsigned,
			'bits' => $this->bits
		];
	}
	
	
	
	//Overridden public methods
	/** {@inheritdoc} */
	public function getName() : string
	{
		return 'integer';
	}
	
	/** {@inheritdoc} */
	public function evaluateValue(&$value) : bool
	{
		return UType::evaluateInteger($value) && 
			(!isset($this->minimum) || $value >= $this->minimum) && 
			(!isset($this->maximum) || $value <= $this->maximum);
	}
	
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options, InfoOptions $info_options) : string
	{
		//non-end-user
		if ($text_options->info_scope !== EInfoScope::ENDUSER) {
			if (isset($this->bits)) {
				if ($this->unsigned) {
					/**
					 * @placeholder bits The number of bits.
					 * @tags non-end-user
					 * @example Unsigned integer (32 bits)
					 */
					return UText::plocalize(
						"Unsigned integer ({{bits}} bit)", "Unsigned integer ({{bits}} bits)",
						$this->bits, 'bits', self::class, $text_options
					);
				}
				/**
				 * @placeholder bits The number of bits.
				 * @tags non-end-user
				 * @example Integer (32 bits)
				 */
				return UText::plocalize(
					"Integer ({{bits}} bit)", "Integer ({{bits}} bits)",
					$this->bits, 'bits', self::class, $text_options
				);
			} elseif ($this->unsigned) {
				/** @tags non-end-user */
				return UText::localize("Unsigned integer", self::class, $text_options);
			}
		}
		return UText::localize("Integer", self::class, $text_options);
	}
	
	/** {@inheritdoc} */
	public function getDescription(TextOptions $text_options, InfoOptions $info_options) : string
	{
		//end-user
		if ($text_options->info_scope === EInfoScope::ENDUSER) {
			if (isset($this->minimum) && isset($this->maximum)) {
				/**
				 * @placeholder minimum The minimum integer number.
				 * @placeholder maximum The maximum integer number.
				 * @tags end-user
				 * @example An integer number between -128 and 127.
				 */
				return UText::localize(
					"An integer number between {{minimum}} and {{maximum}}.",
					self::class, $text_options, [
						'parameters' => ['minimum' => $this->minimum, 'maximum' => $this->maximum]
					]
				);
			} elseif (isset($this->minimum)) {
				/**
				 * @placeholder minimum The minimum integer number.
				 * @tags end-user
				 * @example An integer number greater than or equal to -128.
				 */
				return UText::localize(
					"An integer number greater than or equal to {{minimum}}.",
					self::class, $text_options, [
						'parameters' => ['minimum' => $this->minimum]
					]
				);
			} elseif (isset($this->maximum)) {
				/**
				 * @placeholder maximum The maximum integer number.
				 * @tags end-user
				 * @example An integer number less than or equal to 127.
				 */
				return UText::localize(
					"An integer number less than or equal to {{maximum}}.",
					self::class, $text_options, [
						'parameters' => ['maximum' => $this->maximum]
					]
				);
			}
			/** @tags end-user */
			return UText::localize("An integer number.", self::class, $text_options);
		}
		
		//non-end-user
		if (isset($this->bits)) {
			if ($this->unsigned) {
				/**
				 * @placeholder bits The number of bits.
				 * @placeholder notations The supported integer number notation entries.
				 * @tags non-end-user
				 * @example An unsigned integer number of 32 bits, \
				 * which may be given using any of the following notations:
				 *  &#8226; Standard (examples: "64", "85", "125");
				 *  &#8226; Exponential string (examples: "6.4e1", "0.85e2", "1.25e2");
				 *  &#8226; Octal string (examples: "0100", "0125", "0175");
				 *  &#8226; Hexadecimal string (examples: "0x40", "0x55", "0x7d");
				 *  &#8226; Human-readable string in English (examples: "0.064 thousand", "0.085k", "0.125 k").
				 */
				return UText::plocalize(
					"An unsigned integer number of {{bits}} bit, " . 
						"which may be given using any of the following notations:\n{{notations}}", 
					"An unsigned integer number of {{bits}} bits, " . 
						"which may be given using any of the following notations:\n{{notations}}", 
					$this->bits, 'bits', self::class, $text_options, [
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
			/**
			 * @placeholder bits The number of bits.
			 * @placeholder notations The supported integer number notation entries.
			 * @tags non-end-user
			 * @example An integer number of 32 bits, which may be given using any of the following notations:
			 *  &#8226; Standard (examples: "64", "85", "125");
			 *  &#8226; Exponential string (examples: "6.4e1", "0.85e2", "1.25e2");
			 *  &#8226; Octal string (examples: "0100", "0125", "0175");
			 *  &#8226; Hexadecimal string (examples: "0x40", "0x55", "0x7d");
			 *  &#8226; Human-readable string in English (examples: "0.064 thousand", "0.085k", "0.125 k").
			 */
			return UText::plocalize(
				"An integer number of {{bits}} bit, " . 
					"which may be given using any of the following notations:\n{{notations}}", 
				"An integer number of {{bits}} bits, " . 
					"which may be given using any of the following notations:\n{{notations}}", 
				$this->bits, 'bits', self::class, $text_options, [
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
		} elseif ($this->unsigned) {
			/**
			 * @placeholder notations The supported integer number notation entries.
			 * @tags non-end-user
			 * @example An unsigned integer number, which may be given using any of the following notations:
			 *  &#8226; Standard (examples: "64", "85", "125");
			 *  &#8226; Exponential string (examples: "6.4e1", "0.85e2", "1.25e2");
			 *  &#8226; Octal string (examples: "0100", "0125", "0175");
			 *  &#8226; Hexadecimal string (examples: "0x40", "0x55", "0x7d");
			 *  &#8226; Human-readable string in English (examples: "0.064 thousand", "0.085k", "0.125 k").
			 */
			return UText::localize(
				"An unsigned integer number, which may be given using any of the following notations:\n{{notations}}", 
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
		/**
		 * @placeholder notations The supported integer number notation entries.
		 * @tags non-end-user
		 * @example An integer number, which may be given using any of the following notations:
		 *  &#8226; Standard (examples: "64", "85", "125");
		 *  &#8226; Exponential string (examples: "6.4e1", "0.85e2", "1.25e2");
		 *  &#8226; Octal string (examples: "0100", "0125", "0175");
		 *  &#8226; Hexadecimal string (examples: "0x40", "0x55", "0x7d");
		 *  &#8226; Human-readable string in English (examples: "0.064 thousand", "0.085k", "0.125 k").
		 */
		return UText::localize(
			"An integer number, which may be given using any of the following notations:\n{{notations}}", 
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
			if (isset($this->minimum) && isset($this->maximum)) {
				/**
				 * @placeholder minimum The minimum integer number.
				 * @placeholder maximum The maximum integer number.
				 * @tags end-user
				 * @example Only an integer number between -128 and 127 is allowed.
				 */
				return UText::localize(
					"Only an integer number between {{minimum}} and {{maximum}} is allowed.",
					self::class, $text_options, [
						'parameters' => ['minimum' => $this->minimum, 'maximum' => $this->maximum]
					]
				);
			} elseif (isset($this->minimum)) {
				/**
				 * @placeholder minimum The minimum integer number.
				 * @tags end-user
				 * @example Only an integer number greater than or equal to -128 is allowed.
				 */
				return UText::localize(
					"Only an integer number greater than or equal to {{minimum}} is allowed.",
					self::class, $text_options, [
						'parameters' => ['minimum' => $this->minimum]
					]
				);
			} elseif (isset($this->maximum)) {
				/**
				 * @placeholder maximum The maximum integer number.
				 * @tags end-user
				 * @example Only an integer number less than or equal to 127 is allowed.
				 */
				return UText::localize(
					"Only an integer number less than or equal to {{maximum}} is allowed.",
					self::class, $text_options, [
						'parameters' => ['maximum' => $this->maximum]
					]
				);
			}
			/** @tags end-user */
			return UText::localize("Only an integer number is allowed.", self::class, $text_options);
			
		//bits
		} elseif (isset($this->bits)) {
			if ($this->unsigned) {
				/**
				 * @placeholder bits The number of bits.
				 * @placeholder notations The supported integer number notation entries.
				 * @tags non-end-user
				 * @example Only an unsigned integer number of 32 bits is allowed, \
				 * which may be given using any of the following notations:
				 *  &#8226; Standard (examples: "64", "85", "125");
				 *  &#8226; Exponential string (examples: "6.4e1", "0.85e2", "1.25e2");
				 *  &#8226; Octal string (examples: "0100", "0125", "0175");
				 *  &#8226; Hexadecimal string (examples: "0x40", "0x55", "0x7d");
				 *  &#8226; Human-readable string in English (examples: "0.064 thousand", "0.085k", "0.125 k").
				 */
				return UText::plocalize(
					"Only an unsigned integer number of {{bits}} bit is allowed, " . 
						"which may be given using any of the following notations:\n{{notations}}", 
					"Only an unsigned integer number of {{bits}} bits is allowed, " . 
						"which may be given using any of the following notations:\n{{notations}}", 
					$this->bits, 'bits', self::class, $text_options, [
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
			/**
			 * @placeholder bits The number of bits.
			 * @placeholder notations The supported integer number notation entries.
			 * @tags non-end-user
			 * @example Only an integer number of 32 bits is allowed, \
			 * which may be given using any of the following notations:
			 *  &#8226; Standard (examples: "64", "85", "125");
			 *  &#8226; Exponential string (examples: "6.4e1", "0.85e2", "1.25e2");
			 *  &#8226; Octal string (examples: "0100", "0125", "0175");
			 *  &#8226; Hexadecimal string (examples: "0x40", "0x55", "0x7d");
			 *  &#8226; Human-readable string in English (examples: "0.064 thousand", "0.085k", "0.125 k").
			 */
			return UText::plocalize(
				"Only an integer number of {{bits}} bit is allowed, " . 
					"which may be given using any of the following notations:\n{{notations}}", 
				"Only an integer number of {{bits}} bits is allowed, " . 
					"which may be given using any of the following notations:\n{{notations}}", 
				$this->bits, 'bits', self::class, $text_options, [
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
			
		//unsigned
		} elseif ($this->unsigned) {
			/**
			 * @placeholder notations The supported integer number notation entries.
			 * @tags non-end-user
			 * @example Only an unsigned integer number is allowed, \
			 * which may be given using any of the following notations:
			 *  &#8226; Standard (examples: "64", "85", "125");
			 *  &#8226; Exponential string (examples: "6.4e1", "0.85e2", "1.25e2");
			 *  &#8226; Octal string (examples: "0100", "0125", "0175");
			 *  &#8226; Hexadecimal string (examples: "0x40", "0x55", "0x7d");
			 *  &#8226; Human-readable string in English (examples: "0.064 thousand", "0.085k", "0.125 k").
			 */
			return UText::localize(
				"Only an unsigned integer number is allowed, " . 
					"which may be given using any of the following notations:\n{{notations}}", 
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
		
		//non-end-user
		/**
		 * @placeholder notations The supported integer number notation entries.
		 * @tags non-end-user
		 * @example Only an integer number is allowed, which may be given using any of the following notations:
		 *  &#8226; Standard (examples: "64", "85", "125");
		 *  &#8226; Exponential string (examples: "6.4e1", "0.85e2", "1.25e2");
		 *  &#8226; Octal string (examples: "0100", "0125", "0175");
		 *  &#8226; Hexadecimal string (examples: "0x40", "0x55", "0x7d");
		 *  &#8226; Human-readable string in English (examples: "0.064 thousand", "0.085k", "0.125 k").
		 */
		return UText::localize(
			"Only an integer number is allowed, which may be given using any of the following notations:\n" . 
				"{{notations}}", 
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
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function getNotationStrings(TextOptions $text_options) : array
	{		
		//examples
		$examples = [];
		for ($i = 0; $i < 3; $i++) {
			$maximum = (int)(10e6 / 10 ** $i);
			if (isset($this->maximum) && $this->maximum < $maximum) {
				$maximum = $this->maximum;
			}
			$examples[] = mt_rand(0, $maximum);
		}
		$examples = array_values(array_unique($examples, SORT_NUMERIC));
		sort($examples, SORT_NUMERIC);
		
		//strings
		$strings = [];
		if ($text_options->info_scope !== EInfoScope::ENDUSER) {
			//standard
			/**
			 * @description Standard notation string.
			 * @placeholder examples The list of integer number examples in standard notation.
			 * @tags non-end-user
			 * @example Standard (examples: "64", "85", "125")
			 */
			$strings[] = UText::localize(
				"Standard (examples: {{examples}})",
				self::class, $text_options, [
					'parameters' => ['examples' => array_map('strval', $examples)],
					'string_options' => [
						'quote_strings' => true,
						'non_assoc_mode' => UText::STRING_NONASSOC_MODE_COMMA_LIST
					]
				]
			);
			
			//exponential
			/**
			 * @description Exponential notation string.
			 * @placeholder examples The list of integer number examples in exponential notation.
			 * @tags non-end-user
			 * @example Exponential string (examples: "6.4e1", "0.85e2", "1.25e2")
			 */
			$strings[] = UText::localize(
				"Exponential string (examples: {{examples}})",
				self::class, $text_options, [
					'parameters' => [
						'examples' => array_map(function ($i, $n) {
							return round($n / 10 ** ($i + 2), 3) . 'e' . ($i + 2);
						}, array_keys($examples), $examples)
					],
					'string_options' => [
						'quote_strings' => true,
						'non_assoc_mode' => UText::STRING_NONASSOC_MODE_COMMA_LIST
					]
				]
			);
			
			//octal
			/**
			 * @description Octal notation string.
			 * @placeholder examples The list of integer number examples in octal notation.
			 * @tags non-end-user
			 * @example Octal string (examples: "0100", "0125", "0175")
			 */
			$strings[] = UText::localize(
				"Octal string (examples: {{examples}})",
				self::class, $text_options, [
					'parameters' => [
						'examples' => array_map(function ($n) {
							return '0' . decoct($n);
						}, $examples)
					],
					'string_options' => [
						'quote_strings' => true,
						'non_assoc_mode' => UText::STRING_NONASSOC_MODE_COMMA_LIST
					]
				]
			);
			
			//hexadecimal
			/**
			 * @description Hexadecimal notation string.
			 * @placeholder examples The list of integer number examples in hexadecimal notation.
			 * @tags non-end-user
			 * @example Hexadecimal string (examples: "0x40", "0x55", "0x7d")
			 */
			$strings[] = UText::localize(
				"Hexadecimal string (examples: {{examples}})",
				self::class, $text_options, [
					'parameters' => [
						'examples' => array_map(function ($n) {
							return '0x' . dechex($n);
						}, $examples)
					],
					'string_options' => [
						'quote_strings' => true,
						'non_assoc_mode' => UText::STRING_NONASSOC_MODE_COMMA_LIST
					]
				]
			);
			
			//human-readable
			/**
			 * @description Human-readable notation string.
			 * @placeholder examples The list of integer number examples in human-readable notation.
			 * @tags non-end-user
			 * @example Human-readable string in English (examples: "0.064 thousand", "0.085k", "0.125 k")
			 */
			$strings[] = UText::localize(
				"Human-readable string in English (examples: {{examples}})",
				self::class, $text_options, [
					'parameters' => [
						'examples' => array_map(function ($i, $n) {
							return UMath::hnumber($n, null, ['long' => $i % 2 !== 0]);
						}, array_keys($examples), $examples)
					],
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
