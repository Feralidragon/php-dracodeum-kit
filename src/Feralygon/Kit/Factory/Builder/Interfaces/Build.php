<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Factory\Builder\Interfaces;

/**
 * Factory builder build interface.
 * 
 * This interface defines a method to build objects in a builder.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Factory\Builder
 */
interface Build
{
	//Public methods
	/**
	 * Build object.
	 * 
	 * @since 1.0.0
	 * @param mixed ...$arguments <p>The arguments to build with.</p>
	 * @return object|null <p>The built object or <code>null</code> if none was built.</p>
	 */
	public function build(...$arguments) : ?object;
}
