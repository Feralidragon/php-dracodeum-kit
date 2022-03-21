<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Attributes\Property;

use Dracodeum\Kit\Managers\PropertiesV2\Interfaces\Attribute\Property\PropertyPostInitializer as IPropertyPostInitializer;
use Dracodeum\Kit\Managers\PropertiesV2\Property;
use Dracodeum\Kit\Components\Type as TypeComponent;
use Attribute;

/**
 * This attribute defines the property type as a `Dracodeum\Kit\Components\Type` component, using a given prototype, 
 * as a class or name, and a set of properties.  
 * If no prototype is given, then it's automatically retrieved from the property declared type.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
abstract class type implements IPropertyPostInitializer
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
	
	
	
	//Implemented final public methods (Dracodeum\Kit\Managers\PropertiesV2\Interfaces\PropertyPostInitializer)
	/** {@inheritdoc} */
	final public function postInitializeProperty(Property $property): void
	{
		$prototype = $this->prototype;
		$properties = ['strict' => $this->isStrict()] + $this->properties;
		if ($prototype !== null) {
			$property->setType(TypeComponent::build($prototype, $properties));
		} else {
			$property->setTypeByReflection($properties);
		}
	}
}
