<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Prototypes\Inputs\Text\Prototypes\Modifiers\Constraints;

use Feralygon\Kit\Core\Prototypes\Input\Prototypes\Modifiers\Constraints;
use Feralygon\Kit\Core\Options\Text as TextOptions;
use Feralygon\Kit\Core\Enumerations\InfoScope as EInfoScope;
use Feralygon\Kit\Core\Utilities\{
	Text as UText,
	Type as UType
};

/**
 * Core text input values constraint modifier prototype class.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Core\Prototypes\Inputs\Text
 */
class Values extends Constraints\Values
{
	//Overridden public methods
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options) : string
	{
		//negate
		if ($this->negate) {
			//technical
			if ($text_options->info_scope === EInfoScope::TECHNICAL) {
				/** @tags technical */
				return UText::plocalize("Disallowed string", "Disallowed strings", count($this->values), null, self::class, $text_options);
			}
			
			//non-technical
			/** @tags non-technical */
			return UText::plocalize("Disallowed text", "Disallowed texts", count($this->values), null, self::class, $text_options);
		}
		
		//technical
		if ($text_options->info_scope === EInfoScope::TECHNICAL) {
			/** @tags technical */
			return UText::plocalize("Allowed string", "Allowed strings", count($this->values), null, self::class, $text_options);
		}
		
		//non-technical
		/** @tags non-technical */
		return UText::plocalize("Allowed text", "Allowed texts", count($this->values), null, self::class, $text_options);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options) : string
	{
		//negate
		if ($this->negate) {
			//technical
			if ($text_options->info_scope === EInfoScope::TECHNICAL) {
				/**
				 * @placeholder values The list of disallowed text values.
				 * @tags technical
				 * @example The following strings are not allowed: "foo", "bar" and "abc".
				 */
				return UText::plocalize(
					"The following string is not allowed: {{values}}.",
					"The following strings are not allowed: {{values}}.",
					count($this->values), null, self::class, $text_options, [
						'parameters' => ['values' => $this->getString($text_options)]
					]
				);
			}
			
			//non-technical
			/**
			 * @placeholder values The list of disallowed text values.
			 * @tags non-technical
			 * @example The following texts are not allowed: "foo", "bar" and "abc".
			 */
			return UText::plocalize(
				"The following text is not allowed: {{values}}.",
				"The following texts are not allowed: {{values}}.",
				count($this->values), null, self::class, $text_options, [
					'parameters' => ['values' => $this->getString($text_options)]
				]
			);
		}
		
		//technical
		if ($text_options->info_scope === EInfoScope::TECHNICAL) {
			/**
			 * @placeholder values The list of allowed text values.
			 * @tags technical
			 * @example Only the following strings are allowed: "foo", "bar" and "abc".
			 */
			return UText::plocalize(
				"Only the following string is allowed: {{values}}.",
				"Only the following strings are allowed: {{values}}.",
				count($this->values), null, self::class, $text_options, [
					'parameters' => ['values' => $this->getString($text_options)]
				]
			);
		}
		
		//non-technical
		/**
		 * @placeholder values The list of allowed text values.
		 * @tags non-technical
		 * @example Only the following texts are allowed: "foo", "bar" and "abc".
		 */
		return UText::plocalize(
			"Only the following text is allowed: {{values}}.",
			"Only the following texts are allowed: {{values}}.",
			count($this->values), null, self::class, $text_options, [
				'parameters' => ['values' => $this->getString($text_options)]
			]
		);
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function evaluateValue(&$value) : bool
	{
		return UType::evaluateString($value);
	}
}
