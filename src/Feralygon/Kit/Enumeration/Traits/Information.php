<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Enumeration\Traits;

use Feralygon\Kit\Options\Text as TextOptions;

/** This trait defines a set of methods to return information from an enumeration, namely the label and description. */
trait Information
{
	//Protected static methods
	/**
	 * Return label for a given element name.
	 * 
	 * @param string $name
	 * <p>The name to return for.</p>
	 * @param \Feralygon\Kit\Options\Text $text_options
	 * <p>The text options instance to use.</p>
	 * @return string|null
	 * <p>The label for the given element name or <code>null</code> if none is set.</p>
	 */
	protected static function returnLabel(string $name, TextOptions $text_options): ?string
	{
		return null;
	}
	
	/**
	 * Return description for a given element name.
	 * 
	 * @param string $name
	 * <p>The name to return for.</p>
	 * @param \Feralygon\Kit\Options\Text $text_options
	 * <p>The text options instance to use.</p>
	 * @return string|null
	 * <p>The description for the given element name or <code>null</code> if none is set.</p>
	 */
	protected static function returnDescription(string $name, TextOptions $text_options): ?string
	{
		return null;
	}
}
