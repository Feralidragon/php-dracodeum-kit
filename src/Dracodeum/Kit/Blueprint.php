<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit;

use Dracodeum\Kit\Traits;
use Dracodeum\Kit\Attributes\Property\Ignore;

/**
 * This class is the base to be extended from when creating a blueprint.
 * 
 * @see \Dracodeum\Kit\ComponentV2
 */
abstract class Blueprint
{
	//Traits
	use Traits\PropertiesV2;
	
	
	
	//Public properties
	#[Ignore]
	public readonly ComponentV2 $component;
	
	
	
	//Final public magic methods
	/**
	 * Instantiate class.
	 * 
	 * @param \Dracodeum\Kit\ComponentV2 $component
	 * The component instance to instantiate with.
	 * 
	 * @param mixed $properties
	 * The properties to instantiate with.
	 */
	final public function __construct(ComponentV2 $component, ...$properties)
	{
		$this->component = $component;
		$this->initializePropertiesManager($properties);
	}
}
