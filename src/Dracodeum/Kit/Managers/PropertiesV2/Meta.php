<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Managers\PropertiesV2;

use Dracodeum\Kit\Managers\PropertiesV2\Meta\{
	Entry,
	Exceptions
};
use Dracodeum\Kit\Components\Type;
use Dracodeum\Kit\Primitives\Error;

final class Meta
{
	//Private properties
	private string $class;
	
	/** @var array<string,\Dracodeum\Kit\Managers\PropertiesV2\Meta\Entry> */
	private array $entries = [];
	
	
	
	//Final public magic methods
	/**
	 * Instantiate class.
	 * 
	 * @param string $class
	 * The class to instantiate with.
	 */
	final public function __construct(string $class)
	{
		$this->class = $class;
	}
	
	
	
	//Final public methods
	/**
	 * Get class.
	 * 
	 * @return string
	 * The class.
	 */
	final public function getClass(): string
	{
		return $this->class;
	}
	
	/**
	 * Check if has an entry with a given name.
	 * 
	 * @param string $name
	 * The entry name to check.
	 * 
	 * @return bool
	 * Boolean `true` if has the entry with the given name.
	 */
	final public function has(string $name): bool
	{
		return isset($this->entries[$name]);
	}
	
	/**
	 * Get entry instance with a given name.
	 * 
	 * @param string $name
	 * The name of the entry to get.
	 * 
	 * @throws \Dracodeum\Kit\Managers\PropertiesV2\Meta\Exceptions\Undefined
	 * 
	 * @return \Dracodeum\Kit\Managers\PropertiesV2\Meta\Entry
	 * The entry instance with the given name.
	 */
	final public function get(string $name): Entry
	{
		if (!isset($this->entries[$name])) {
			throw new Exceptions\Undefined([$this->class, $name]);
		}
		return $this->entries[$name];
	}
	
	/**
	 * Set entry with a given name.
	 * 
	 * @param string $name
	 * The name of the entry to set.
	 * 
	 * @param \Dracodeum\Kit\Components\Type $type
	 * The type instance to set.
	 * 
	 * @param mixed $default
	 * The default value to set.
	 * 
	 * @throws \Dracodeum\Kit\Managers\PropertiesV2\Meta\Exceptions\Defined
	 * @throws \Dracodeum\Kit\Managers\PropertiesV2\Meta\Exceptions\InvalidDefault
	 * 
	 * @return $this
	 * This instance, for chaining purposes.
	 */
	final public function set(string $name, Type $type, mixed $default)
	{
		//check
		if (isset($this->entries[$name])) {
			throw new Exceptions\Defined([$this->class, $name]);
		}
		
		//default
		$error = $type->process($default);
		if ($error !== null) {
			throw new Exceptions\InvalidDefault([$this->class, $name, $default, $error]);
		}
		
		//set
		$this->entries[$name] = new Entry($type, $default);
		
		//return
		return $this;
	}
	
	/**
	 * Process a given value using an entry with a given name.
	 * 
	 * @param string $name
	 * The name of the entry to use.
	 * 
	 * @param mixed $value
	 * The value to process.
	 * 
	 * @throws \Dracodeum\Kit\Managers\PropertiesV2\Meta\Exceptions\Undefined
	 * 
	 * @return \Dracodeum\Kit\Primitives\Error|null
	 * An error instance if the given value failed to be processed, or `null` if otherwise.
	 */
	final public function process(string $name, mixed &$value): ?Error
	{
		if (!isset($this->entries[$name])) {
			throw new Exceptions\Undefined([$this->class, $name]);
		}
		return $this->entries[$name]->type->process($value);
	}
	
	/**
	 * Clone into a new instance.
	 * 
	 * @param string $class
	 * The class to set the cloned instance with.
	 * 
	 * @return self
	 * A new clone from this instance.
	 */
	final public function clone(string $class): self
	{
		$clone = clone $this;
		$clone->class = $class;
		return $clone;
	}
}
