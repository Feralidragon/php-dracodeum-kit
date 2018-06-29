<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Prototypes\Inputs\Numbers\Size\Prototypes\Modifiers\Constraints;

use Feralygon\Kit\Prototypes\Inputs\Number\Prototypes\Modifiers\Constraints;
use Feralygon\Kit\Options\Text as TextOptions;
use Feralygon\Kit\Utilities\{
	Byte as UByte,
	Text as UText
};

/**
 * @since 1.0.0
 * @see \Feralygon\Kit\Prototypes\Inputs\Numbers\Size
 */
class Maximum extends Constraints\Maximum
{
	//Overridden public methods
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options): string
	{
		return UText::localize("Maximum allowed size", self::class, $text_options);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options): string
	{
		$value_string = $this->stringifyValue($this->value, $text_options);
		if ($this->exclusive) {
			/**
			 * @placeholder value The maximum allowed value.
			 * @example Only a size less than 250 MB is allowed.
			 */
			return UText::localize(
				"Only a size less than {{value}} is allowed.",
				self::class, $text_options, ['parameters' => ['value' => $value_string]]
			);
		}
		/**
		 * @placeholder value The maximum allowed value.
		 * @example Only a size less than or equal to 250 MB is allowed.
		 */
		return UText::localize(
			"Only a size less than or equal to {{value}} is allowed.",
			self::class, $text_options, ['parameters' => ['value' => $value_string]]
		);
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function evaluateValue(&$value): bool
	{
		return UByte::evaluateSize($value);
	}
	
	/** {@inheritdoc} */
	protected function stringifyValue($value, TextOptions $text_options): string
	{
		return UByte::hvalue($value);
	}
}
