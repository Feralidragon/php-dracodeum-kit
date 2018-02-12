<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core;

use Feralygon\Kit\Core\Interfaces\Arrayable as IArrayable;

/**
 * Core structure class.
 * 
 * This class is the base to be extended from when creating a structure.<br>
 * <br>
 * A structure is a simple object which represents and stores multiple properties of multiple types.<br>
 * Each and every single one of its properties is validated and sanitized, guaranteeing its type and integrity, 
 * and may be retrieved and modified directly just like any public object property.
 * 
 * @since 1.0.0
 * @see https://en.wikipedia.org/wiki/Struct_(C_programming_language)
 */
abstract class Structure implements \ArrayAccess, IArrayable
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
		$this->initializeProperties(\Closure::fromCallable([$this, 'buildProperties']), $properties);
	}
	
	
	
	//Abstract protected methods
	/**
	 * Build properties.
	 * 
	 * @since 1.0.0
	 * @return void
	 */
	abstract protected function buildProperties() : void;
}
