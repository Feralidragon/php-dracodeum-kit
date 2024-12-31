<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Traits\DebugInfo;

/**
 * This trait implements the <code>Dracodeum\Kit\Traits\DebugInfo\Interfaces\DebugInfoProcessor</code> interface 
 * when the debug info and the <code>Dracodeum\Kit\Traits\TReadonly</code> traits are used.
 * 
 * @see \Dracodeum\Kit\Traits\DebugInfo\Interfaces\DebugInfoProcessor
 * @see \Dracodeum\Kit\Traits\TReadonly
 */
trait ReadonlyProcessor
{
	//Implemented public methods (Dracodeum\Kit\Traits\DebugInfo\Interfaces\DebugInfoProcessor)
	/** {@inheritdoc} */
	public function processDebugInfo(Info $info): void
	{
		$this->processReadonlyDebugInfo($info);
	}
}
