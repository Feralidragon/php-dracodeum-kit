<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Primitives\Vector\Exceptions;

use Feralygon\Kit\Primitives\Vector\Exception;

/**
 * This exception is thrown from a vector whenever no value is set at a given index.
 * 
 * @since 1.0.0
 * @property-read int $index [strict]
 * <p>The index.<br>
 * It must be greater than or equal to <code>0</code>.</p>
 */
class ValueNotSet extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		return "No value set at index {{index}} in vector {{vector}}.";
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		//parent
		parent::loadProperties();
		
		//properties
		$this->addProperty('index')->setAsStrictInteger(true);
	}
}
