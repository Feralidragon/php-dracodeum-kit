<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Traits\DebugInfo;

/**
 * This trait implements the <code>Feralygon\Kit\Traits\DebugInfo\Interfaces\DebugInfoProcessor</code> interface 
 * when the debug info and the <code>Feralygon\Kit\Traits\Readonly</code> traits are used.
 * 
 * @see \Feralygon\Kit\Traits\DebugInfo\Interfaces\DebugInfoProcessor
 * @see \Feralygon\Kit\Traits\DebugInfo
 * @see \Feralygon\Kit\Traits\Readonly
 */
trait ReadonlyProcessor
{
	//Implemented public methods (Feralygon\Kit\Traits\DebugInfo\Interfaces\DebugInfoProcessor)
	/** {@inheritdoc} */
	public function processDebugInfo(Info $info): void
	{
		$this->processReadonlyDebugInfo($info);
	}
}
