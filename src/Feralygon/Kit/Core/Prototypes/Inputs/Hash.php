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
use Feralygon\Kit\Core\Prototypes\Inputs\Hash\Prototypes\Modifiers\{
	Constraints,
	Filters
};
use Feralygon\Kit\Core\Options\Text as TextOptions;
use Feralygon\Kit\Core\Components\Input\Options\Info as InfoOptions;
use Feralygon\Kit\Core\Enumerations\InfoScope as EInfoScope;
use Feralygon\Kit\Core\Utilities\{
	Base64 as UBase64,
	Hash as UHash,
	Text as UText
};

/**
 * Core hash input prototype class.
 * 
 * This input prototype represents a hash, as a string in hexadecimal notation, for which only the following types of values may be evaluated as such:<br>
 * &nbsp; &#8226; &nbsp; a hexadecimal notation string;<br>
 * &nbsp; &#8226; &nbsp; a Base64 or an URL-safe Base64 encoded string;<br>
 * &nbsp; &#8226; &nbsp; a raw binary string.
 * 
 * @since 1.0.0
 * @see https://en.wikipedia.org/wiki/Hash_function
 * @see \Feralygon\Kit\Core\Prototypes\Inputs\Hash\Prototypes\Modifiers\Constraints\Values [modifier, name = 'constraints.values' or 'constraints.non_values']
 * @see \Feralygon\Kit\Core\Prototypes\Inputs\Hash\Prototypes\Modifiers\Filters\Raw [modifier, name = 'filters.raw']
 * @see \Feralygon\Kit\Core\Prototypes\Inputs\Hash\Prototypes\Modifiers\Filters\Base64 [modifier, name = 'filters.base64']
 */
abstract class Hash extends Input implements IInformation, IModifiers
{
	//Abstract public methods
	/**
	 * Get number of bits.
	 * 
	 * @since 1.0.0
	 * @return int <p>The number of bits.</p>
	 */
	abstract public function getBits() : int;
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function getName() : string
	{
		return 'hash';
	}
	
	/** {@inheritdoc} */
	public function evaluateValue(&$value) : bool
	{
		return UHash::evaluate($value, $this->getBits());
	}
	
	
	
	//Implemented public methods (core input prototype information interface)
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options, InfoOptions $info_options) : string
	{
		/**
		 * @description Core hash input prototype label.
		 * @tags core prototype input hash label
		 */
		return UText::localize("Hash", 'core.prototypes.inputs.hash', $text_options);
	}
	
	/** {@inheritdoc} */
	public function getDescription(TextOptions $text_options, InfoOptions $info_options) : string
	{
		//end-user
		if ($text_options->info_scope === EInfoScope::ENDUSER) {
			/**
			 * @description Core hash input prototype description (end-user).
			 * @placeholder label The hash input label.
			 * @tags core prototype input hash description end-user
			 * @example A CRC32 hash, given in hexadecimal notation.
			 */
			return UText::localize(
				"A {{label}} hash, given in hexadecimal notation.", 
				'core.prototypes.inputs.hash', $text_options, [
					'parameters' => ['label' => $this->getLabel($text_options, $info_options)]
				]
			);
		}
		
		//non-end-user
		/**
		 * @description Core hash input prototype description.
		 * @placeholder label The hash input label.
		 * @placeholder notations The supported hash notation entries.
		 * @tags core prototype input hash description non-end-user
		 * @example A CRC32 hash, which may be given using any of the following notations:
		 *  &#8226; Hexadecimal string (example: "a7fed3fa");
		 *  &#8226; Base64 encoded string (example: "p/7T+g==");
		 *  &#8226; URL-safe Base64 encoded string (example: "p_7T-g");
		 *  &#8226; Raw binary string.
		 */
		return UText::localize(
			"A {{label}} hash, which may be given using any of the following notations:\n{{notations}}", 
			'core.prototypes.inputs.hash', $text_options, [
				'parameters' => [
					'label' => $this->getLabel($text_options, $info_options), 
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
			 * @description Core hash input prototype message (end-user).
			 * @placeholder label The hash input label.
			 * @tags core prototype input hash message end-user
			 * @example Only CRC32 hashes, given in hexadecimal notation, are allowed.
			 */
			return UText::localize(
				"Only {{label}} hashes, given in hexadecimal notation, are allowed.", 
				'core.prototypes.inputs.hash', $text_options, [
					'parameters' => ['label' => $this->getLabel($text_options, $info_options)]
				]
			);
		}
		
		//non-end-user
		/**
		 * @description Core hash input prototype message.
		 * @placeholder label The hash input label.
		 * @placeholder notations The supported hash notation entries.
		 * @tags core prototype input hash message non-end-user
		 * @example Only CRC32 hashes are allowed, which may be given using any of the following notations:
		 *  &#8226; Hexadecimal string (example: "a7fed3fa");
		 *  &#8226; Base64 encoded string (example: "p/7T+g==");
		 *  &#8226; URL-safe Base64 encoded string (example: "p_7T-g");
		 *  &#8226; Raw binary string.
		 */
		return UText::localize(
			"Only {{label}} hashes are allowed, which may be given using any of the following notations:\n{{notations}}", 
			'core.prototypes.inputs.hash', $text_options, [
				'parameters' => [
					'label' => $this->getLabel($text_options, $info_options), 
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
			case 'filters.raw':
				return $this->createFilter(Filters\Raw::class, $prototype_properties, $properties);
			case 'filters.base64':
				return $this->createFilter(Filters\Base64::class, $prototype_properties, $properties);
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
		$example_value = random_bytes($this->getBits() / 8);
		
		//strings
		if ($text_options->info_scope !== EInfoScope::ENDUSER) {
			/**
			 * @description Core hash input prototype hexadecimal notation string.
			 * @placeholder example The hash example in hexadecimal notation.
			 * @tags core prototype input hash notation string non-end-user
			 * @example Hexadecimal string (example: "a7fed3fa")
			 */
			$strings[] = UText::localize("Hexadecimal string (example: {{example}})", 'core.prototypes.inputs.hash', $text_options, [
				'parameters' => ['example' => UText::stringify(bin2hex($example_value), $text_options)]
			]);
			/**
			 * @description Core hash input prototype Base64 encoded notation string.
			 * @placeholder example The hash example in Base64 encoded notation.
			 * @tags core prototype input hash notation string non-end-user
			 * @example Base64 encoded string (example: "p/7T+g==")
			 */
			$strings[] = UText::localize("Base64 encoded string (example: {{example}})", 'core.prototypes.inputs.hash', $text_options, [
				'parameters' => ['example' => UText::stringify(UBase64::encode($example_value), $text_options)]
			]);
			/**
			 * @description Core hash input prototype URL-safe Base64 encoded notation string.
			 * @placeholder example The hash example in URL-safe Base64 encoded notation.
			 * @tags core prototype input hash notation string non-end-user
			 * @example URL-safe Base64 encoded string (example: "p_7T-g")
			 */
			$strings[] = UText::localize("URL-safe Base64 encoded string (example: {{example}})", 'core.prototypes.inputs.hash', $text_options, [
				'parameters' => ['example' => UText::stringify(UBase64::encode($example_value, true), $text_options)]
			]);
			/**
			 * @description Core hash input prototype raw binary notation string.
			 * @tags core prototype input hash notation string non-end-user
			 */
			$strings[] = UText::localize("Raw binary string", 'core.prototypes.inputs.hash', $text_options);
		}
		
		//return
		return $strings;
	}
}
