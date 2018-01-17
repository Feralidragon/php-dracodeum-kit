<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Prototypes\Inputs\Text\Prototypes\Modifiers\Constraints;

use Feralygon\Kit\Core\Prototypes\Input\Prototypes\Modifiers\Constraints;
use Feralygon\Kit\Core\Options\Text as TextOptions;
use Feralygon\Kit\Core\Enumerations\InfoScope as EInfoScope;
use Feralygon\Kit\Core\Utilities\Text as UText;

/**
 * Core text input lowercase constraint modifier prototype class.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Core\Prototypes\Inputs\Text
 */
class Lowercase extends Constraints\Lowercase
{
	//Overridden public methods
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options) : string
	{
		if ($text_options->info_scope === EInfoScope::TECHNICAL) {
			/** @tags technical */
			return UText::localize("Only strings in lowercase are allowed.", self::class, $text_options);
		}
		/** @tags non-technical */
		return UText::localize("Only text in lowercase is allowed.", self::class, $text_options);
	}
}
