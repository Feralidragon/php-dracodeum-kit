<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core;

use Feralygon\Kit\Core\Prototype\{
	Exceptions,
	Interfaces
};

/**
 * Core prototype class.
 * 
 * This class is the base to be extended from when creating a prototype.<br>
 * For more information, please check the <code>\Feralygon\Kit\Core\Component</code> class.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Core\Component
 * @see \Feralygon\Kit\Core\Prototype\Interfaces\Properties
 * @see \Feralygon\Kit\Core\Prototype\Interfaces\Functions
 * @see \Feralygon\Kit\Core\Prototype\Interfaces\Initialization
 */
abstract class Prototype implements \ArrayAccess
{
	//Traits
	use Traits\ExtendedPropertiesArrayAccess;
	use Traits\Functions;
	
	
	
	//Final public magic methods
	/**
	 * Instantiate class.
	 *
	 * @since 1.0.0
	 * @param array $properties [default = []] <p>The properties, as <code>name => value</code> pairs.</p>
	 * @throws \Feralygon\Kit\Core\Prototype\Exceptions\PropertiesNotImplemented
	 */
	final public function __construct(array $properties = [])
	{
		//properties
		if ($this instanceof Interfaces\Properties) {
			$this->initializeProperties($properties, [$this, 'buildProperty'], $this->getRequiredPropertyNames());
		} elseif (!empty($properties)) {
			throw new Exceptions\PropertiesNotImplemented(['prototype' => $this]);
		}
		
		//functions
		if ($this instanceof Interfaces\Functions) {
			$this->initializeFunctions([$this, 'getFunctionTemplate'], true);
		}
		
		//initialization
		if ($this instanceof Interfaces\Initialization) {
			$this->initialize();
		}
	}
}
