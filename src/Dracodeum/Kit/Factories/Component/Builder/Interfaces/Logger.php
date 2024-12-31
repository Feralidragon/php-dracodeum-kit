<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Factories\Component\Builder\Interfaces;

use Dracodeum\Kit\Components\Logger as Component;

/** This interface defines a method to build a logger instance. */
interface Logger
{
	//Public methods
	/**
	 * Build instance with a given prototype and set of properties.
	 * 
	 * @param \Dracodeum\Kit\Prototypes\Logger|string $prototype
	 * <p>The prototype instance, class or name to build with.</p>
	 * @param array $properties
	 * <p>The properties to build with, as a set of <samp>name => value</samp> pairs, 
	 * if a prototype class or name is given.<br>
	 * Required properties may also be given as an array of values (<samp>[value1, value2, ...]</samp>), 
	 * in the same order as how these properties were first declared.</p>
	 * @return \Dracodeum\Kit\Components\Logger
	 * <p>The built instance with the given prototype and set of properties.</p>
	 */
	public function build($prototype, array $properties): Component;
}
