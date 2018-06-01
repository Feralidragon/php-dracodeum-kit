<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Utilities\Text\Exceptions\Fill;

/**
 * This exception is thrown from the text utility <code>fill</code> method whenever 
 * a given placeholder identifier is invalid.
 * 
 * @since 1.0.0
 * @property-read string $identifier
 * <p>The identifier.</p>
 * @property-read mixed $pointer
 * <p>The pointer.</p>
 */
class InvalidPlaceholderIdentifier extends InvalidPlaceholder
{
	//Overridden public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Invalid identifier {{identifier}} for {{pointer}} in placeholder {{placeholder}} " . 
			"in string {{string}}.\n" . 
			"HINT: The corresponding pointer must be an array or object.";
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function loadProperties() : void
	{
		//parent
		parent::loadProperties();
		
		//properties
		$this->addProperty('identifier')->setAsString();
		$this->addProperty('pointer');
	}
}
