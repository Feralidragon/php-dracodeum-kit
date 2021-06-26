<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Managers\PropertiesV2;

use ReflectionProperty as Reflection;
use Dracodeum\Kit\Components\Type;

final class Property
{
	//Private properties
	private Reflection $reflection;
	
	private ?Type $type = null;
	
	
	
	//Final public magic methods
	/**
	 * Instantiate class.
	 * 
	 * @param \ReflectionProperty $reflection
	 * The reflection instance to instantiate with.
	 */
	final public function __construct(Reflection $reflection)
	{
		$this->reflection = $reflection;
	}
	
	
	
	//Final public methods
	/**
	 * Get reflection instance.
	 * 
	 * @return \ReflectionProperty
	 * The reflection instance.
	 */
	final public function getReflection(): Reflection
	{
		return $this->reflection;
	}
	
	/**
	 * Get type instance.
	 * 
	 * @return \Dracodeum\Kit\Components\Type|null
	 * The type instance, or `null` if none is set.
	 */
	final public function getType(): ?Type
	{
		return $this->type;
	}
	
	/**
	 * Set type instance.
	 * 
	 * @param \Dracodeum\Kit\Components\Type $type
	 * The type instance to set.
	 * 
	 * @return $this
	 * This instance, for chaining purposes.
	 */
	final public function setType(Type $type)
	{
		$this->type = $type;
		return $this;
	}
}
