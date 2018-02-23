<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Factory;

use Feralygon\Kit\Traits;

/**
 * Factory builder class.
 * 
 * This class is the base to be extended from when creating a factory builder.<br>
 * For more information, please check the <code>Feralygon\Kit\Factory</code> class.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Factory
 */
abstract class Builder
{
	//Traits
	use Traits\NoConstructor;
	
	
	
	//Abstract public methods
	/**
	 * Build object for a given name.
	 * 
	 * @since 1.0.0
	 * @param string $name <p>The name to build for.</p>
	 * @param mixed ...$arguments <p>The arguments to build with.</p>
	 * @return object|null <p>The built object for the given name or <code>null</code> if none was built.</p>
	 */
	abstract public function build(string $name, ...$arguments) : ?object;
}
