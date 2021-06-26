<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Attributes\Property;

use Dracodeum\Kit\Managers\PropertiesV2\Interfaces\PropertyInitializer as IPropertyInitializer;
use Dracodeum\Kit\Managers\PropertiesV2\Property;
use Dracodeum\Kit\Components\Type as TypeComponent;
use ReflectionNamedType;
use ReflectionUnionType;
use Attribute;

/**
 * This attribute defines the property type as a `Dracodeum\Kit\Components\Type` component, using a given prototype, 
 * as a class or name, and a set of properties.  
 * If no prototype is given, then it's automatically retrieved from the property declared type.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
abstract class type implements IPropertyInitializer
{
	//Private properties
	private ?string $prototype;
	
	private array $properties;
	
	
	
	//Final public magic methods
	/**
	 * Instantiate class.
	 * 
	 * @param string|null $prototype
	 * The prototype class or name to instantiate with.
	 * 
	 * @param mixed $properties
	 * The properties to instantiate with.
	 */
	final public function __construct(?string $prototype = null, ...$properties)
	{
		$this->prototype = $prototype;
		$this->properties = $properties;
	}
	
	
	
	//Abstract protected methods
	/**
	 * Check if is strict.
	 * 
	 * @return bool
	 * Boolean `true` if is strict.
	 */
	abstract protected function isStrict(): bool;
	
	
	
	//Implemented final public methods (Dracodeum\Kit\Managers\PropertiesV2\Interfaces\PropertyInitializer)
	/** {@inheritdoc} */
	final public function initializeProperty(Property $property): void
	{
		//prototype
		$prototype = $this->prototype;
		if ($prototype === null) {
			$r_type = $property->getReflection()->getType();
			if ($r_type instanceof ReflectionNamedType) {
				$prototype = $r_type->getName();
			} elseif ($r_type instanceof ReflectionUnionType) {
				$r_type_names = [];
				foreach ($r_type->getTypes() as $r_named_type) {
					$r_type_names[] = $r_named_type->getName();
				}
				$prototype = implode('|', $r_type_names);
			}
		}
		
		//properties
		$properties = ['strict' => $this->isStrict()] + $this->properties;
		
		//finalize
		if ($prototype !== null) {
			$property->setType(TypeComponent::build($prototype, $properties));
		}
	}
}
