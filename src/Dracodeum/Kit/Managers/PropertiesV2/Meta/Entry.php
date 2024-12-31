<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Managers\PropertiesV2\Meta;

use Dracodeum\Kit\Components\Type;

final class Entry
{
	//Public properties
	public Type $type; //TODO: set as readonly (PHP 8.1)
	
	public mixed $default; //TODO: set as readonly (PHP 8.1)
	
	
	
	//Final public magic methods
	/**
	 * Instantiate class.
	 * 
	 * @param \Dracodeum\Kit\Components\Type $type
	 * The type instance to instantiate with.
	 * 
	 * @param mixed $default
	 * The default value to instantiate with.
	 */
	final public function __construct(Type $type, mixed $default)
	{
		$this->type = $type;
		$this->default = $default;
	}
}
