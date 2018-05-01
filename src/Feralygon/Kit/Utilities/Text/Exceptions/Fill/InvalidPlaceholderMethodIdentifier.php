<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Utilities\Text\Exceptions\Fill;

/**
 * This exception is thrown from the text utility <code>fill</code> method whenever 
 * a given placeholder method identifier is invalid.
 * 
 * @since 1.0.0
 */
class InvalidPlaceholderMethodIdentifier extends InvalidPlaceholderIdentifier
{
	//Overridden public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Invalid method identifier {{identifier}} for {{pointer}} in placeholder {{placeholder}} " . 
			"in string {{string}}.\n" . 
			"HINT: The corresponding pointer must be an object.";
	}
}
