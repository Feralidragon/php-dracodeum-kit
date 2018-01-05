<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Root\System;

use Feralygon\Kit\Core\Traits\NoConstructor as TNoConstructor;

/**
 * Root system environment class.
 * 
 * This class is the base to be extended from when creating a system environment.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Root\System
 */
abstract class Environment
{
	//Traits
	use TNoConstructor;
	
	
	
	//Abstract public methods
	/**
	 * Get name.
	 * 
	 * The returning name defines an unique canonical identifier for this environment, 
	 * to be used in situations where it is required to identify the environment through such a name,
	 * such as to select which configuration files to use through the usage of this name in their filename.
	 * 
	 * @since 1.0.0
	 * @return string <p>The name.</p>
	 */
	abstract public function getName() : string;
	
	/**
	 * Check if is debug.
	 * 
	 * In a debug environment, the system behaves in such a way so that code can be easily modified and tested,
	 * at the potential cost of lower performance and a higher memory footprint.
	 * 
	 * @since 1.0.0
	 * @return bool <p>Boolean <samp>true</samp> if is debug.</p>
	 */
	abstract public function isDebug() : bool;
	
	/**
	 * Check if errors can be displayed.
	 * 
	 * If boolean <samp>true</samp> is returned, any unsuppressed errors will be displayed to the end-user,
	 * either in the console for a CLI application or in the browser for a web server application.<br>
	 * If the system is set to work as a library however, this method has no effect whatsoever.
	 * 
	 * @since 1.0.0
	 * @return bool <p>Boolean <samp>true</samp> if errors can be displayed.</p>
	 */
	abstract public function canDisplayErrors() : bool;
	
	/**
	 * Get error reporting bitwise flags.
	 * 
	 * The returning error flags define which kinds of errors are reported and written to the error log.<br>
	 * If the system is set to work as a library however, this method has no effect whatsoever.
	 * 
	 * @since 1.0.0
	 * @see https://php.net/manual/en/errorfunc.constants.php
	 * @return int <p>The error reporting bitwise flags.</p>
	 */
	abstract public function getErrorReportingFlags() : int;
}
