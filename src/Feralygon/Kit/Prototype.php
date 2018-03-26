<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit;

use Feralygon\Kit\Prototype\{
	Exceptions,
	Interfaces
};
use Feralygon\Kit\Utilities\{
	Call as UCall,
	Type as UType
};

/**
 * This class is the base to be extended from when creating a prototype.
 * 
 * For more information, please check the <code>Feralygon\Kit\Component</code> class.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Component
 * @see \Feralygon\Kit\Prototype\Interfaces\Contract
 * @see \Feralygon\Kit\Prototype\Interfaces\Properties
 * @see \Feralygon\Kit\Prototype\Interfaces\Initialization
 */
abstract class Prototype
{
	//Traits
	use Traits\LazyProperties;
	
	
	
	//Private properties
	/** @var \Feralygon\Kit\Component|null */
	private $component = null;
	
	
	
	//Final public magic methods
	/**
	 * Instantiate class.
	 *
	 * @since 1.0.0
	 * @param array $properties [default = []]
	 * <p>The properties, as <samp>name => value</samp> pairs.</p>
	 * @throws \Feralygon\Kit\Prototype\Exceptions\PropertiesNotImplemented
	 */
	final public function __construct(array $properties = [])
	{
		//properties
		if ($this instanceof Interfaces\Properties) {
			$this->initializeProperties([$this, 'buildProperty'], $properties, $this->getRequiredPropertyNames());
		} elseif (!empty($properties)) {
			throw new Exceptions\PropertiesNotImplemented(['prototype' => $this]);
		}
		
		//initialization
		if ($this instanceof Interfaces\Initialization) {
			$this->initialize();
		}
	}
	
	
	
	//Final public methods
	/**
	 * Set component instance.
	 * 
	 * This method may only be called if no component instance has already been set.<br>
	 * If a contract interface is defined, the given component instance must also implement that interface.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Component $component
	 * <p>The component instance to set.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setComponent(Component $component) : Prototype
	{
		//guard
		UCall::guard(!isset($this->component), [
			'hint_message' => "This method may only be called if no component instance has already been set."
		]);
		
		//contract
		$contract = $this instanceof Interfaces\Contract ? $this->getContract() : null;
		UCall::guard(!isset($contract) || UType::implements($component, $contract), [
			'hint_message' => "The given component {{component}} must implement the contract {{contract}}.",
			'parameters' => ['component' => $component, 'contract' => $contract]
		]);
		
		//set
		$this->component = $component;
		
		//return
		return $this;
	}
	
	
	
	//Final protected methods
	/**
	 * Call contract method with a given name.
	 * 
	 * This method may only be called if a contract interface is defined and a component instance has already been set.
	 * 
	 * @since 1.0.0
	 * @param string $name
	 * <p>The contract method name to call.</p>
	 * @param mixed ...$arguments
	 * <p>The contract method arguments to call with.</p>
	 * @return mixed
	 * <p>The returned value from the called contract method with the given name.</p>
	 */
	final protected function contractCall(string $name, ...$arguments)
	{
		//guard
		UCall::guard($this instanceof Interfaces\Contract && isset($this->component), [
			'hint_message' => "This method may only be called if a contract interface is defined " . 
				"and a component instance has already been set."
		]);
		
		//contract
		$contract = UType::interface($this->getContract());
		UCall::guardParameter('name', $name, method_exists($contract, $name), [
			'hint_message' => "The given method name has not been found in contract {{contract}} " . 
				"implemented by component {{component}}.",
			'parameters' => ['contract' => $contract, 'component' => $this->component]
		]);
		
		//return
		return $this->component->$name(...$arguments);
	}
}
