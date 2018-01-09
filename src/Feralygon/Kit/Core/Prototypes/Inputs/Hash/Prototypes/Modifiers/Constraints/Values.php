<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Prototypes\Inputs\Hash\Prototypes\Modifiers\Constraints;

use Feralygon\Kit\Core\Prototypes\Input\Prototypes\Modifiers\Constraints;
use Feralygon\Kit\Core\Options\Text as TextOptions;
use Feralygon\Kit\Core\Utilities\{
	Text as UText,
	Type as UType
};

/**
 * Core hash input values constraint modifier prototype class.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Core\Prototypes\Inputs\Hash
 */
class Values extends Constraints\Values
{
	//Overridden public methods
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options) : string
	{
		if ($this->negate) {
			/**
			 * @description Core hash input values constraint modifier prototype label (negate).
			 * @tags core prototype input hash modifier constraint values label
			 */
			return UText::plocalize(
				"Disallowed hash", "Disallowed hashes",
				count($this->values), null,
				'core.prototypes.inputs.hash.prototypes.modifiers.constraints.values', $text_options
			);
		}
		/**
		 * @description Core hash input values constraint modifier prototype label.
		 * @tags core prototype input hash modifier constraint values label
		 */
		return UText::plocalize(
			"Allowed hash", "Allowed hashes",
			count($this->values), null,
			'core.prototypes.inputs.hash.prototypes.modifiers.constraints.values', $text_options
		);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options) : string
	{
		if ($this->negate) {
			/**
			 * @description Core hash input values constraint modifier prototype message (negate).
			 * @placeholder values The list of disallowed hash values.
			 * @tags core prototype input hash modifier constraint values message
			 * @example The following hashes are not allowed: "b9b183b8", "13bf50b8" and "ac5139b4".
			 */
			return UText::plocalize(
				"The following hash is not allowed: {{values}}.",
				"The following hashes are not allowed: {{values}}.",
				count($this->values), null,
				'core.prototypes.inputs.hash.prototypes.modifiers.constraints.values', $text_options, [
					'parameters' => ['values' => $this->getString($text_options)]
				]
			);
		}
		/**
		 * @description Core hash input values constraint modifier prototype message.
		 * @placeholder values The list of allowed hash values.
		 * @tags core prototype input hash modifier constraint values message
		 * @example Only the following hashes are allowed: "b9b183b8", "13bf50b8" and "ac5139b4".
		 */
		return UText::plocalize(
			"Only the following hash is allowed: {{values}}.",
			"Only the following hashes are allowed: {{values}}.",
			count($this->values), null,
			'core.prototypes.inputs.hash.prototypes.modifiers.constraints.values', $text_options, [
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
