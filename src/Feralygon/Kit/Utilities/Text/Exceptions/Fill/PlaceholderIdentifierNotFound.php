<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Utilities\Text\Exceptions\Fill;

/**
 * This exception is thrown from the text utility <code>fill</code> method whenever 
 * a given placeholder identifier is not found.
 * 
 * @since 1.0.0
 * @property-read string $identifier
 * <p>The identifier.</p>
 * @property-read mixed $pointer
 * <p>The pointer.</p>
 */
class PlaceholderIdentifierNotFound extends InvalidPlaceholder
{
	//Overridden public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Placeholder {{placeholder}} identifier {{identifier}} not found in {{pointer}} in string {{string}}.";
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function loadProperties() : void
	{
		//parent
		parent::loadProperties();
		
		//properties
		$this->addProperty('identifier')->setAsString()->setAsRequired();
		$this->addProperty('pointer')->setAsRequired();
	}
}
