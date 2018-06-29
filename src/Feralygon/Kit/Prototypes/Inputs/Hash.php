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
use Feralygon\Kit\Prototypes\Inputs\Hash\Prototypes\Modifiers\{
	Constraints,
	Filters
};
use Feralygon\Kit\Options\Text as TextOptions;
use Feralygon\Kit\Components\Input\Options\Info as InfoOptions;
use Feralygon\Kit\Enumerations\InfoScope as EInfoScope;
use Feralygon\Kit\Utilities\{
	Base64 as UBase64,
	Hash as UHash,
	Text as UText
};

/**
 * This input prototype represents a hash, as a string in hexadecimal notation.
 * 
 * Only the following types of values may be evaluated as a hash:<br>
 * &nbsp; &#8226; &nbsp; a hexadecimal notation string;<br>
 * &nbsp; &#8226; &nbsp; a Base64 or an URL-safe Base64 encoded string;<br>
 * &nbsp; &#8226; &nbsp; a raw binary string.
 * 
 * @since 1.0.0
 * @see https://en.wikipedia.org/wiki/Hash_function
 * @see \Feralygon\Kit\Prototypes\Inputs\Hash\Prototypes\Modifiers\Constraints\Values
 * [modifier, name = 'constraints.values' or 'values' or 'constraints.non_values' or 'non_values']
 * @see \Feralygon\Kit\Prototypes\Inputs\Hash\Prototypes\Modifiers\Filters\Raw
 * [modifier, name = 'filters.raw']
 * @see \Feralygon\Kit\Prototypes\Inputs\Hash\Prototypes\Modifiers\Filters\Base64
 * [modifier, name = 'filters.base64']
 */
abstract class Hash extends Input implements IInformation, IModifiers
{
	//Abstract public methods
	/**
	 * Get number of bits.
	 * 
	 * @since 1.0.0
	 * @return int
	 * <p>The number of bits.</p>
	 */
	abstract public function getBits(): int;
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function getName(): string
	{
		return 'hash';
	}
	
	/** {@inheritdoc} */
	public function evaluateValue(&$value): bool
	{
		return UHash::evaluate($value, $this->getBits());
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Prototypes\Input\Interfaces\Information)
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options, InfoOptions $info_options): string
	{
		return UText::localize("Hash", self::class, $text_options);
	}
	
	/** {@inheritdoc} */
	public function getDescription(TextOptions $text_options, InfoOptions $info_options): string
	{
		//end-user
		if ($text_options->info_scope === EInfoScope::ENDUSER) {
			/**
			 * @placeholder label The hash input label.
			 * @tags end-user
			 * @example A CRC32 hash, given in hexadecimal notation.
			 */
			return UText::localize(
				"A {{label}} hash, given in hexadecimal notation.", 
				self::class, $text_options, ['parameters' => ['label' => $this->getLabel($text_options, $info_options)]]
			);
		}
		
		//non-end-user
		/**
		 * @placeholder label The hash input label.
		 * @placeholder notations The supported hash notation entries.
		 * @tags non-end-user
		 * @example A CRC32 hash, which may be given using any of the following notations:
		 *  &#8226; Hexadecimal string (example: "a7fed3fa");
		 *  &#8226; Base64 encoded string (example: "p/7T+g==");
		 *  &#8226; URL-safe Base64 encoded string (example: "p_7T-g");
		 *  &#8226; Raw binary string.
		 */
		return UText::localize(
			"A {{label}} hash, which may be given using any of the following notations:\n{{notations}}", 
			self::class, $text_options, [
				'parameters' => [
					'label' => $this->getLabel($text_options, $info_options), 
					'notations' => UText::mbulletify(
						$this->getNotationStrings($text_options), $text_options, ['merge' => true, 'punctuate' => true]
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
			/**
			 * @placeholder label The hash input label.
			 * @tags end-user
			 * @example Only a CRC32 hash, given in hexadecimal notation, is allowed.
			 */
			return UText::localize(
				"Only a {{label}} hash, given in hexadecimal notation, is allowed.", 
				self::class, $text_options, ['parameters' => ['label' => $this->getLabel($text_options, $info_options)]]
			);
		}
		
		//non-end-user
		/**
		 * @placeholder label The hash input label.
		 * @placeholder notations The supported hash notation entries.
		 * @tags non-end-user
		 * @example Only a CRC32 hash is allowed, which may be given using any of the following notations:
		 *  &#8226; Hexadecimal string (example: "a7fed3fa");
		 *  &#8226; Base64 encoded string (example: "p/7T+g==");
		 *  &#8226; URL-safe Base64 encoded string (example: "p_7T-g");
		 *  &#8226; Raw binary string.
		 */
		return UText::localize(
			"Only a {{label}} hash is allowed, which may be given using any of the following notations:\n{{notations}}",
			self::class, $text_options, [
				'parameters' => [
					'label' => $this->getLabel($text_options, $info_options), 
					'notations' => UText::mbulletify(
						$this->getNotationStrings($text_options), $text_options, ['merge' => true, 'punctuate' => true]
					)
				]
			]
		);
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Prototypes\Input\Interfaces\Modifiers)
	/** {@inheritdoc} */
	public function buildModifier(string $name, array $properties = []): ?Modifier
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
			
			//filters
			case 'filters.raw':
				return $this->createFilter(Filters\Raw::class, $properties);
			case 'filters.base64':
				return $this->createFilter(Filters\Base64::class, $properties);
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
	protected function getNotationStrings(TextOptions $text_options): array
	{
		$strings = [];
		if ($text_options->info_scope !== EInfoScope::ENDUSER) {
			//initialize
			$example_value = random_bytes($this->getBits() / 8);
			
			//hexadecimal
			/**
			 * @description Hexadecimal notation string.
			 * @placeholder example The hash example in hexadecimal notation.
			 * @tags non-end-user
			 * @example Hexadecimal string (example: "a7fed3fa")
			 */
			$strings[] = UText::localize(
				"Hexadecimal string (example: {{example}})",
				self::class, $text_options, [
					'parameters' => ['example' => bin2hex($example_value)],
					'string_options' => ['quote_strings' => true]
				]
			);
			
			//base64 encoded
			/**
			 * @description Base64 encoded notation string.
			 * @placeholder example The hash example in Base64 encoded notation.
			 * @tags non-end-user
			 * @example Base64 encoded string (example: "p/7T+g==")
			 */
			$strings[] = UText::localize(
				"Base64 encoded string (example: {{example}})",
				self::class, $text_options, [
					'parameters' => ['example' => UBase64::encode($example_value)],
					'string_options' => ['quote_strings' => true]
				]
			);
			
			//url-safe base64 encoded
			/**
			 * @description URL-safe Base64 encoded notation string.
			 * @placeholder example The hash example in URL-safe Base64 encoded notation.
			 * @tags non-end-user
			 * @example URL-safe Base64 encoded string (example: "p_7T-g")
			 */
			$strings[] = UText::localize(
				"URL-safe Base64 encoded string (example: {{example}})",
				self::class, $text_options, [
					'parameters' => ['example' => UBase64::encode($example_value, true)],
					'string_options' => ['quote_strings' => true]
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
