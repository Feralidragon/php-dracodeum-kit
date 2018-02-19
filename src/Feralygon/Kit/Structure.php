<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit;

use Feralygon\Kit\Interfaces\Arrayable as IArrayable;

/**
 * Structure class.
 * 
 * This class is the base to be extended from when creating a structure.<br>
 * <br>
 * A structure is a simple object which represents and stores multiple properties of multiple types.<br>
 * Each and every single one of its properties is validated and sanitized, guaranteeing its type and integrity, 
 * and may be retrieved and modified directly just like any public object property, 
 * and may also be set to read-only during instantiation to prevent any further changes.
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
	 * @param bool $readonly [default = false] <p>Set all properties as read-only.</p>
	 */
	final public function __construct(array $properties = [], bool $readonly = false)
	{
		$mode = $readonly ? 'r+' : 'rw';
		$this->initializeProperties(\Closure::fromCallable([$this, 'buildProperties']), $properties, $mode);
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
