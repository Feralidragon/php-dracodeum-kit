<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Component;

use Feralygon\Kit\Interfaces\Uncloneable as IUncloneable;
use Feralygon\Kit\{
	Component,
	Traits
};
use Feralygon\Kit\Utilities\{
	Call as UCall,
	Type as UType
};

/**
 * This class is the base to be extended from when creating a proxy.
 * 
 * A proxy is responsible for implementing the prototype contract interface or any subcontract interfaces or both, 
 * and forward any method calls to the component, instead of being directly implemented by the component itself.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Component
 */
abstract class Proxy implements IUncloneable
{
	//Traits
	use Traits\NoConstructor;
	use Traits\Uncloneable;
	
	
	
	//Private properties
	/** @var \Feralygon\Kit\Component|null */
	private $component = null;
	
	
	
	//Abstract public static methods
	/**
	 * Get base component class.
	 * 
	 * Any component instance given to be used by this proxy must be or 
	 * extend from the same class as the base component class returned here.
	 * 
	 * @since 1.0.0
	 * @return string
	 * <p>The base component class.</p>
	 */
	abstract public static function getBaseComponentClass(): string;
	
	
	
	//Final public methods
	/**
	 * Set component instance.
	 * 
	 * This method may only be called if no component instance has already been set.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Component $component
	 * <p>The component instance to set.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setComponent(Component $component): Proxy
	{
		//guard
		UCall::guard(!isset($this->component), [
			'hint_message' => "This method may only be called if no component instance has already been set."
		]);
		
		//component base class
		$component_base_class = $this->getBaseComponentClass();
		UCall::guardInternal(UType::isA($component_base_class, Component::class), [
			'error_message' => "Invalid base component class {{base_class}}.",
			'parameters' => ['base_class' => $component_base_class]
		]);
		
		//guard component
		UCall::guardParameter('component', $component, UType::isA($component, $component_base_class), [
			'error_message' => "Invalid component class.",
			'hint_message' => "Only a class or subclass of {{base_class}} is allowed for this proxy.",
			'parameters' => ['base_class' => $component_base_class]
		]);
		
		//set
		$this->component = $component;
		
		//return
		return $this;
	}
	
	
	
	//Final protected methods
	/**
	 * Get component instance.
	 * 
	 * This method may only be called if a component instance has already been set.
	 * 
	 * @since 1.0.0
	 * @return \Feralygon\Kit\Component
	 * <p>The component instance.</p>
	 */
	final protected function getComponent(): Component
	{
		UCall::guard(isset($this->component), [
			'hint_message' => "This method may only be called if a component instance has already been set."
		]);
		return $this->component;
	}
}
