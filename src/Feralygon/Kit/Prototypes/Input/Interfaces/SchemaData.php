<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Prototypes\Input\Interfaces;

/**
 * Input prototype schema data interface.
 * 
 * This interface defines a method to retrieve the schema data from an input prototype.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Prototypes\Input
 */
interface SchemaData
{
	//Public methods
	/**
	 * Get schema data.
	 * 
	 * The returning data is meant to characterize this input, such as, 
	 * for example, returning some of its properties as <samp>name => value</samp> pairs.
	 * 
	 * @since 1.0.0
	 * @return mixed <p>The schema data.</p>
	 */
	public function getSchemaData();
}
