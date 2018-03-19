<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Prototypes\Input;

use Feralygon\Kit\Components\Input\Components\Modifiers\{
	Constraint,
	Filter
};

/**
 * This interface defines a contract as a set of methods to be implemented by a component to use an input prototype.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Prototypes\Input
 */
interface Contract
{
	//Public methods
	/**
	 * Create a constraint instance with a given prototype.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Prototypes\Input\Prototypes\Modifiers\Constraint|string $prototype
	 * <p>The constraint prototype instance, class or name to create with.</p>
	 * @param array $properties [default = []]
	 * <p>The constraint properties to use, as <samp>name => value</samp> pairs.</p>
	 * @return \Feralygon\Kit\Components\Input\Components\Modifiers\Constraint
	 * <p>The created constraint instance with the given prototype.</p>
	 */
	public function createConstraint($prototype, array $properties = []) : Constraint;
	
	/**
	 * Create a filter instance with a given prototype.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Prototypes\Input\Prototypes\Modifiers\Filter|string $prototype
	 * <p>The filter prototype instance, class or name to create with.</p>
	 * @param array $properties [default = []]
	 * <p>The filter properties to use, as <samp>name => value</samp> pairs.</p>
	 * @return \Feralygon\Kit\Components\Input\Components\Modifiers\Filter
	 * <p>The created filter instance with the given prototype.</p>
	 */
	public function createFilter($prototype, array $properties = []) : Filter;
}
