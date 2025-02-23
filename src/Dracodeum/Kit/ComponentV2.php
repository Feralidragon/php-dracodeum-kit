<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit;

use Dracodeum\Kit\Traits as KTraits;
use Dracodeum\Kit\ComponentV2\{
	Traits,
	BlueEntry
};
use Dracodeum\Kit\Attributes\Property\Ignore;
use Dracodeum\Kit\Exceptions\Argument\Invalid as InvalidArgument;
use Dracodeum\Kit\Exceptions\Value\Unexpected as UnexpectedValue;

/**
 * This class is the base to be extended from when creating a component.
 * 
 * A *component*, in the context of this kit, is a class that represents an object with a closed set of public 
 * properties and methods, with its inner implementation specified through a chosen *blueprint* class.   
 *   
 * When instantiating a *component*, a *blueprint* is given either by name or class, and never given as an instance, 
 * meaning that a *blueprint* class is never meant to be directly instantiated on its own outside of a *component*.  
 *   
 * The property values given during instantiation of a *component* are applied to both itself and the *blueprint*, 
 * with the corresponding *component* ones being first extracted and applied to the *component* instance itself, 
 * and with the remaining ones at the end being used to instantiate the *blueprint* with, providing the ability to pass 
 * "arguments" in the form of properties to a *blueprint*, in order to further customize its behavior.  
 *   
 * Both the *component* and *blueprint* classes implement extended properties.
 * 
 * @see \Dracodeum\Kit\Blueprint
 * @see \Dracodeum\Kit\ComponentV2\Traits\Booter
 * @see \Dracodeum\Kit\ComponentV2\Traits\Initializer
 * @see \Dracodeum\Kit\ComponentV2\Traits\BlueEntryProducer
 */
abstract class ComponentV2
{
	//Traits
	use KTraits\PropertiesV2;
	use Traits\Booter;
	use Traits\Initializer;
	use Traits\BlueEntryProducer;
	
	
	
	//Protected properties
	#[Ignore]
	protected readonly Blueprint $blueprint;
	
	
	
	//Private static properties
	/** @var array<string,<string,\Dracodeum\Kit\ComponentV2\BlueEntry>> */
	private static array $blue_entries = [];
	
	
	
	//Final public magic methods
	/**
	 * Instantiate class.
	 * 
	 * @param string $blueprint
	 * The blueprint name or class to instantiate with.
	 * 
	 * @param mixed $properties
	 * The properties to instantiate with.
	 * 
	 * @throws \Dracodeum\Kit\Exceptions\Argument\Invalid
	 * @throws \Dracodeum\Kit\Exceptions\Value\Unexpected
	 */
	final public function __construct(string $blueprint, ...$properties)
	{
		//initialize
		$b_base_class = $this->getBlueprintBaseClass();
		
		//properties
		$req_i = 0;
		$c_properties = [];
		foreach ($this->properties() as $name => $property) {
			//required
			if ($property->isRequired()) {
				if (array_key_exists($req_i, $properties)) {
					$c_properties[$req_i] = $properties[$req_i];
					unset($properties[$req_i]);
				}
				$req_i++;
			}
			
			//named
			if (array_key_exists($name, $properties)) {
				$c_properties[$name] = $properties[$name];
				unset($properties[$name]);
			}
		}
		$this->initializePropertiesManager($c_properties);
		
		//boot
		$this->executeBoot();
		
		//blue entry
		$blue_entry = $this->produceBlueEntry($blueprint);
		if ($blue_entry === null) {
			$blue_entry = $this->getBlueEntry($blueprint);
		} elseif (is_string($blue_entry) && is_a($blue_entry, $b_base_class, true)) {
			$blue_entry = $this->createBlueEntry($blue_entry);
		} elseif (!($blue_entry instanceof BlueEntry)) {
			throw new UnexpectedValue(
				$blue_entry, "{$this::class}::produceBlueEntry",
				error_message: "The returning blue entry must be an instance, or a subclass of \"{$b_base_class}\"."
			);
		}
		
		//blueprint (properties)
		$b_properties = [];
		foreach ($properties as $k => $v) {
			$b_properties[is_int($k) ? $k - $req_i : $k] = $v;
		}
		
		//blueprint
		if ($blue_entry !== null) {
			$this->blueprint = new $blue_entry->class($this, ...($blue_entry->properties + $b_properties));
		} elseif (is_a($blueprint, $b_base_class, true)) {
			$this->blueprint = new $blueprint($this, $b_properties);
		} else {
			throw new InvalidArgument(
				'blueprint', $blueprint, error_message: "The given blueprint must be a subclass of \"{$b_base_class}\"."
			);
		}
		
		//initialize (trait)
		$this->initialize();
	}
	
	
	
