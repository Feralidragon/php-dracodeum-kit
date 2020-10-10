<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Prototypes\Inputs\Numbers;

use Dracodeum\Kit\Prototypes\Inputs\Number;
use Dracodeum\Kit\Prototypes\Input\Interfaces\SchemaData as ISchemaData;
use Dracodeum\Kit\Traits\LazyProperties\Property;
use Dracodeum\Kit\Options\Text as TextOptions;
use Dracodeum\Kit\Components\Input\Options\Info as InfoOptions;
use Dracodeum\Kit\Enumerations\InfoScope as EInfoScope;
use Dracodeum\Kit\Utilities\{
	Call as UCall,
	Math as UMath,
	Text as UText,
	Type as UType
};

/**
 * This input prototype represents an integer number.
 * 
 * Only the following types of values may be evaluated as an integer number:<br>
 * &nbsp; &#8226; &nbsp; an integer or float;<br>
 * &nbsp; &#8226; &nbsp; a numeric string, such as <code>"1000"</code>;<br>
 * &nbsp; &#8226; &nbsp; a numeric string in exponential notation, 
 * such as <code>"1e3"</code> or <code>"1E3"</code>;<br>
 * &nbsp; &#8226; &nbsp; a numeric string in octal notation, such as <code>"01750"</code>;<br>
 * &nbsp; &#8226; &nbsp; a numeric string in hexadecimal notation, 
 * such as <code>"0x03e8"</code> or <code>"0x03E8"</code>;<br>
 * &nbsp; &#8226; &nbsp; a human-readable numeric string in English, 
 * such as <code>"1 thousand"</code> or <code>"1k"</code>;<br>
 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Integerable</code> interface;<br>
 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Floatable</code> interface.
 * 
 * @property-write bool $unsigned [writeonce] [transient] [coercive] [default = false]
 * <p>Set as an unsigned integer.</p>
 * @property-write int|null $bits [writeonce] [transient] [coercive] [default = null]
 * <p>The number of bits to use.<br>
 * <br>
 * For signed integers, the maximum allowed number is <code>64</code>, 
 * while for unsigned integers this number is <code>63</code>.<br>
 * If not set, then the number of bits to use becomes system dependent.</p>
 * @see https://en.wikipedia.org/wiki/Integer_(computer_science)
 * @see \Dracodeum\Kit\Interfaces\Integerable
 * @see \Dracodeum\Kit\Interfaces\Floatable
 */
class Integer extends Number implements ISchemaData
{
	//Protected properties
	/** @var bool */
	protected $unsigned = false;
	
	/** @var int|null */
	protected $bits = null;
	
	/** @var int|null */
	protected $minimum = null;
	
	/** @var int|null */
	protected $maximum = null;
	
	
	
