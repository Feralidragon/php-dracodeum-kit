<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Prototypes\Input\Interfaces;

/** This interface defines a method to get the schema data from an input prototype. */
interface SchemaData
{
	//Public methods
	/**
	 * Get schema data.
	 * 
	 * The returning data is meant to structurally describe this specific input in detail, 
	 * such as returning some of its properties as <samp>name => value</samp> pairs, for example.
	 * 
	 * @return mixed
	 * <p>The schema data.</p>
	 */
	public function getSchemaData();
}
