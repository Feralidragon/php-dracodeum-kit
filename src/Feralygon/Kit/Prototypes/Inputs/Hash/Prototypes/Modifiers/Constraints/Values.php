<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Prototypes\Inputs\Hash\Prototypes\Modifiers\Constraints;

use Feralygon\Kit\Components\Input\Prototypes\Modifiers\Constraints;
use Feralygon\Kit\Options\Text as TextOptions;
use Feralygon\Kit\Utilities\{
	Text as UText,
	Type as UType
};

/**
 * @since 1.0.0
 * @see \Feralygon\Kit\Prototypes\Inputs\Hash
 */
class Values extends Constraints\Values
{
	//Overridden public methods
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options) : string
	{
		return $this->negate
			? UText::plocalize(
				"Disallowed hash", "Disallowed hashes",
				count($this->values), null, self::class, $text_options
			)
			: UText::plocalize(
				"Allowed hash", "Allowed hashes",
				count($this->values), null, self::class, $text_options
			);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options) : string
	{
		if ($this->negate) {
			/**
			 * @placeholder values The list of disallowed hash values.
			 * @example The following hashes are not allowed: "b9b183b8", "13bf50b8" and "ac5139b4".
			 */
			return UText::plocalize(
				"The following hash is not allowed: {{values}}.",
				"The following hashes are not allowed: {{values}}.",
				count($this->values), null, self::class, $text_options, [
					'parameters' => ['values' => $this->getString($text_options)]
				]
			);
		}
		/**
		 * @placeholder values The list of allowed hash values.
		 * @example Only the following hashes are allowed: "b9b183b8", "13bf50b8" and "ac5139b4".
		 */
		return UText::plocalize(
			"Only the following hash is allowed: {{values}}.",
			"Only the following hashes are allowed: {{values}}.",
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
