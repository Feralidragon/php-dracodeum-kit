<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit;

use Dracodeum\Kit\Interfaces\{
	DebugInfo as IDebugInfo,
	Properties as IProperties,
	Uncloneable as IUncloneable
};
use Dracodeum\Kit\Traits\DebugInfo\Interfaces\DebugInfoProcessor as IDebugInfoProcessor;
use Dracodeum\Kit\Prototype\{
	Interfaces,
	Traits
};
use Dracodeum\Kit\Traits as KitTraits;
use Dracodeum\Kit\Utilities\{
	Call as UCall,
	Type as UType
};

/**
 * This class is the base to be extended from when creating a prototype.
 * 
 * For more information, please check the <code>Dracodeum\Kit\Component</code> class.
 * 
 * @see \Dracodeum\Kit\Component
 * @see \Dracodeum\Kit\Prototype\Interfaces\Contract
 * @see \Dracodeum\Kit\Prototype\Interfaces\Subcontracts
 * @see \Dracodeum\Kit\Prototype\Traits\RequiredPropertyNamesLoader
 * @see \Dracodeum\Kit\Prototype\Traits\PropertyBuilder
 * @see \Dracodeum\Kit\Prototype\Traits\Initializer
 */
abstract class Prototype implements IDebugInfo, IDebugInfoProcessor, IProperties, IUncloneable
{
	//Traits
	use KitTraits\DebugInfo;
	use KitTraits\DebugInfo\PropertiesDumpProcessor;
	use KitTraits\LazyProperties;
	use KitTraits\Uncloneable;
	use Traits\RequiredPropertyNamesLoader;
	use Traits\PropertyBuilder;
	use Traits\Initializer;
	
	
	
	//Private properties
	/** @var \Dracodeum\Kit\Component|null */
	private $component = null;
	
	
	
	//Final public magic methods
	/**
	 * Instantiate class.
	 * 
	 * @param array $properties [default = []]
	 * <p>The properties to instantiate with, as <samp>name => value</samp> pairs.<br>
	 * Required properties may also be given as an array of values (<samp>[value1, value2, ...]</samp>), 
	 * in the same order as how these properties were first declared.</p>
	 */
	final public function __construct(array $properties = [])
	{
		//properties
		$this->initializeProperties(
			\Closure::fromCallable([$this, 'buildProperty']), $properties,
			\Closure::fromCallable([$this, 'loadRequiredPropertyNames'])
		);
		
		//initialization
		$this->initialize();
	}
	
	
	
	//Final public methods
	/**
	 * Set component instance.
	 * 
	 * This method may only be called if no component instance has already been set.<br>
	 * If a contract interface is defined, then the given component instance must also implement that interface.
	 * 
	 * @param \Dracodeum\Kit\Component $component
	 * <p>The component instance to set.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setComponent(Component $component): Prototype
	{
		//guard
		UCall::guard(!isset($this->component), [
			'hint_message' => "This method may only be called if no component instance has already been set."
		]);
		
		//contract
		$contract = $this instanceof Interfaces\Contract ? $this->getContract() : null;
		UCall::guard(
			!isset($contract) || UType::implements($component, $contract) || 
			($component->hasProxy() && UType::implements($component->getProxy(), $contract)), [
				'error_message' => "The given component {{component}} must implement the contract {{contract}}.",
				'parameters' => ['component' => $component, 'contract' => $contract]
			]
		);
		
		//set
		$this->component = $component;
		
		//return
		return $this;
	}
	
	
	
	//Final protected methods
	/**
	 * Call method with a given name using a contract.
	 * 
	 * This method may only be called if a contract interface is defined and a component instance has already been set.
	 * 
	 * @param string $method_name
	 * <p>The method name to call.</p>
	 * @param mixed ...$arguments
	 * <p>The arguments to call with.</p>
	 * @return mixed
	 * <p>The returned value from the called method with the given name using the contract.</p>
	 */
	final protected function contractCall(string $method_name, ...$arguments)
	{
		//guard
		UCall::guard($this instanceof Interfaces\Contract && isset($this->component), [
			'hint_message' => "This method may only be called if a contract interface is defined " . 
				"and a component instance has already been set."
		]);
		
		//contract
		$contract = UType::interface($this->getContract());
		UCall::guardParameter('method_name', $method_name, method_exists($contract, $method_name), [
			'error_message' => "Method name not found in contract {{contract}} implemented by component {{component}}.",
			'parameters' => ['contract' => $contract, 'component' => $this->component]
		]);
		
		//proxy
		$proxy = $this->component->getProxy(true);
		if (isset($proxy) && UType::implements($proxy, $contract)) {
			return $proxy->$method_name(...$arguments);	
		}
		
		//return
		return $this->component->$method_name(...$arguments);
	}
	
	/**
	 * Call method with a given name using a subcontract for a given name.
	 * 
	 * This method may only be called if any subcontract interface is defined 
	 * and a component instance has already been set.
	 * 
	 * @param string $name
	 * <p>The subcontract name to use.</p>
	 * @param string $method_name
	 * <p>The method name to call.</p>
	 * @param callable $fallback
	 * <p>The fallback function to call if the component does not implement the subcontract for the given name.<br>
	 * It is expected to be compatible with the signature from the corresponding method with the given name.</p>
	 * @param mixed ...$arguments
	 * <p>The arguments to call with.</p>
	 * @return mixed
	 * <p>The returned value from the called method with the given name using the subcontract for the given name.</p>
	 */
	final protected function subcontractCall(string $name, string $method_name, callable $fallback, ...$arguments)
	{
		//guard
		UCall::guard($this instanceof Interfaces\Subcontracts && isset($this->component), [
			'hint_message' => "This method may only be called if any subcontract interface is defined " . 
				"and a component instance has already been set."
		]);
		
		//subcontract
		$subcontract = $this->getSubcontract($name);
		UCall::guardParameter('name', $name, isset($subcontract), [
			'error_message' => "No subcontract found for the given name."
		]);
		$subcontract = UType::interface($subcontract);
		UCall::guardParameter('method_name', $method_name, method_exists($subcontract, $method_name), [
			'error_message' => "Method name not found in subcontract {{subcontract}}.",
			'parameters' => ['subcontract' => $subcontract]
		]);
		
		//proxy
		$proxy = $this->component->getProxy(true);
		if (isset($proxy) && UType::implements($proxy, $subcontract)) {
			return $proxy->$method_name(...$arguments);	
		}
		
		//return
		if (UType::implements($this->component, $subcontract)) {
			return $this->component->$method_name(...$arguments);
		}
		UCall::assert('fallback', $fallback, [$subcontract, $method_name]);
		return $fallback(...$arguments);
	}
}
