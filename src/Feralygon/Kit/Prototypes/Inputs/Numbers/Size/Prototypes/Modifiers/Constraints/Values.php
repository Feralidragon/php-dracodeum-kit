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
class Values extends Constraints\Values
{
	//Overridden public methods
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options): string
	{
		return $this->negate
			? UText::plocalize(
				"Disallowed size", "Disallowed sizes",
				count($this->values), null, self::class, $text_options
			)
			: UText::plocalize(
				"Allowed size", "Allowed sizes",
				count($this->values), null, self::class, $text_options
			);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options): string
	{
		//negate
		if ($this->negate) {
			/**
			 * @placeholder values The list of disallowed size values.
			 * @example The following sizes are not allowed: 3 B, 8 kB and 27 MB.
			 */
			return UText::plocalize(
				"The following size is not allowed: {{values}}.",
				"The following sizes are not allowed: {{values}}.",
				count($this->values), null, self::class, $text_options, [
					'parameters' => ['values' => $this->getString($text_options)]
				]
			);
		}
		
		//default
		/**
		 * @placeholder values The list of allowed size values.
		 * @example Only the following sizes are allowed: 3 B, 8 kB and 27 MB.
		 */
		return UText::plocalize(
			"Only the following size is allowed: {{values}}.",
			"Only the following sizes are allowed: {{values}}.",
			count($this->values), null, self::class, $text_options, [
				'parameters' => ['values' => $this->getString($text_options)]
			]
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