	//Implemented public methods (Dracodeum\Kit\Prototypes\Input\Interfaces\SchemaData)
	/** {@inheritdoc} */
	public function getSchemaData()
	{
		return [
			'unsigned' => $this->unsigned,
			'bits' => $this->bits
		];
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Prototype\Traits\PropertyBuilder)
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		switch ($name) {
			case 'unsigned':
				return $this->createProperty()->setMode('w--')->setAsBoolean()->bind(self::class);
			case 'bits':
				return $this->createProperty()
					->setMode('w--')
					->setAsInteger(true, null, true)
					->addEvaluator(function (&$value): bool {
						return !isset($value) || $value > 0;
					})
					->bind(self::class)
				;
		}
		return null;
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Prototype\Traits\Initializer)
	/** {@inheritdoc} */
	protected function initialize(): void
	{
		if ($this->unsigned) {
			$this->minimum = 0;
			if (isset($this->bits)) {
				UCall::guardInternal($this->bits <= UType::INTEGER_BITS_MAX_UNSIGNED, [
					'error_message' => "Invalid bits {{bits}}.",
					'hint_message' => "Only up to {{max_bits}} bits are allowed for unsigned integers.",
					'parameters' => ['bits' => $this->bits, 'max_bits' => UType::INTEGER_BITS_MAX_UNSIGNED]
				]);
				$this->maximum = UType::INTEGER_BITS_FULL_UNSIGNED >> (UType::INTEGER_BITS_MAX_UNSIGNED - $this->bits);
			}
		} elseif (isset($this->bits)) {
			UCall::guardInternal($this->bits <= UType::INTEGER_BITS_MAX_SIGNED, [
				'error_message' => "Invalid bits {{bits}}.",
				'hint_message' => "Only up to {{max_bits}} bits are allowed for signed integers.",
				'parameters' => ['bits' => $this->bits, 'max_bits' => UType::INTEGER_BITS_MAX_SIGNED]
			]);
			$this->maximum = UType::INTEGER_BITS_FULL_UNSIGNED >> (UType::INTEGER_BITS_MAX_SIGNED - $this->bits);
			$this->minimum = -$this->maximum - 1;
		}
	}
	
	
	
	//Overridden public methods
	/** {@inheritdoc} */
	public function getName(): string
	{
		return 'integer';
	}
	
	/** {@inheritdoc} */
	public function evaluateValue(&$value): bool
	{
		return UType::evaluateInteger($value) && 
			(!isset($this->minimum) || $value >= $this->minimum) && 
			(!isset($this->maximum) || $value <= $this->maximum);
	}
	
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options, InfoOptions $info_options): string
	{
		//non-end-user
		if ($text_options->info_scope !== EInfoScope::ENDUSER) {
			//bits
			if (isset($this->bits)) {
				//unsigned
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
				
				//signed
				/**
				 * @placeholder bits The number of bits.
				 * @tags non-end-user
				 * @example Integer (32 bits)
				 */
				return UText::plocalize(
					"Integer ({{bits}} bit)", "Integer ({{bits}} bits)",
					$this->bits, 'bits', self::class, $text_options
				);
			}
			
			//unsigned
			if ($this->unsigned) {
				/** @tags non-end-user */
				return UText::localize("Unsigned integer", self::class, $text_options);
			}
		}
		
		//default
		return UText::localize("Integer", self::class, $text_options);
	}
	
	/** {@inheritdoc} */
	public function getDescription(TextOptions $text_options, InfoOptions $info_options): string
	{
		//end-user
		if ($text_options->info_scope === EInfoScope::ENDUSER) {
			//minimum and maximum
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
			}
			
			//minimum
			if (isset($this->minimum)) {
				/**
				 * @placeholder minimum The minimum integer number.
				 * @tags end-user
				 * @example An integer number greater than or equal to -128.
				 */
				return UText::localize(
					"An integer number greater than or equal to {{minimum}}.",
					self::class, $text_options, ['parameters' => ['minimum' => $this->minimum]]
				);
			}
			
			//maximum
			if (isset($this->maximum)) {
				/**
				 * @placeholder maximum The maximum integer number.
				 * @tags end-user
				 * @example An integer number less than or equal to 127.
				 */
				return UText::localize(
					"An integer number less than or equal to {{maximum}}.",
					self::class, $text_options, ['parameters' => ['maximum' => $this->maximum]]
				);
			}
			
			//default
			/** @tags end-user */
			return UText::localize("An integer number.", self::class, $text_options);
		}
		
		//non-end-user
		if (isset($this->bits)) {
			//unsigned
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
			
			//signed
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
		}
		
		//unsigned
		if ($this->unsigned) {
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
		
		//default
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
	public function getMessage(TextOptions $text_options, InfoOptions $info_options): string
	{
		//end-user
		if ($text_options->info_scope === EInfoScope::ENDUSER) {
			//minimum and maximum
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
			}
			
			//minimum
			if (isset($this->minimum)) {
				/**
				 * @placeholder minimum The minimum integer number.
				 * @tags end-user
				 * @example Only an integer number greater than or equal to -128 is allowed.
				 */
				return UText::localize(
					"Only an integer number greater than or equal to {{minimum}} is allowed.",
					self::class, $text_options, ['parameters' => ['minimum' => $this->minimum]]
				);
			}
			
			//maximum
			if (isset($this->maximum)) {
				/**
				 * @placeholder maximum The maximum integer number.
				 * @tags end-user
				 * @example Only an integer number less than or equal to 127 is allowed.
				 */
				return UText::localize(
					"Only an integer number less than or equal to {{maximum}} is allowed.",
					self::class, $text_options, ['parameters' => ['maximum' => $this->maximum]]
				);
			}
			
			//default
			/** @tags end-user */
			return UText::localize("Only an integer number is allowed.", self::class, $text_options);
		}
			
		//non-end-user
		if (isset($this->bits)) {
			//unsigned
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
			
			//signed
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
		}
		
		//unsigned
		if ($this->unsigned) {
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
		
		//default
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
	protected function getNotationStrings(TextOptions $text_options): array
	{		
		//examples
		$examples = [];
		for ($i = 0; $i < 3; $i++) {
			$maximum = (int)(10e6 / 10 ** $i);
			if (isset($this->maximum) && $this->maximum < $maximum) {
				$maximum = $this->maximum;
			}
			$examples[] = UMath::random($maximum, 0, $i);
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
