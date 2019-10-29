<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Traits\Properties;

use Dracodeum\Kit\Utilities\Data as UData;

/**
 * This trait implements the <code>Dracodeum\Kit\Interfaces\Arrayable</code> interface 
 * when the properties trait is used.
 * 
 * @see \Dracodeum\Kit\Interfaces\Arrayable
 */
trait Arrayable
{
	//Implemented final public methods (Dracodeum\Kit\Interfaces\Arrayable)
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
