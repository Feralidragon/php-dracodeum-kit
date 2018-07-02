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
 * @since 1.0.0
 * @see \Feralygon\Kit\Factories\Options
 */
interface Text
{
	//Public methods
	/**
	 * Build instance.
	 * 
	 * @since 1.0.0
	 * @param array $properties [default = []]
	 * <p>The properties to build with, as <samp>name => value</samp> pairs.</p>
	 * @param bool $readonly [default = false]
	 * <p>Set the built instance as read-only.</p>
	 * @return \Feralygon\Kit\Options\Text
	 * <p>The built instance.</p>
	 */
	public function build(array $properties = [], bool $readonly = false): Options;
}
