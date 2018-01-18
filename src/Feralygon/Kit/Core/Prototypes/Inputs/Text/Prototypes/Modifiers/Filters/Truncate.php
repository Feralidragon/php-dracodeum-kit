<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Prototypes\Inputs\Text\Prototypes\Modifiers\Filters;

use Feralygon\Kit\Core\Prototypes\Input\Prototypes\Modifiers\Filters;
use Feralygon\Kit\Core\Options\Text as TextOptions;
use Feralygon\Kit\Core\Enumerations\InfoScope as EInfoScope;
use Feralygon\Kit\Core\Utilities\Text as UText;

/**
 * Core text input truncate filter modifier prototype class.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Core\Prototypes\Inputs\Text
 */
class Truncate extends Filters\Truncate
{
	//Overridden public methods
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options) : string
	{
		//technical
		if ($text_options->info_scope === EInfoScope::TECHNICAL) {
			/**
			 * @placeholder length The truncated length.
			 * @tags technical
			 * @example Strings are truncated to 100 characters.
			 */
			return UText::plocalize(
				"Strings are truncated to {{length}} character.",
				"Strings are truncated to {{length}} characters.",
				$this->length, 'length', self::class, $text_options
			);
		}
		
		//non-technical
		/**
		 * @placeholder length The truncated length.
		 * @tags non-technical
		 * @example Text is truncated to 100 characters.
		 */
		return UText::plocalize(
			"Text is truncated to {{length}} character.",
			"Text is truncated to {{length}} characters.",
			$this->length, 'length', self::class, $text_options
		);
	}
}
