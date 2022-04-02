<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Prototypes\Type\Interfaces;

interface MutatorProducer
{
	//Public methods
	/**
	 * Produce mutator.
	 * 
	 * @param string $name
	 * The name to produce for.
	 * 
	 * @param array $properties
	 * The properties to produce with, as a set of `name => value` pairs.  
	 * Required properties may also be given as an array of values (`[value1, value2, ...]`), 
	 * in the same order as how these properties were first declared.
	 * 
	 * @return coercible:component<\Dracodeum\Kit\Components\Type\Components\Mutator>|null
	 * The produced mutator, or `null` if none was produced.
	 */
	public function produceMutator(string $name, array $properties);
}
