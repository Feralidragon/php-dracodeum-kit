<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Prototypes;

use Feralygon\Kit\Prototype;
use Feralygon\Kit\Prototype\Interfaces\Functions as IFunctions;
use Feralygon\Kit\Components\Input\Components\Modifiers\{
	Constraint,
	Filter
};

/**
 * @since 1.0.0
 * @see \Feralygon\Kit\Components\Input
 * @see \Feralygon\Kit\Prototypes\Input\Interfaces\Information
 * @see \Feralygon\Kit\Prototypes\Input\Interfaces\ErrorUnset
 * @see \Feralygon\Kit\Prototypes\Input\Interfaces\ErrorInformation
 * @see \Feralygon\Kit\Prototypes\Input\Interfaces\ValueStringification
 * @see \Feralygon\Kit\Prototypes\Input\Interfaces\SchemaData
 * @see \Feralygon\Kit\Prototypes\Input\Interfaces\Modifiers
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
	 * @return bool <p>Boolean <code>true</code> if the given value is successfully evaluated.</p>
	 */
	abstract public function evaluateValue(&$value) : bool;
	
	
	
	//Implemented public methods (Feralygon\Kit\Prototype\Interfaces\Functions)
	/** {@inheritdoc} */
	public function getFunctionTemplate(string $name) : ?callable
	{
		switch ($name) {
			case 'createConstraint':
				return function ($prototype, array $properties) : Constraint {};
			case 'createFilter':
				return function ($prototype, array $properties) : Filter {};
		}
		return null;
	}
	
	
	
	//Protected methods
	/**
	 * Create a constraint instance with a given prototype.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Prototypes\Input\Prototypes\Modifiers\Constraint|string $prototype 
	 * <p>The constraint prototype instance, class or name to create with.</p>
	 * @param array $properties [default = []] <p>The constraint properties to use, 
	 * as <samp>name => value</samp> pairs.</p>
	 * @return \Feralygon\Kit\Components\Input\Components\Modifiers\Constraint 
	 * <p>The created constraint instance with the given prototype.</p>
	 */
	protected function createConstraint($prototype, array $properties = []) : Constraint
	{
		return $this->call('createConstraint', $prototype, $properties);
	}
	
	/**
	 * Create a filter instance with a given prototype.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Prototypes\Input\Prototypes\Modifiers\Filter|string $prototype 
	 * <p>The filter prototype instance, class or name to create with.</p>
	 * @param array $properties [default = []] <p>The filter properties to use, 
	 * as <samp>name => value</samp> pairs.</p>
	 * @return \Feralygon\Kit\Components\Input\Components\Modifiers\Filter 
	 * <p>The created filter instance with the given prototype.</p>
	 */
	protected function createFilter($prototype, array $properties = []) : Filter
	{
		return $this->call('createFilter', $prototype, $properties);
	}
}
