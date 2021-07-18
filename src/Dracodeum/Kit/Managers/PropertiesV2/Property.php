<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Managers\PropertiesV2;

use ReflectionProperty as Reflection;
use Dracodeum\Kit\Components\Type;
use ReflectionNamedType;
use ReflectionUnionType;

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
	
	/**
	 * Set type by reflection.
	 * 
	 * @param array $properties
	 * The properties to set with.
	 * 
	 * @return $this
	 * This instance, for chaining purposes.
	 */
	final public function setTypeByReflection(array $properties)
	{
		//initialize
		$r_inner_types = [];
		$r_type = $this->reflection->getType();
		if ($r_type === null) {
			return $this;
		} elseif ($r_type instanceof ReflectionNamedType) {
			$r_inner_types[] = $r_type;
		} elseif ($r_type instanceof ReflectionUnionType) {
			$r_inner_types = $r_type->getTypes();
		}
		
		//properties
		$properties['nullable'] = $r_type->allowsNull();
		
		//inner
		$inner_types = [];
		$is_single_type = count($r_inner_types) === 1;
		foreach ($r_inner_types as $r_inner_type) {
			//initialize
			$inner_properties = [];
			$inner_prototype = $r_inner_type->getName();
			
			//prototype
			if ($inner_prototype === 'null') {
				continue;
			} elseif (class_exists($inner_prototype)) {
				$inner_properties['class'] = $inner_prototype;
				$inner_prototype = 'object';
			}
			
			//single
			if ($is_single_type) {
				$this->type = Type::build($inner_prototype, $inner_properties + $properties);
				return $this;
			}
			
			//append
			$inner_types[] = Type::build($inner_prototype, $inner_properties);
		}
		
		//finalize
		$this->type = Type::build('any', ['types' => $inner_types] + $properties);
		
		//return
		return $this;
	}
}
