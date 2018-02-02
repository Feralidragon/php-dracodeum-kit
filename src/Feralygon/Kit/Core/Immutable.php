<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core;

use Feralygon\Kit\Core\Interfaces\Arrayable as IArrayable;

/**
 * Core immutable class.
 * 
 * This class is the base to be extended from when creating an immutable.<br>
 * <br>
 * An immutable object represents and stores multiple properties of multiple types,  
 * in such a way that none of them can ever be modified after instantiation (read-only).<br>
 * Each and every single one of its properties is validated and sanitized, guaranteeing its type and integrity, 
 * and may be retrieved directly just like any public object property.
 * 
 * @since 1.0.0
 * @see https://en.wikipedia.org/wiki/Immutable_object
 */
abstract class Immutable implements \ArrayAccess, IArrayable
{
	//Traits
	use Traits\Properties\ArrayableAccess;
	
	
	
	//Final public magic methods
	/**
	 * Instantiate class.
	 * 
	 * @since 1.0.0
	 * @param array $properties [default = []] <p>The properties, as <samp>name => value</samp> pairs.</p>
	 */
	final public function __construct(array $properties = [])
	{
		$this->initializeProperties($properties, \Closure::fromCallable([$this, 'loadProperties']), 'r');
	}
	
	
	
	//Abstract protected methods	
	/**
	 * Load properties.
	 * 
	 * @since 1.0.0
	 * @return void
	 */
	abstract protected function loadProperties() : void;
}
