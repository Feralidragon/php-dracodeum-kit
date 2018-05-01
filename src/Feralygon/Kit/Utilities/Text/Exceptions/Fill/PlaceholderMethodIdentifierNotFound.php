<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Utilities\Text\Exceptions\Fill;

/**
 * This exception is thrown from the text utility <code>fill</code> method whenever 
 * a given placeholder method identifier is not found.
 * 
 * @since 1.0.0
 */
class PlaceholderMethodIdentifierNotFound extends PlaceholderIdentifierNotFound
{
	//Overridden public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Placeholder {{placeholder}} method identifier {{identifier}} not found " . 
			"in {{pointer}} in string {{string}}.";
	}
}
