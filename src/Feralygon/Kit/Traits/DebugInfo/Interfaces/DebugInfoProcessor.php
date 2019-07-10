<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Traits\DebugInfo\Interfaces;

use Feralygon\Kit\Traits\DebugInfo\Info;

/**
 * This interface defines a method to process a debug info instance in an object 
 * using the <code>Feralygon\Kit\Traits\DebugInfo</code> trait.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Traits\DebugInfo
 */
interface DebugInfoProcessor
{
	//Public methods
	/**
	 * Process a given debug info instance.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Traits\DebugInfo\Info $info
	 * <p>The debug info instance to process.</p>
	 * @return void
	 */
	public function processDebugInfo(Info $info): void;
}
