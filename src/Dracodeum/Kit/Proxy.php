<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit;

use Dracodeum\Kit\Interfaces\{
	DebugInfo as IDebugInfo,
	Uncloneable as IUncloneable
};
use Dracodeum\Kit\Traits\DebugInfo\Interfaces\DebugInfoProcessor as IDebugInfoProcessor;
use Dracodeum\Kit\Proxy\Traits;
use Dracodeum\Kit\Traits as KitTraits;
use Dracodeum\Kit\Traits\DebugInfo\Info as DebugInfo;
use Dracodeum\Kit\Utilities\{
	Call as UCall,
	Type as UType
};

/**
 * This class is the base to be extended from when creating a proxy.
 * 
 * A proxy is responsible to process and forward any method calls to an owner instance.
 * 
 * @see \Dracodeum\Kit\Proxy\Traits\Initializer
 */
abstract class Proxy implements IDebugInfo, IDebugInfoProcessor, IUncloneable
{
	//Traits
	use KitTraits\DebugInfo;
	use KitTraits\Uncloneable;
	use Traits\Initializer;
	
	
	
	//Private properties
	/** @var object */
	private $owner;
	
	/** @var \Closure[] */
	private $methods = [];
	
	
	
	//Final public magic methods
	/**
	 * Instantiate class.
	 * 
	 * @param object $owner
	 * <p>The owner instance to instantiate with.</p>
	 */
	final public function __construct(object $owner)
	{
		//owner
		$owner_base_class = $this->getOwnerBaseClass();
		if (!UType::isA($owner, $owner_base_class)) {
			UCall::haltParameter('owner', $owner, [
				'error_message' => "Invalid owner class.",
				'hint_message' => "Only a class or subclass of {{base_class}} is allowed for this proxy.",
				'parameters' => ['base_class' => $owner_base_class]
			]);
		}
		$this->owner = $owner;
		
		//initialize
		$this->initialize();
	}
	
	
	
	//Abstract protected static methods
	/**
	 * Get owner base class.
	 * 
	 * Any given owner class or instance to be used by this proxy must be or 
	 * extend from the same class as the one returned here.
	 * 
	 * @return string
	 * <p>The owner base class.</p>
	 */
	abstract protected static function getOwnerBaseClass(): string;
	
	
	
	//Implemented public methods (Dracodeum\Kit\Traits\DebugInfo\Interfaces\DebugInfoProcessor)
	/** {@inheritdoc} */
	public function processDebugInfo(DebugInfo $info): void
	{
		$info
			->enableObjectPropertiesDump()
			->hideObjectProperty('owner', self::class)
			->hideObjectProperty('methods', self::class)
		;
	}
	
	
	
	//Final public static methods
	/**
	 * Build instance with a given owner instance.
	 * 
	 * @param object $owner
	 * <p>The owner instance to build with.</p>
	 * @return static
	 * <p>The built instance with the given owner instance.</p>
	 */
	final public static function build(object $owner): Proxy
	{
		return new static($owner);
	}
	
	
	
	//Final protected methods
	/**
	 * Get owner instance.
	 * 
	 * @return object
	 * <p>The owner instance.</p>
	 */
	final protected function getOwner(): object
	{
		return $this->owner;
	}
	
	/**
	 * Bind owner method with a given name.
	 * 
	 * @param string $name
	 * <p>The name to bind with.</p>
	 * @param string|null $class [default = null]
	 * <p>The class to bind with.<br>
	 * It should be set if the owner method with the given name is private.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final protected function bind(string $name, ?string $class = null): Proxy
	{
		//bind
		$this->methods[$name] = \Closure::bind(function (...$arguments) use ($name) {
			return $this->$name(...$arguments);
		}, $this->owner, $class ?? get_class($this->owner));
		
		//return
		return $this;
	}
	
	/**
	 * Call owner method with a given name.
	 * 
	 * This method may only be called if the owner method with the given name has been bound.
	 * 
	 * @param string $name
	 * <p>The name to call with.</p>
	 * @param mixed ...$arguments
	 * <p>The arguments to call with.</p>
	 * @return mixed
	 * <p>The returned value from the called owner method with the given name.</p>
	 */
	final protected function call(string $name, ...$arguments)
	{
		$method = $this->methods[$name] ?? null;
		if ($method === null) {
			UCall::haltParameter('name', $name, ['error_message' => "Owner method name not bound."]);
		}
		return $method(...$arguments);
	}
}
