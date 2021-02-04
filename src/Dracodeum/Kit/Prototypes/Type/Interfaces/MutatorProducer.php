<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Prototypes\Type\Interfaces;

/** This interface defines a method to produce mutators. */
interface MutatorProducer
{
	//Public methods
	/**
	 * Produce mutator.
	 * 
	 * @param string $name
	 * <p>The name to produce for.</p>
	 * @param array $properties
	 * <p>The properties to produce with, as a set of <samp>name => value</samp> pairs.<br>
	 * Required properties may also be given as an array of values (<samp>[value1, value2, ...]</samp>), 
	 * in the same order as how these properties were first declared.</p>
	 * @return coercible:component<\Dracodeum\Kit\Components\Type\Components\Mutator>|null
	 * <p>The produced mutator or <code>null</code> if none was produced.</p>
	 */
	public function produceMutator(string $name, array $properties);
}
