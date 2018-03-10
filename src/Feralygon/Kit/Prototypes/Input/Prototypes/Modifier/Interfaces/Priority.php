<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Prototypes\Input\Prototypes\Modifier\Interfaces;

/**
 * This interface defines a method to retrieve the priority from an input modifier prototype.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Prototypes\Input\Prototypes\Modifier
 */
interface Priority
{
	//Public methods
	/**
	 * Get priority.
	 * 
	 * The returning priority determines the order by which this modifier is applied.<br>
	 * Modifiers with the same priority are grouped together and are all executed, even if any one of them fails.
	 * 
	 * @since 1.0.0
	 * @return int <p>The priority.</p>
	 */
	public function getPriority() : int;
}
