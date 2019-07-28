<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Prototypes\Inputs\Numbers\Size\Constraints;

use Feralygon\Kit\Prototypes\Inputs\Number\Constraints;
use Feralygon\Kit\Options\Text as TextOptions;
use Feralygon\Kit\Utilities\{
	Byte as UByte,
	Text as UText
};

/** @see \Feralygon\Kit\Prototypes\Inputs\Numbers\Size */
class Minimum extends Constraints\Minimum
{
	//Overridden public methods
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options): string
	{
		return UText::localize("Minimum allowed size", self::class, $text_options);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options): string
	{
		//initialize
		$value_string = $this->stringifyValue($this->value, $text_options);
		
		//exclusive
		if ($this->exclusive) {
			/**
			 * @placeholder value The minimum allowed value.
			 * @example Only a size greater than 250 kB is allowed.
			 */
			return UText::localize(
				"Only a size greater than {{value}} is allowed.",
				self::class, $text_options, ['parameters' => ['value' => $value_string]]
			);
		}
		
		//default
		/**
		 * @placeholder value The minimum allowed value.
		 * @example Only a size greater than or equal to 250 kB is allowed.
		 */
		return UText::localize(
			"Only a size greater than or equal to {{value}} is allowed.",
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
