<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Prototypes\Input\Subcontracts;

use Dracodeum\Kit\Components\Input\Components\Modifiers\Constraint;

/**
 * This interface defines a subcontract as a method to create constraint instances, 
 * which may be implemented by any component set to use an input prototype.
 */
interface ConstraintCreator
{
	//Public methods
	/**
	 * Create a constraint instance with a given prototype.
	 * 
	 * @param \Dracodeum\Kit\Components\Input\Prototypes\Modifiers\Constraint|string $prototype
	 * <p>The prototype instance, class or name to create with.</p>
	 * @param array $properties [default = []]
	 * <p>The properties to create with, as a set of <samp>name => value</samp> pairs, 
	 * if a prototype class or name is given.<br>
	 * Required properties may also be given as an array of values (<samp>[value1, value2, ...]</samp>), 
	 * in the same order as how these properties were first declared.</p>
	 * @return \Dracodeum\Kit\Components\Input\Components\Modifiers\Constraint
	 * <p>The created constraint instance with the given prototype.</p>
	 */
	public function createConstraint($prototype, array $properties = []): Constraint;
}
