<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Enumeration\Traits;

use Feralygon\Kit\Core\Options\Text as TextOptions;

/**
 * Core enumeration information trait.
 * 
 * This trait defines a set of methods to retrieve information from an enumeration, namely a label and a description.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Core\Enumeration
 */
trait Information
{
	//Protected static methods
	/**
	 * Retrieve label for a given enumerated element name.
	 * 
	 * @since 1.0.0
	 * @param string $name <p>The enumerated element name to retrieve for.</p>
	 * @param \Feralygon\Kit\Core\Options\Text $text_options <p>The text options instance to use.</p>
	 * @return string|null <p>The label for the given enumerated element name or <code>null</code> if none exists.</p>
	 */
	protected static function retrieveLabel(string $name, TextOptions $text_options) : ?string
	{
		return null;
	}
	
	/**
	 * Retrieve description for a given enumerated element name.
	 * 
	 * @since 1.0.0
	 * @param string $name <p>The enumerated element name to retrieve for.</p>
	 * @param \Feralygon\Kit\Core\Options\Text $text_options <p>The text options instance to use.</p>
	 * @return string|null <p>The description for the given enumerated element name or <code>null</code> if none exists.</p>
	 */
	protected static function retrieveDescription(string $name, TextOptions $text_options) : ?string
	{
		return null;
	}
}
