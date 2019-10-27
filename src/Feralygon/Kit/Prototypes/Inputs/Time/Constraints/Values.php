<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Prototypes\Inputs\Time\Constraints;

use Feralygon\Kit\Components\Input\Prototypes\Modifiers\Constraints;
use Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\Subtype as ISubtype;
use Feralygon\Kit\Options\Text as TextOptions;
use Feralygon\Kit\Utilities\{
	Text as UText,
	Time as UTime
};

class Values extends Constraints\Values implements ISubtype
{
	//Implemented public methods (Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\Subtype)
	/** {@inheritdoc} */
	public function getSubtype(): string
	{
		return 'time';
	}
	
	
	
	//Overridden public methods
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options): string
	{
		return $this->negate
			? UText::plocalize(
				"Disallowed time", "Disallowed times",
				count($this->values), null, self::class, $text_options
			)
			: UText::plocalize(
				"Allowed time", "Allowed times",
				count($this->values), null, self::class, $text_options
			);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options): string
	{
		//negate
		if ($this->negate) {
			/**
			 * @placeholder values The list of disallowed time values.
			 * @example The following times are not allowed: 03:00:00, 12:45:00 and 17:20:00.
			 */
			return UText::plocalize(
				"The following time is not allowed: {{values}}.",
				"The following times are not allowed: {{values}}.",
				count($this->values), null, self::class, $text_options, [
					'parameters' => ['values' => $this->getString($text_options)]
				]
			);
		}
		
		//default
		/**
		 * @placeholder values The list of allowed time values.
		 * @example Only the following times are allowed: 03:00:00, 12:45:00 or 17:20:00.
		 */
		return UText::plocalize(
			"Only the following time is allowed: {{values}}.",
			"Only the following times are allowed: {{values}}.",
			count($this->values), null, self::class, $text_options, [
				'parameters' => ['values' => $this->getString($text_options)]
			]
		);
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function evaluateValue(&$value): bool
	{
		return UTime::evaluateTime($value);
	}
	
	/** {@inheritdoc} */
	protected function stringifyValue($value, TextOptions $text_options): string
	{
		return UTime::stringifyTime($value, $text_options);
	}
}
