<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Factories\Options\Builder\Interfaces;

use Feralygon\Kit\Options\Text as Options;

/**
 * This interface defines a method to build a text instance.
 * 
 * @see \Feralygon\Kit\Factories\Options
 */
interface Text
{
	//Public methods
	/**
	 * Build instance with a given set of properties.
	 * 
	 * @param array $properties
	 * <p>The properties to build with, as <samp>name => value</samp> pairs.</p>
	 * @return \Feralygon\Kit\Options\Text
	 * <p>The built instance with the given set of properties.</p>
	 */
	public function build(array $properties): Options;
}
