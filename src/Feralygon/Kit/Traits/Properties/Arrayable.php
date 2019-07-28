<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Traits\Properties;

use Feralygon\Kit\Utilities\Data as UData;

/**
 * This trait implements the PHP <code>Feralygon\Kit\Interfaces\Arrayable</code> interface 
 * when the properties trait is used.
 * 
 * @see \Feralygon\Kit\Interfaces\Arrayable
 * @see \Feralygon\Kit\Traits\Properties
 */
trait Arrayable
{
	//Implemented final public methods (Feralygon\Kit\Interfaces\Arrayable)
	/** {@inheritdoc} */
	final public function toArray(bool $recursive = false): array
	{
		$array = $this->getAll();
		if ($recursive) {
			foreach ($array as &$value) {
				if (is_object($value)) {
					UData::evaluate($value, null, false, false, true);
				}
			}
			unset($value);
		}
		return $array;
	}
}
