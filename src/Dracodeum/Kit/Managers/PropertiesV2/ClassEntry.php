<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Managers\PropertiesV2;

/** @internal */
final class ClassEntry
{
	//Public properties
	/** @var array<string,\Dracodeum\Kit\Managers\PropertiesV2\Property> */
	public array $properties;
	
	/** @var array<string,int> */
	public array $required_maps = [];
	
	
	
	//Final public magic methods
	/**
	 * Instantiate class.
	 * 
	 * @param array<string,\Dracodeum\Kit\Managers\PropertiesV2\Property> $properties
	 * The property instances to instantiate with, as a set of `name => property` pairs.
	 */
	final public function __construct(array $properties)
	{
		$this->properties = $properties;
	}
}
