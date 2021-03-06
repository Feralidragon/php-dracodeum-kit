<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Traits;

use Dracodeum\Kit\Interfaces\DebugInfo as IDebugInfo;
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
	 * @return array
	 * <p>The debug info.</p>
	 */
	final public function __debugInfo(): array
	{
		return $this->getDebugInfo();
	}
	
	
	
	//Implemented final public methods (Dracodeum\Kit\Interfaces\DebugInfo)
	/** {@inheritdoc} */
	final public function getDebugInfo(bool $recursive = false): array
	{
		//process
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
		
		//recursive
		if ($recursive) {
			foreach ($debug_info as &$value) {
				if (is_object($value) && $value instanceof IDebugInfo) {
					$value = $value->getDebugInfo($recursive);
				}
			}
			unset($value);
		}
		
		//return
		return $debug_info;
	}
}
