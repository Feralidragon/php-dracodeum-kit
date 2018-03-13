<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Prototypes\Inputs;

use Feralygon\Kit\Prototypes\Input;
use Feralygon\Kit\Prototypes\Input\Interfaces\Information as IInformation;
use Feralygon\Kit\Options\Text as TextOptions;
use Feralygon\Kit\Components\Input\Options\Info as InfoOptions;
use Feralygon\Kit\Utilities\{
	Text as UText,
	Type as UType
};

/**
 * This input prototype represents a boolean.
 * 
 * Only the following types of values may be evaluated as a boolean:<br>
 * &nbsp; &#8226; &nbsp; a boolean;<br>
 * &nbsp; &#8226; &nbsp; an integer, with <code>0</code> as boolean <code>false</code>, 
 * and <code>1</code> as boolean <code>true</code>;<br>
 * &nbsp; &#8226; &nbsp; a float, with <code>0.0</code> as boolean <code>false</code>, 
 * and <code>1.0</code> as boolean <code>true</code>;<br>
 * &nbsp; &#8226; &nbsp; a string, with <code>"0"</code>, <code>"f"</code>, <code>"false"</code>, 
 * <code>"off"</code> and <code>"no"</code> as boolean <code>false</code>, 
 * and <code>"1"</code>, <code>"t"</code>, <code>"true"</code>, 
 * <code>"on"</code> and <code>"yes"</code> as boolean <code>true</code>.
 * 
 * @since 1.0.0
 * @see https://en.wikipedia.org/wiki/Boolean
 */
class Boolean extends Input implements IInformation
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getName() : string
	{
		return 'boolean';
	}
	
	/** {@inheritdoc} */
	public function evaluateValue(&$value) : bool
	{
		return UType::evaluateBoolean($value);
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Prototypes\Input\Interfaces\Information)
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options, InfoOptions $info_options) : string
	{
		return UText::localize("Boolean", self::class, $text_options);
	}
	
	/** {@inheritdoc} */
	public function getDescription(TextOptions $text_options, InfoOptions $info_options) : string
	{
		/**
		 * @placeholder values.false The list of possible allowed values which evaluate to boolean false.
		 * @placeholder values.true The list of possible allowed values which evaluate to boolean true.
		 * @example A boolean, which may be given as "0", "f", "false", "off" and "no" as boolean false, \
		 * and "1", "t", "true", "on" and "yes" as boolean true.
		 */
		return UText::localize(
			"A boolean, which may be given as {{values.false}} as boolean false, and {{values.true}} as boolean true.",
			self::class, $text_options, [
				'parameters' => [
					'values' => [
						'false' => $this->getFalseValueStrings($text_options), 
						'true' => $this->getTrueValueStrings($text_options)
					]
				],
				'string_options' => [
					'quote_strings' => true,
					'non_assoc_mode' => UText::STRING_NONASSOC_MODE_COMMA_LIST_AND
				]
			]
		);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options, InfoOptions $info_options) : string
	{
		/**
		 * @placeholder values.false The list of possible allowed values which evaluate to boolean false.
		 * @placeholder values.true The list of possible allowed values which evaluate to boolean true.
		 * @example Only a boolean is allowed, \
		 * which may be given as "0", "f", "false", "off" and "no" as boolean false, \
		 * and "1", "t", "true", "on" and "yes" as boolean true.
		 */
		return UText::localize(
			"Only a boolean is allowed, which may be given as {{values.false}} as boolean false, " . 
				"and {{values.true}} as boolean true.", 
			self::class, $text_options, [
				'parameters' => [
					'values' => [
						'false' => $this->getFalseValueStrings($text_options), 
						'true' => $this->getTrueValueStrings($text_options)
					]
				],
				'string_options' => [
					'quote_strings' => true,
					'non_assoc_mode' => UText::STRING_NONASSOC_MODE_COMMA_LIST_AND
				]
			]
		);
	}
	
	
	
	//Protected methods
	/**
	 * Get <code>false</code> value strings.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Options\Text $text_options
	 * <p>The text options instance to use.</p>
	 * @return string[]
	 * <p>The <code>false</code> value strings.</p>
	 */
	protected function getFalseValueStrings(TextOptions $text_options) : array
	{
		return ['0', 'f', 'false', 'off', 'no'];
	}
	
	/**
	 * Get <code>true</code> value strings.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Options\Text $text_options
	 * <p>The text options instance to use.</p>
	 * @return string[]
	 * <p>The <code>true</code> value strings.</p>
	 */
	protected function getTrueValueStrings(TextOptions $text_options) : array
	{
		return ['1', 't', 'true', 'on', 'yes'];
	}
}
