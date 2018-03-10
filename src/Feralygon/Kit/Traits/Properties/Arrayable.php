<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Traits\Properties;

use Feralygon\Kit\Traits\Properties;

/**
 * This trait extends the properties trait and implements 
 * the <code>Feralygon\Kit\Interfaces\Arrayable</code> interface.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Traits\Properties
 * @see \Feralygon\Kit\Interfaces\Arrayable
 */
trait Arrayable
{
	//Traits
	use Properties;
	
	
	
	//Implemented final public methods (arrayable interface)
	/** {@inheritdoc} */
	final public function toArray() : array
	{
		return $this->getAll();
	}
}
