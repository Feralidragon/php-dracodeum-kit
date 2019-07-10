<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Traits;

use Feralygon\Kit\Root\System;
use Feralygon\Kit\Root\System\Enumerations\DumpVerbosityLevel as EDumpVerbosityLevel;

/**
 * This trait enables debug info support for a class 
 * and may be used as an implementation of the <code>Feralygon\Kit\Interfaces\DebugInfo</code> interface.
 * 
 * @since 1.0.0
 * @see https://www.php.net/manual/en/language.oop5.magic.php#object.debuginfo
 * @see \Feralygon\Kit\Interfaces\DebugInfo
 * @see \Feralygon\Kit\Traits\DebugInfo\Interfaces\DebugInfoProcessor
 */
trait DebugInfo
{
	//Final public magic methods
	/**
	 * Get debug info.
	 * 
	 * @since 1.0.0
	 * @return array
	 * <p>The debug info.</p>
	 */
	final public function __debugInfo(): array
	{
		return $this->getDebugInfo();
	}
	
	
	
	//Implemented final public methods (Feralygon\Kit\Interfaces\DebugInfo)
	/** {@inheritdoc} */
	final public function getDebugInfo(bool $recursive = false): array
	{
		
		//TODO
		
		/**
		 * Public members: member_name
		 * Protected memebers: \0*\0member_name
		 * Private members: \0Class_name\0member_name
		 */
		
	}
}
