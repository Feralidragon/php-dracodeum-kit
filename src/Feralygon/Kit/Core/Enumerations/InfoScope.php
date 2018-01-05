<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Enumerations;

use Feralygon\Kit\Core\Enumeration;
use Feralygon\Kit\Core\Utilities\Text as UText;
use Feralygon\Kit\Core\Options\Text as TextOptions;

/**
 * Core info scope enumeration class.
 * 
 * This enumeration represents info scopes, which are used to define which kind of information to return depending on the targetted scope.
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
	
	
	
	//Implemented protected static methods (core enumeration information trait)
	/** {@inheritdoc} */
	protected static function retrieveLabel(string $name, TextOptions $text_options) : ?string
	{
		switch ($name) {
			case 'NONE':
				/**
				 * @description Core info scope enumeration "NONE" label.
				 * @tags core enumeration info scope label
				 */
				return UText::localize("None", 'core.enumerations.info_scope', $text_options);
			case 'TECHNICAL':
				/**
				 * @description Core info scope enumeration "TECHNICAL" label.
				 * @tags core enumeration info scope label
				 */
				return UText::localize("Technical", 'core.enumerations.info_scope', $text_options);
			case 'ENDUSER':
				/**
				 * @description Core info scope enumeration "ENDUSER" label.
				 * @tags core enumeration info scope label
				 */
				return UText::localize("End-user", 'core.enumerations.info_scope', $text_options);
		}
		return null;
	}
	
	/** {@inheritdoc} */
	protected static function retrieveDescription(string $name, TextOptions $text_options) : ?string
	{
		switch ($name) {
			case 'NONE':
				/**
				 * @description Core info scope enumeration "NONE" description.
				 * @tags core enumeration info scope description
				 */
				return UText::localize("No info scope specified.", 'core.enumerations.info_scope', $text_options);
			case 'TECHNICAL':
				/**
				 * @description Core info scope enumeration "TECHNICAL" description.
				 * @tags core enumeration info scope description
				 */
				return UText::localize("Technical info scope, for the developer creating the application.", 'core.enumerations.info_scope', $text_options);
			case 'ENDUSER':
				/**
				 * @description Core info scope enumeration "ENDUSER" description.
				 * @tags core enumeration info scope description
				 */
				return UText::localize("End-user info scope, for the user interacting with the application.", 'core.enumerations.info_scope', $text_options);
		}
		return null;
	}
}
