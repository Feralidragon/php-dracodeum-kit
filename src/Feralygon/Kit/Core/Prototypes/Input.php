<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Prototypes;

use Feralygon\Kit\Core\Prototype;
use Feralygon\Kit\Core\Prototype\Interfaces\Functions as IFunctions;
use Feralygon\Kit\Core\Components\Input\Components\Modifiers\{
	Constraint,
	Filter
};

/**
 * Core input prototype class.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Core\Components\Input
 * @see \Feralygon\Kit\Core\Prototypes\Input\Interfaces\Error
 * @see \Feralygon\Kit\Core\Prototypes\Input\Interfaces\Information
 * @see \Feralygon\Kit\Core\Prototypes\Input\Interfaces\ErrorInformation
 * @see \Feralygon\Kit\Core\Prototypes\Input\Interfaces\ValueStringification
 * @see \Feralygon\Kit\Core\Prototypes\Input\Interfaces\SchemaData
 * @see \Feralygon\Kit\Core\Prototypes\Input\Interfaces\Modifiers
 */
abstract class Input extends Prototype implements IFunctions
{
	//Abstract public methods
	/**
	 * Get name.
	 * 
	 * The returning name must be a canonical string, which uniquely identifies this input.
	 * 
	 * @since 1.0.0
	 * @return string <p>The name.</p>
	 */
	abstract public function getName() : string;
	
	/**
	 * Evaluate a given value.
	 * 
	 * @since 1.0.0
	 * @param mixed $value [reference] <p>The value to evaluate (validate and sanitize).</p>
	 * @return bool <p>Boolean <code>true</code> if the given value is valid.</p>
	 */
	abstract public function evaluateValue(&$value) : bool;
	
	
	
	//Implemented public methods (core prototype functions interface)
	/** {@inheritdoc} */
	public function getFunctionTemplate(string $name) : ?callable
	{
		switch ($name) {
			case 'createConstraint':
				return function ($prototype, array $prototype_properties, array $properties) : Constraint {};
			case 'createFilter':
				return function ($prototype, array $prototype_properties, array $properties) : Filter {};
		}
		return null;
	}
	
	
	
	//Protected methods
	/**
	 * Create a constraint instance.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Core\Prototypes\Input\Prototypes\Modifiers\Constraint|string $prototype <p>The constraint prototype instance, class or name.</p>
	 * @param array $prototype_properties [default = []] <p>The constraint prototype properties, as <samp>name => value</samp> pairs.</p>
	 * @param array $properties [default = []] <p>The constraint properties, as <samp>name => value</samp> pairs.</p>
	 * @return \Feralygon\Kit\Core\Components\Input\Components\Modifiers\Constraint <p>The created constraint instance.</p>
	 */
	protected function createConstraint($prototype, array $prototype_properties = [], array $properties = []) : Constraint
	{
		return $this->call('createConstraint', $prototype, $prototype_properties, $properties);
	}
	
	/**
	 * Create a filter instance.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Core\Prototypes\Input\Prototypes\Modifiers\Filter|string $prototype <p>The filter prototype instance, class or name.</p>
	 * @param array $prototype_properties [default = []] <p>The filter prototype properties, as <samp>name => value</samp> pairs.</p>
	 * @param array $properties [default = []] <p>The filter properties, as <samp>name => value</samp> pairs.</p>
	 * @return \Feralygon\Kit\Core\Components\Input\Components\Modifiers\Filter <p>The created filter instance.</p>
	 */
	protected function createFilter($prototype, array $prototype_properties = [], array $properties = []) : Filter
	{
		return $this->call('createFilter', $prototype, $prototype_properties, $properties);
	}
}
