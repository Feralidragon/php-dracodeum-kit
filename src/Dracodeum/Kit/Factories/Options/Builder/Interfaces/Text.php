<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Factories\Options\Builder\Interfaces;

use Dracodeum\Kit\Options\Text as Options;

/** This interface defines a method to build a text instance. */
interface Text
{
	//Public methods
	/**
	 * Build instance with a given set of properties.
	 * 
	 * @param array $properties
	 * <p>The properties to build with, as <samp>name => value</samp> pairs.</p>
	 * @return \Dracodeum\Kit\Options\Text
	 * <p>The built instance with the given set of properties.</p>
	 */
	public function build(array $properties): Options;
}
