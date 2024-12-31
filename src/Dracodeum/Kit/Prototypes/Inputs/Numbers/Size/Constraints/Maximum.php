<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Prototypes\Inputs\Numbers\Size\Constraints;

use Dracodeum\Kit\Prototypes\Inputs\Number\Constraints;
use Dracodeum\Kit\Options\Text as TextOptions;
use Dracodeum\Kit\Utilities\{
	Byte as UByte,
	Text as UText
};

class Maximum extends Constraints\Maximum
{
	//Overridden public methods
	/** {@inheritdoc} */
	public function getSubtype(): string
	{
		return 'size';
	}
	
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options): string
	{
		return UText::localize("Maximum allowed size", self::class, $text_options);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options): string
	{
		//initialize
		$value_string = $this->stringifyValue($this->value, $text_options);
		
		//exclusive
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
		
		//default
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
