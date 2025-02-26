<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Traits\DebugInfo;

/**
 * This trait implements the <code>Dracodeum\Kit\Traits\DebugInfo\Interfaces\DebugInfoProcessor</code> interface, 
 * with the dump of object properties enabled, when the debug info and one of the following traits are used: 
 * <code>Dracodeum\Kit\Traits\Properties</code> or <code>Dracodeum\Kit\Traits\LazyProperties</code>.
 * 
 * @see \Dracodeum\Kit\Traits\DebugInfo\Interfaces\DebugInfoProcessor
 * @see \Dracodeum\Kit\Traits\Properties
 * @see \Dracodeum\Kit\Traits\LazyProperties
 */
trait PropertiesDumpProcessor
{
	//Implemented public methods (Dracodeum\Kit\Traits\DebugInfo\Interfaces\DebugInfoProcessor)
	/** {@inheritdoc} */
	public function processDebugInfo(Info $info): void
	{
		$this->processPropertiesDebugInfo($info);
		$info->enableObjectPropertiesDump();
	}
}
