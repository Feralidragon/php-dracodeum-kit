<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Factory\Builder\Interfaces;

/**
 * Factory builder named build interface.
 * 
 * This interface defines a method to build objects by name in a builder.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Factory\Builder
 */
interface NamedBuild
{
	//Public methods
	/**
	 * Build object by using a given name.
	 * 
	 * @since 1.0.0
	 * @param string $name <p>The name to use.</p>
	 * @param mixed ...$arguments <p>The arguments to build with.</p>
	 * @return object|null <p>The built object by using the given name or <code>null</code> if none was built.</p>
	 */
	public function buildByName(string $name, ...$arguments) : ?object;
}
