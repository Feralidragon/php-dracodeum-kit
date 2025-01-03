<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Prototypes\Inputs;

use Dracodeum\Kit\Prototypes\Input;
use Dracodeum\Kit\Prototypes\Input\Interfaces\{
	Information as IInformation,
	SchemaData as ISchemaData,
	ConstraintProducer as IConstraintProducer,
	FilterProducer as IFilterProducer
};
use Dracodeum\Kit\Prototypes\Inputs\Hash\{
	Constraints,
	Filters
};
use Dracodeum\Kit\Traits\LazyProperties\Property;
use Dracodeum\Kit\Options\Text as TextOptions;
use Dracodeum\Kit\Components\Input\Options\Info as InfoOptions;
use Dracodeum\Kit\Enumerations\InfoScope as EInfoScope;
use Dracodeum\Kit\Utilities\{
	Base64 as UBase64,
	Hash as UHash,
	Math as UMath,
	Text as UText
};

/**
 * This input prototype represents a hash, as a string in hexadecimal notation.
 * 
 * Only the following types of values may be evaluated as a hash:<br>
 * &nbsp; &#8226; &nbsp; a hexadecimal notation string;<br>
 * &nbsp; &#8226; &nbsp; a colon-hexadecimal notation string, as octets or hextets;<br>
 * &nbsp; &#8226; &nbsp; a Base64 or a URL-safe Base64 encoded string;<br>
 * &nbsp; &#8226; &nbsp; a raw binary string.
 * 
 * @property-write int|null $bits [writeonce] [transient] [default = null]
 * <p>The number of bits to use.</p>
 * @property-write string|null $label [writeonce] [transient] [default = null]
 * <p>The label to use.</p>
 * @see https://en.wikipedia.org/wiki/Hash_function
 */
class Hash extends Input implements IInformation, ISchemaData, IConstraintProducer, IFilterProducer
{
	//Protected properties
	/** @var int|null */
	protected $bits = null;
	
	/** @var string|null */
	protected $label = null;
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function getName(): string
	{
		return 'hash';
	}
	
	/** {@inheritdoc} */
	public function isScalar(): bool
	{
		return true;
	}
	
