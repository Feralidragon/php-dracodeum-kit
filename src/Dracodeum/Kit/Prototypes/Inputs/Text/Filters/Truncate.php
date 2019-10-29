<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Prototypes\Inputs\Text\Filters;

use Dracodeum\Kit\Components\Input\Prototypes\Modifiers\Filters;
use Dracodeum\Kit\Options\Text as TextOptions;
use Dracodeum\Kit\Enumerations\InfoScope as EInfoScope;
use Dracodeum\Kit\Utilities\Text as UText;

class Truncate extends Filters\Truncate
{
	//Overridden public methods
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options): string
	{
		//technical
		if ($text_options->info_scope === EInfoScope::TECHNICAL) {
			/**
			 * @placeholder length The truncated length.
			 * @tags technical
			 * @example The string is truncated to 100 characters.
			 */
			return UText::plocalize(
				"The string is truncated to {{length}} character.",
				"The string is truncated to {{length}} characters.",
				$this->length, 'length', self::class, $text_options
			);
		}
		
		//non-technical
		/**
		 * @placeholder length The truncated length.
		 * @tags non-technical
		 * @example The text is truncated to 100 characters.
		 */
		return UText::plocalize(
			"The text is truncated to {{length}} character.",
			"The text is truncated to {{length}} characters.",
			$this->length, 'length', self::class, $text_options
		);
	}
}
