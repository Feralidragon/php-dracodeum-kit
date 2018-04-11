<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Prototypes\Input\Subcontracts;

use Feralygon\Kit\Components\Input\Components\Modifiers\Constraint;

/**
 * This interface defines a subcontract as a method to create constraint instances, 
 * which may be implemented by any component set to use an input prototype.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Prototypes\Input
 */
interface Constraints
{
	//Public methods
	/**
	 * Create a constraint instance with a given prototype.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Components\Input\Prototypes\Modifiers\Constraint|string $prototype
	 * <p>The prototype instance, class or name to create with.</p>
	 * @param array $properties [default = []]
	 * <p>The properties to create with, as <samp>name => value</samp> pairs.</p>
	 * @return \Feralygon\Kit\Components\Input\Components\Modifiers\Constraint
	 * <p>The created constraint instance with the given prototype.</p>
	 */
	public function createConstraint($prototype, array $properties = []) : Constraint;
}
