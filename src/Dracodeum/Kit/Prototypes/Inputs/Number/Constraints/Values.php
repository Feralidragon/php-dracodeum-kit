<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Prototypes\Inputs\Number\Constraints;

use Dracodeum\Kit\Components\Input\Prototypes\Modifiers\Constraints;
use Dracodeum\Kit\Components\Input\Prototypes\Modifier\Interfaces\Subtype as ISubtype;
use Dracodeum\Kit\Options\Text as TextOptions;
use Dracodeum\Kit\Utilities\{
	Text as UText,
	Type as UType
};

class Values extends Constraints\Values implements ISubtype
{
	//Implemented public methods (Dracodeum\Kit\Components\Input\Prototypes\Modifier\Interfaces\Subtype)
	/** {@inheritdoc} */
	public function getSubtype(): string
	{
		return 'number';
	}
	
	
	
	//Overridden public methods
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options): string
	{
		return $this->negate
			? UText::plocalize(
				"Disallowed number", "Disallowed numbers",
				count($this->values), null, self::class, $text_options
			)
			: UText::plocalize(
				"Allowed number", "Allowed numbers",
				count($this->values), null, self::class, $text_options
			);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options): string
	{
		//negate
		if ($this->negate) {
			/**
			 * @placeholder values The list of disallowed number values.
			 * @example The following numbers are not allowed: 3, 8 and 27.
			 */
			return UText::plocalize(
				"The following number is not allowed: {{values}}.",
				"The following numbers are not allowed: {{values}}.",
				count($this->values), null, self::class, $text_options, [
					'parameters' => ['values' => $this->getString($text_options)]
				]
			);
		}
		
		//default
		/**
		 * @placeholder values The list of allowed number values.
		 * @example Only the following numbers are allowed: 3, 8 or 27.
		 */
		return UText::plocalize(
			"Only the following number is allowed: {{values}}.",
			"Only the following numbers are allowed: {{values}}.",
			count($this->values), null, self::class, $text_options, [
				'parameters' => ['values' => $this->getString($text_options)]
			]
		);
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function evaluateValue(&$value): bool
	{
		return UType::evaluateNumber($value);
	}
}
