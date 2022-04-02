<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Traits;

use Dracodeum\Kit\Traits\DebugInfo\{
	Info,
	Interfaces
};
use Dracodeum\Kit\Root\System;
use Dracodeum\Kit\Root\System\Enumerations\DumpVerbosityLevel as EDumpVerbosityLevel;

/**
 * This trait enables debug info support for a class 
 * and may be used as an implementation of the <code>Dracodeum\Kit\Interfaces\DebugInfo</code> interface.
 * 
 * @see https://www.php.net/manual/en/language.oop5.magic.php#object.debuginfo
 * @see \Dracodeum\Kit\Interfaces\DebugInfo
 * @see \Dracodeum\Kit\Traits\DebugInfo\Interfaces\DebugInfoProcessor
 * @see \Dracodeum\Kit\Traits\DebugInfo\PropertiesProcessor
 * @see \Dracodeum\Kit\Traits\DebugInfo\PropertiesDumpProcessor
 * @see \Dracodeum\Kit\Traits\DebugInfo\ReadonlyProcessor
 * @see \Dracodeum\Kit\Traits\DebugInfo\ReadonlyDumpProcessor
 * @see \Dracodeum\Kit\Traits\DebugInfo\ReadonlyPropertiesProcessor
 * @see \Dracodeum\Kit\Traits\DebugInfo\ReadonlyPropertiesDumpProcessor
 */
trait DebugInfo
{
	//Final public magic methods
	/**
	 * Get debug info.
	 * 
	 * @return array|null
	 * <p>The debug info or <code>null</code> if none is set.</p>
	 */
	final public function __debugInfo(): ?array
	{
		return $this->getDebugInfo();
	}
	
	
	
	//Implemented final public methods (Dracodeum\Kit\Interfaces\DebugInfo)
	/** {@inheritdoc} */
	final public function getDebugInfo(): array
	{
		$debug_info = [];
		if (System::getDumpVerbosityLevel() < EDumpVerbosityLevel::HIGH) {
			//info
			$info = new Info();
			if ($this instanceof Interfaces\DebugInfoProcessor) {
				$this->processDebugInfo($info);
			}
			
			//debug info
			$debug_info = $info->getAll();
			if ($info->isObjectPropertiesDumpEnabled()) {
				foreach ((array)$this as $name => $value) {
					//initialize
					$pname = $name;
					$class = null;
					if (preg_match('/^\0(?P<class>(?:\*|[\w\\\\]+))\0(?P<name>\w+)$/', $name, $matches)) {
						$pname = $matches['name'];
						if ($matches['class'] !== '*') {
							$class = $matches['class'];
						}
					}
					
					//set
					if (!$info->isObjectPropertyHidden($pname, $class)) {
						$debug_info[$name] = $value;
					}
				}
			}
			
		} else {
			$debug_info = (array)$this;
		}
		return $debug_info;
	}
}