	/** {@inheritdoc} */
	public function evaluateValue(&$value): bool
	{
		return UHash::evaluate($value, $this->bits);
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Prototypes\Input\Interfaces\Information)
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options, InfoOptions $info_options): string
	{
		return $this->label ?? UText::localize("Hash", self::class, $text_options);
	}
	
	/** {@inheritdoc} */
	public function getDescription(TextOptions $text_options, InfoOptions $info_options): string
	{
		//end-user
		if ($text_options->info_scope === EInfoScope::ENDUSER) {
			//label
			if (isset($this->label)) {
				/**
				 * @placeholder label The hash label.
				 * @tags end-user
				 * @example A CRC32 hash, given in hexadecimal notation.
				 */
				return UText::localize(
					"A {{label}} hash, given in hexadecimal notation.", 
					self::class, $text_options, ['parameters' => ['label' => $this->label]]
				);
			}
			
			//bits
			if (isset($this->bits)) {
				/**
				 * @placeholder bits The hash number of bits.
				 * @tags end-user
				 * @example A hash of 32 bits, given in hexadecimal notation.
				 */
				return UText::localize(
					"A hash of {{bits}} bits, given in hexadecimal notation.", 
					self::class, $text_options, ['parameters' => ['bits' => $this->bits]]
				);
			}
			
			//default
			/** @tags end-user */
			return UText::localize("A hash, given in hexadecimal notation.", self::class, $text_options);
		}
		
		//notations
		$notations_string = UText::mbulletify(
			$this->getNotationStrings($text_options), $text_options, ['merge' => true, 'punctuate' => true]
		);
		
		//label
		if (isset($this->label)) {
			/**
			 * @placeholder label The hash label.
			 * @placeholder notations The supported hash notation entries.
			 * @tags non-end-user
			 * @example A CRC32 hash, which may be given using any of the following notations:
			 *  &#8226; Hexadecimal case-insensitive string (example: "a7fed3fa" or "A7FED3FA");
			 *  &#8226; Colon-hexadecimal case-insensitive string (example: "a7:fe:d3:fa" or "A7FE:D3FA");
			 *  &#8226; Base64 or URL-safe Base64 encoded string (example: "p/7T+g==" or "p_7T-g");
			 *  &#8226; Raw binary string.
			 */
			return UText::localize(
				"A {{label}} hash, which may be given using any of the following notations:\n{{notations}}", 
				self::class, $text_options, [
					'parameters' => ['label' => $this->label, 'notations' => $notations_string]
				]
			);
		}
		
		//bits
		if (isset($this->bits)) {
			/**
			 * @placeholder bits The hash number of bits.
			 * @placeholder notations The supported hash notation entries.
			 * @tags non-end-user
			 * @example A hash of 32 bits, which may be given using any of the following notations:
			 *  &#8226; Hexadecimal case-insensitive string (example: "a7fed3fa" or "A7FED3FA");
			 *  &#8226; Colon-hexadecimal case-insensitive string (example: "a7:fe:d3:fa" or "A7FE:D3FA");
			 *  &#8226; Base64 or URL-safe Base64 encoded string (example: "p/7T+g==" or "p_7T-g");
			 *  &#8226; Raw binary string.
			 */
			return UText::localize(
				"A hash of {{bits}} bits, which may be given using any of the following notations:\n{{notations}}", 
				self::class, $text_options, ['parameters' => ['bits' => $this->bits, 'notations' => $notations_string]]
			);
		}
		
		//default
		/**
		 * @placeholder notations The supported hash notation entries.
		 * @tags non-end-user
		 * @example A hash, which may be given using any of the following notations:
		 *  &#8226; Hexadecimal case-insensitive string (example: "a7fed3fa" or "A7FED3FA");
		 *  &#8226; Colon-hexadecimal case-insensitive string (example: "a7:fe:d3:fa" or "A7FE:D3FA");
		 *  &#8226; Base64 or URL-safe Base64 encoded string (example: "p/7T+g==" or "p_7T-g");
		 *  &#8226; Raw binary string.
		 */
		return UText::localize(
			"A hash, which may be given using any of the following notations:\n{{notations}}", 
			self::class, $text_options, ['parameters' => ['notations' => $notations_string]]
		);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options, InfoOptions $info_options): string
	{
		//end-user
		if ($text_options->info_scope === EInfoScope::ENDUSER) {
			//label
			if (isset($this->label)) {
				/**
				 * @placeholder label The hash label.
				 * @tags end-user
				 * @example Only a CRC32 hash, given in hexadecimal notation, is allowed.
				 */
				return UText::localize(
					"Only a {{label}} hash, given in hexadecimal notation, is allowed.", 
					self::class, $text_options, ['parameters' => ['label' => $this->label]]
				);
			}
			
			//bits
			if (isset($this->bits)) {
				/**
				 * @placeholder bits The hash number of bits.
				 * @tags end-user
				 * @example Only a hash of 32 bits, given in hexadecimal notation, is allowed.
				 */
				return UText::localize(
					"Only a hash of {{bits}} bits, given in hexadecimal notation, is allowed.", 
					self::class, $text_options, ['parameters' => ['bits' => $this->bits]]
				);
			}
			
			//default
			/** @tags end-user */
			return UText::localize(
				"Only a hash, given in hexadecimal notation, is allowed.", self::class, $text_options
			);
		}
		
		//notations
		$notations_string = UText::mbulletify(
			$this->getNotationStrings($text_options), $text_options, ['merge' => true, 'punctuate' => true]
		);
		
		//label
		if (isset($this->label)) {
			/**
			 * @placeholder label The hash label.
			 * @placeholder notations The supported hash notation entries.
			 * @tags non-end-user
			 * @example Only a CRC32 hash is allowed, which may be given using any of the following notations:
			 *  &#8226; Hexadecimal case-insensitive string (example: "a7fed3fa" or "A7FED3FA");
			 *  &#8226; Colon-hexadecimal case-insensitive string (example: "a7:fe:d3:fa" or "A7FE:D3FA");
			 *  &#8226; Base64 or URL-safe Base64 encoded string (example: "p/7T+g==" or "p_7T-g");
			 *  &#8226; Raw binary string.
			 */
			return UText::localize(
				"Only a {{label}} hash is allowed, " . 
					"which may be given using any of the following notations:\n{{notations}}",
				self::class, $text_options, [
					'parameters' => ['label' => $this->label, 'notations' => $notations_string]
				]
			);
		}
		
		//bits
		if (isset($this->bits)) {
			/**
			 * @placeholder bits The hash number of bits.
			 * @placeholder notations The supported hash notation entries.
			 * @tags non-end-user
			 * @example Only a hash of 32 bits is allowed, which may be given using any of the following notations:
			 *  &#8226; Hexadecimal case-insensitive string (example: "a7fed3fa" or "A7FED3FA");
			 *  &#8226; Colon-hexadecimal case-insensitive string (example: "a7:fe:d3:fa" or "A7FE:D3FA");
			 *  &#8226; Base64 or URL-safe Base64 encoded string (example: "p/7T+g==" or "p_7T-g");
			 *  &#8226; Raw binary string.
			 */
			return UText::localize(
				"Only a hash of {{bits}} bits is allowed, " . 
					"which may be given using any of the following notations:\n{{notations}}",
				self::class, $text_options, ['parameters' => ['bits' => $this->bits, 'notations' => $notations_string]]
			);
		}
		
		//default
		/**
		 * @placeholder notations The supported hash notation entries.
		 * @tags non-end-user
		 * @example Only a hash is allowed, which may be given using any of the following notations:
		 *  &#8226; Hexadecimal case-insensitive string (example: "a7fed3fa" or "A7FED3FA");
		 *  &#8226; Colon-hexadecimal case-insensitive string (example: "a7:fe:d3:fa" or "A7FE:D3FA");
		 *  &#8226; Base64 or URL-safe Base64 encoded string (example: "p/7T+g==" or "p_7T-g");
		 *  &#8226; Raw binary string.
		 */
		return UText::localize(
			"Only a hash is allowed, which may be given using any of the following notations:\n{{notations}}",
			self::class, $text_options, ['parameters' => ['notations' => $notations_string]]
		);
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Prototypes\Input\Interfaces\SchemaData)
	/** {@inheritdoc} */
	public function getSchemaData()
	{
		return [
			'bits' => $this->bits,
			'label' => $this->label
		];
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Prototypes\Input\Interfaces\ConstraintProducer)
	/** {@inheritdoc} */
	public function produceConstraint(string $name, array $properties)
	{
		switch ($name) {
			case 'values':
				return Constraints\Values::class;
			case 'non_values':
				return $this->createConstraint(Constraints\Values::class, ['negate' => true] + $properties);
		}
		return null;
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Prototypes\Input\Interfaces\FilterProducer)
	/** {@inheritdoc} */
	public function produceFilter(string $name, array $properties)
	{
		switch ($name) {
			case 'base64':
				return Filters\Base64::class;
			case 'colonify':
				return Filters\Colonify::class;
			case 'raw':
				return Filters\Raw::class;
			case 'uppercase':
				return Filters\Uppercase::class;
		}
		return null;
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Prototype\Traits\PropertyBuilder)
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		switch ($name) {
			case 'bits':
				return $this->createProperty()
					->setMode('w--')
					->setAsInteger(true, null, true)
					->addEvaluator(function (&$value): bool {
						return !isset($value) || $value % 8 === 0;
					})
					->bind(self::class)
				;
			case 'label':
				return $this->createProperty()->setMode('w--')->setAsString(true, true)->bind(self::class);
		}
		return null;
	}
	
	
	
	//Protected methods
	/**
	 * Get notation strings.
	 * 
	 * @param \Dracodeum\Kit\Options\Text $text_options
	 * <p>The text options instance to use.</p>
	 * @return string[]
	 * <p>The notation strings.</p>
	 */
	protected function getNotationStrings(TextOptions $text_options): array
	{
		$strings = [];
		if ($text_options->info_scope !== EInfoScope::ENDUSER) {
			//initialize
			$example_value = '';
			$bytes = isset($this->bits) ? $this->bits / 8 : 4;
			for ($i = 0; $i < $bytes; $i++) {
				$example_value .= chr(UMath::random(255, 0, $i));
			}
			
			//hexadecimal (examples)
			$example_hexadecimal = bin2hex($example_value);
			$hexadecimal_examples = [$example_hexadecimal, strtoupper($example_hexadecimal)];
			
			//hexadecimal (notation)
			/**
			 * @description Hexadecimal notation string.
			 * @placeholder example The hash example in hexadecimal notation.
			 * @tags non-end-user
			 * @example Hexadecimal case-insensitive string (example: "a7fed3fa" or "A7FED3FA")
			 */
			$strings[] = UText::localize(
				"Hexadecimal case-insensitive string (example: {{example}})",
				self::class, $text_options, [
					'parameters' => [
						'example' => UText::commify($hexadecimal_examples, $text_options, 'or', true)
					]
				]
			);
			
			//colon-hexadecimal
			if ($bytes >= 2) {
				//examples
				$colon_hexadecimal_examples = [
					UHash::colonify($example_hexadecimal),
					strtoupper(UHash::colonify($example_hexadecimal, $bytes >= 4 && $bytes % 2 === 0))
				];
				
				//notation
				/**
				 * @description Colon-hexadecimal notation string.
				 * @placeholder example The hash example in colon-hexadecimal notation.
				 * @tags non-end-user
				 * @example Colon-hexadecimal case-insensitive string (example: "a7:fe:d3:fa" or "A7FE:D3FA")
				 */
				$strings[] = UText::localize(
					"Colon-hexadecimal case-insensitive string (example: {{example}})",
					self::class, $text_options, [
						'parameters' => [
							'example' => UText::commify($colon_hexadecimal_examples, $text_options, 'or', true)
						]
					]
				);
			}
			
			//base64 encoded (examples)
			$example_base64 = UBase64::encode($example_value);
			$example_base64_urlsafe = UBase64::encode($example_value, true);
			$base64_examples = [$example_base64];
			if ($example_base64 !== $example_base64_urlsafe) {
				$base64_examples[] = $example_base64_urlsafe;
			}
			
			//base64 encoded (notation)
			/**
			 * @description Base64 or URL-safe Base64 encoded notation string.
			 * @placeholder example The hash example in Base64 or URL-safe Base64 encoded notation.
			 * @tags non-end-user
			 * @example Base64 or URL-safe Base64 encoded string (example: "p/7T+g==" or "p_7T-g")
			 */
			$strings[] = UText::localize(
				"Base64 or URL-safe Base64 encoded string (example: {{example}})",
				self::class, $text_options, [
					'parameters' => [
						'example' => UText::commify($base64_examples, $text_options, 'or', true)
					]
				]
			);
			
			//raw binary
			/**
			 * @description Raw binary notation string.
			 * @tags non-end-user
			 */
			$strings[] = UText::localize("Raw binary string", self::class, $text_options);
		}
		return $strings;
	}
}
