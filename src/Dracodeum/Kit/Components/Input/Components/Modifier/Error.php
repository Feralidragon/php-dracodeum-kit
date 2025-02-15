<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components\Input\Components\Modifier;

/** @internal */
final class Error
{
	//Public properties
	/** @var mixed */
	public $value;
	
	
	
	//Final public magic methods
	/**
	 * Instantiate class.
	 * 
	 * @param mixed $value
	 * <p>The value to instantiate with.</p>
	 */
	final public function __construct($value)
	{
		$this->value = $value;
	}
}
