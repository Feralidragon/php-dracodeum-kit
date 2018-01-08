<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Prototypes\Input\Prototypes\Modifier\Interfaces;

/**
 * Core input modifier prototype specification data interface.
 * 
 * This interface defines a method to retrieve the specification data from an input modifier prototype.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Core\Prototypes\Input\Prototypes\Modifier
 */
interface SpecificationData
{
	//Public methods
	/**
	 * Get specification data.
	 * 
	 * The returning data is meant to characterize this modifier, such as, 
	 * for example, returning some of its properties as <samp>name => value</samp> pairs.
	 * 
	 * @since 1.0.0
	 * @return mixed <p>The specification data.</p>
	 */
	public function getSpecificationData();
}
