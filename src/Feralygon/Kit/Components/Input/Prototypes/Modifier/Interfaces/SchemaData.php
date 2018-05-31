<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces;

/**
 * This interface defines a method to get the schema data from an input modifier prototype.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Components\Input\Prototypes\Modifier
 */
interface SchemaData
{
	//Public methods
	/**
	 * Get schema data.
	 * 
	 * The returning data is meant to structurally describe this specific modifier in detail, 
	 * such as returning some of its properties as <samp>name => value</samp> pairs, for example.
	 * 
	 * @since 1.0.0
	 * @return mixed
	 * <p>The schema data.</p>
	 */
	public function getSchemaData();
}
