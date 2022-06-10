<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Traits;

use Dracodeum\Kit\Managers\PropertiesV2 as Manager;
use Dracodeum\Kit\Utilities\Call as UCall;

/**
 * This trait handles and extends the properties of a class by using a `Dracodeum\Kit\Managers\PropertiesV2` manager.
 * 
 * @see \Dracodeum\Kit\Managers\PropertiesV2
 */
trait PropertiesV2
{
	//Private properties
	private Manager $properties_manager;
	
	
	
	//Final public magic methods
	/**
	 * Get value of a property with a given name.
	 * 
	 * @param string $name
	 * The name of the property to get from.
	 * 
	 * @return mixed
	 * The value of the property with the given name.
	 */
	final public function __get(string $name): mixed
	{
		return $this->properties_manager->get($name, UCall::stackPreviousClass());
	}
	
	/**
	 * Check if a property with a given name is set (exists, is initialized and is not `null`).
	 * 
	 * @param string $name
	 * The name of the property to check.
	 * 
	 * @return bool
	 * Boolean `true` if the property with the given name is set (exists, is initialized and is not `null`).
	 */
	final public function __isset(string $name): bool
	{
		return $this->properties_manager->isset($name, UCall::stackPreviousClass());
	}
	
	/**
	 * Set value into a property with a given name.
	 * 
	 * @param string $name
	 * The name of the property to set into.
	 * 
	 * @param mixed $value
	 * The value to set.
	 */
	final public function __set(string $name, mixed $value): void
	{
		$this->properties_manager->set($name, $value, UCall::stackPreviousClass());
	}
	
	/**
	 * Unset property value.
	 * 
	 * @param string $name
	 * The name of the property to unset.
	 */
	final public function __unset(string $name): void
	{
		$this->properties_manager->unset($name, UCall::stackPreviousClass());
	}
	
	
	
	//Final protected methods
	/**
	 * Initialize properties manager.
	 * 
	 * @param array $values
	 * The values to initialize with, as a set of `name => value` pairs.  
	 * Values corresponding to required properties may also be given as a non-associative array, with the given values 
	 * following the same order as their corresponding property declarations.
	 * 
	 * @return \Dracodeum\Kit\Managers\PropertiesV2
	 * The initialized properties manager instance.
	 */
	final protected function initializePropertiesManager(array $values = []): Manager
	{
		$manager = $this->getPropertiesManager();
		if ($manager->isInitialized()) {
			UCall::halt(['error_message' => "Properties have already been initialized."]);
		}
		$manager->initialize($values, UCall::stackPreviousClass(UCall::stackPreviousName() === '__construct' ? 1 : 0));
		return $manager;
	}
	
	/**
	 * Get properties manager instance.
	 * 
	 * @return \Dracodeum\Kit\Managers\PropertiesV2
	 * The properties manager instance.
	 */
	final protected function getPropertiesManager(): Manager
	{
		if (!isset($this->properties_manager)) {
			$this->properties_manager = new Manager($this, self::class);
		}
		return $this->properties_manager;
	}
}
