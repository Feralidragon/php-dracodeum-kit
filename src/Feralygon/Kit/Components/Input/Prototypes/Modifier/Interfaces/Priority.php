<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces;

/** This interface defines a method to get the priority from an input modifier prototype. */
interface Priority
{
	//Public methods
	/**
	 * Get priority.
	 * 
	 * The returning priority determines the order by which this modifier is applied.<br>
	 * Modifiers with the same priority are grouped together and are all executed, even if any one of them fails.
	 * 
	 * @return int
	 * <p>The priority.</p>
	 */
	public function getPriority(): int;
}
