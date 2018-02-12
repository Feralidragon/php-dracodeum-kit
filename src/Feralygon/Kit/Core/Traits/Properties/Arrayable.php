<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Traits\Properties;

use Feralygon\Kit\Core\Traits\Properties;

/**
 * Core properties arrayable trait.
 * 
 * This trait extends the properties trait and implements 
 * the <code>Feralygon\Kit\Core\Interfaces\Arrayable</code> interface.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Core\Traits\Properties
 * @see \Feralygon\Kit\Core\Interfaces\Arrayable
 */
trait Arrayable
{
	//Traits
	use Properties;
	
	
	
	//Implemented final public methods (core arrayable interface)
	/** {@inheritdoc} */
	final public function toArray() : array
	{
		return $this->getAll();
	}
}