	//Abstract public static methods
	/**
	 * Get blueprint base class.
	 * 
	 * @return string
	 * The blueprint base class.
	 */
	abstract public static function getBlueprintBaseClass(): string;
	
	
	
	//Final public methods
	/**
	 * Get blueprint class.
	 * 
	 * @return string
	 * The blueprint class.
	 */
	final public function getBlueprintClass(): string
	{
		return $this->blueprint::class;
	}
	
	
	
	//Final public static methods
	/**
	 * Create a new instance.
	 * 
	 * @param string $blueprint
	 * The blueprint name or class to instantiate with.
	 * 
	 * @param mixed $properties
	 * The properties to instantiate with.
	 * 
	 * @return static
	 * The newly created instance.
	 */
	final public static function new(string $blueprint, ...$properties): static
	{
		return new static($blueprint, ...$properties);
	}
	
	/**
	 * Add blueprint.
	 * 
	 * @param string $name
	 * The name to add with.
	 * 
	 * @param string $class
	 * The class to add.
	 * 
	 * @param mixed $properties
	 * The properties to add with.
	 * 
	 * @throws \Dracodeum\Kit\Exceptions\Argument\Invalid
	 */
	final public static function addBlueprint(string $name, string $class, ...$properties): void
	{
		//name
		if (isset(self::$blue_entries[static::class][$name])) {
			throw new InvalidArgument(
				'name', $name, error_message: "The given blueprint name already exists in \"" . static::class . "\"."
			);
		}
		
		//class
		$base_class = static::getBlueprintBaseClass();
		if (!is_a($class, $base_class, true)) {
			throw new InvalidArgument(
				'class', $class, error_message: "The given blueprint class must be a subclass of \"{$base_class}\"."
			);
		}
		
		//boot
		static::executeBoot();
		
		//add
		self::$blue_entries[static::class][$name] = static::createBlueEntry($class, $properties);
	}
	
	
	
	//Final protected static methods
	/**
	 * Get blue entry instance with a given name.
	 * 
	 * @param string $name
	 * The name to get with.
	 * 
	 * @return \Dracodeum\Kit\ComponentV2\BlueEntry|null
	 * The blue entry instance with the given name, or `null` if none is set.
	 */
	final protected static function getBlueEntry(string $name): ?BlueEntry
	{
		for ($class = static::class; $class !== false; $class = get_parent_class($class)) {
			$blue_entry = self::$blue_entries[static::class][$name] ?? null;
			if ($blue_entry !== null) {
				return $blue_entry;
			}
		}
		return null;
	}
	
	/**
	 * Create a new blue entry instance.
	 * 
	 * @param string $class
	 * The class to create with.
	 * 
	 * @param array $properties
	 * The properties to create with.
	 * 
	 * @return \Dracodeum\Kit\ComponentV2\BlueEntry
	 * The newly created blue entry instance.
	 */
	final protected static function createBlueEntry(string $class, array $properties = []): BlueEntry
	{
		return new BlueEntry($class, $properties);
	}
	
	
	
	//Private static methods
	/** Execute boot. */
	private static function executeBoot(): void
	{
		//initialize
		static $booting = false;
		static $booted_map = [];
		if ($booting || isset($booted_map[static::class])) {
			return;
		}
		
		//boot
		try {
			//initialize
			$booting = true;
			
			//classes
			$classes = [];
			for ($class = static::class; $class !== false; $class = get_parent_class($class)) {
				if (!isset($booted_map[$class])) {
					$classes[] = $class;
				}
			}
			
			//boot
			foreach (array_reverse($classes) as $class) {
				$class::boot();
				$booted_map[$class] = true;
			}
			
		} finally {
			$booting = false;
		}
	}
}
