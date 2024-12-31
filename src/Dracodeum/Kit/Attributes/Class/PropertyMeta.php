<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Attributes\Class;

use Dracodeum\Kit\Managers\PropertiesV2\Interfaces\Attribute\Class\MetaInitializer as IMetaInitializer;
use Dracodeum\Kit\Managers\PropertiesV2\Meta;
use Dracodeum\Kit\Components\Type;
use Attribute;

/**
 * This attribute defines a new meta entry for the properties of the class, with a default value, and with the type as 
 * a `Dracodeum\Kit\Components\Type` component, using a given prototype, as a class or name, and a set of properties.
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
final class PropertyMeta implements IMetaInitializer
{
	//Private properties
	private string $name;
	
	private string $type_prototype;
	
	private mixed $default;
	
	private array $type_properties;
	
	
	
	//Final public magic methods
	/**
	 * Instantiate class.
	 * 
	 * @param string $name
	 * The name to instantiate with.
	 * 
	 * @param string $type_prototype
	 * The type prototype class or name to instantiate with.
	 * 
	 * @param mixed $default
	 * The default to instantiate with.
	 * 
	 * @param mixed $type_properties
	 * The type properties to instantiate with.
	 */
	final public function __construct(string $name, string $type_prototype, mixed $default, ...$type_properties)
	{
		$this->name = $name;
		$this->type_prototype = $type_prototype;
		$this->default = $default;
		$this->type_properties = $type_properties;
	}
	
	
	
	//Implemented final public methods (Dracodeum\Kit\Managers\PropertiesV2\Interfaces\Attribute\Class\MetaInitializer)
	/** {@inheritdoc} */
	final public function initializeMeta(Meta $meta): void
	{
		$meta->set($this->name, Type::build($this->type_prototype, $this->type_properties), $this->default);
	}
}
