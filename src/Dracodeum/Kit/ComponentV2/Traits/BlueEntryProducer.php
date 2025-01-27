<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\ComponentV2\Traits;

trait BlueEntryProducer
{
	//Protected methods
	/**
	 * Produce blue entry for a given name.
	 * 
	 * @param string $name
	 * The name to produce for.
	 * 
	 * @return \Dracodeum\Kit\ComponentV2\BlueEntry|string|null
	 * The produced blue entry for the given name, as an instance or a blueprint class, or `null` if none was produced.
	 */
	protected function produceBlueEntry(string $name)
	{
		return null;
	}
}
