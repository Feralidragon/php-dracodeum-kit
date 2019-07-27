<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Interfaces;

/**
 * This interface defines a method to get debug info from an object.
 * 
 * @see https://www.php.net/manual/en/language.oop5.magic.php#object.debuginfo
 */
interface DebugInfo
{
	//Public methods
	/**
	 * Get debug info.
	 * 
	 * @param bool $recursive [default = false]
	 * <p>Get debug info from all the possible referenced subobjects recursively.</p>
	 * @return array
	 * <p>The debug info.</p>
	 */
	public function getDebugInfo(bool $recursive = false): array;
}
