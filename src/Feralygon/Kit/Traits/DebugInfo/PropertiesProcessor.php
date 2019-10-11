<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Traits\DebugInfo;

/**
 * This trait implements the <code>Feralygon\Kit\Traits\DebugInfo\Interfaces\DebugInfoProcessor</code> interface 
 * when the debug info and one of the following traits are used: <code>Feralygon\Kit\Traits\Properties</code> or 
 * <code>Feralygon\Kit\Traits\LazyProperties</code>.
 * 
 * @see \Feralygon\Kit\Traits\DebugInfo\Interfaces\DebugInfoProcessor
 * @see \Feralygon\Kit\Traits\Properties
 * @see \Feralygon\Kit\Traits\LazyProperties
 */
trait PropertiesProcessor
{
	//Implemented public methods (Feralygon\Kit\Traits\DebugInfo\Interfaces\DebugInfoProcessor)
	/** {@inheritdoc} */
	public function processDebugInfo(Info $info): void
	{
		$this->processPropertiesDebugInfo($info);
	}
}
