<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Enumerations;

use Feralygon\Kit\Enumeration;
use Feralygon\Kit\Utilities\Text as UText;
use Feralygon\Kit\Options\Text as TextOptions;

/**
 * This enumeration represents info scopes, which are used to define which kind of information to return 
 * depending on the targetted scope.
 * 
 * @since 1.0.0
 */
class InfoScope extends Enumeration
{
	//Public constants
	/** No info scope specified. */
	public const NONE = 0;
	
	/** Technical info scope, for the developer creating the application. */
	public const TECHNICAL = 1;
	
	/** End-user info scope, for the user interacting with the application. */
	public const ENDUSER = 2;
	
	
	
	//Implemented protected static methods (Feralygon\Kit\Enumeration\Traits\Information)
	/** {@inheritdoc} */
	protected static function returnLabel(string $name, TextOptions $text_options): ?string
	{
		switch ($name) {
			case 'NONE':
				/** @description "NONE" label. */
				return UText::localize("None", self::class, $text_options);
			case 'TECHNICAL':
				/** @description "TECHNICAL" label. */
				return UText::localize("Technical", self::class, $text_options);
			case 'ENDUSER':
				/** @description "ENDUSER" label. */
				return UText::localize("End-user", self::class, $text_options);
		}
		return null;
	}
	
	/** {@inheritdoc} */
	protected static function returnDescription(string $name, TextOptions $text_options): ?string
	{
		switch ($name) {
			case 'NONE':
				/** @description "NONE" description. */
				return UText::localize(
					"No info scope specified.", self::class, $text_options
				);
			case 'TECHNICAL':
				/** @description "TECHNICAL" description. */
				return UText::localize(
					"Technical info scope, for the developer creating the application.", self::class, $text_options
				);
			case 'ENDUSER':
				/** @description "ENDUSER" description. */
				return UText::localize(
					"End-user info scope, for the user interacting with the application.", self::class, $text_options
				);
		}
		return null;
	}
}
