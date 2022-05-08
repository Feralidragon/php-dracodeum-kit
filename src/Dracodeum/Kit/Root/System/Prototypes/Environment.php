<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Root\System\Prototypes;

use Dracodeum\Kit\Prototype;

/** @see \Dracodeum\Kit\Root\System\Components\Environment */
abstract class Environment extends Prototype
{
	//Abstract public methods
	/**
	 * Get name.
	 * 
	 * The returning name defines a unique canonical identifier for this environment, 
	 * to be used to select which configuration profile to use.
	 * 
	 * @return string
	 * <p>The name.</p>
	 */
	abstract public function getName(): string;
	
	/**
	 * Check if is a debug environment.
	 * 
	 * In a debug environment, the system behaves in such a way so that code can be easily debugged, 
	 * by performing additional integrity checks during runtime (assertions), 
	 * at the potential cost of lower performance and a higher memory footprint.
	 * 
	 * @return bool
	 * <p>Boolean <code>true</code> if is a debug environment.</p>
	 */
	abstract public function isDebug(): bool;
	
	/**
	 * Get dump verbosity level.
	 * 
	 * @see \Dracodeum\Kit\Root\System\Enumerations\DumpVerbosityLevel
	 * @return int
	 * <p>The dump verbosity level.</p>
	 */
	abstract public function getDumpVerbosityLevel(): int;
	
	/** Apply.  */
	abstract public function apply(): void;
}
