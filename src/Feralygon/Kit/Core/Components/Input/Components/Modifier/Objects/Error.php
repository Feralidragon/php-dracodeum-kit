<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Components\Input\Components\Modifier\Objects;

/**
 * Core input modifier component error object class.
 * 
 * @since 1.0.0
 * @internal
 * @see \Feralygon\Kit\Core\Components\Input\Components\Modifier
 */
final class Error
{
	//Public properties
	/** @var mixed */
	public $value;
	
	
	
	//Final public magic methods
	/**
	 * Instantiate class.
	 * 
	 * @since 1.0.0
	 * @param mixed $value <p>The value.</p>
	 */
	final public function __construct($value)
	{
		$this->value = $value;
	}
}
