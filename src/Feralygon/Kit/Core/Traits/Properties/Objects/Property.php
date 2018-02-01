<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Traits\Properties\Objects;

/**
 * Core properties trait property object class.
 * 
 * @since 1.0.0
 * @internal
 * @see \Feralygon\Kit\Core\Traits\Properties
 */
final class Property
{
	//Public properties
	/** @var mixed */
	public $value = null;
	
	/** @var mixed */
	public $default_value = null;
	
	/** @var \Closure|null */
	public $evaluator = null;
	
	
	
	//Final public magic methods
	/**
	 * Instantiate class.
	 * 
	 * @since 1.0.0
	 * @param \Closure|null $evaluator [default = null] <p>The evaluator function.</p>
	 */
	final public function __construct(?\Closure $evaluator = null)
	{
		$this->evaluator = $evaluator;
	}
}
