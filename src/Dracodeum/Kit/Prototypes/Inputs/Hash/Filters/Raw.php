<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Prototypes\Inputs\Hash\Filters;

use Dracodeum\Kit\Components\Input\Prototypes\Modifiers\Filter;
use Dracodeum\Kit\Components\Input\Prototypes\Modifier\Interfaces\Subtype as ISubtype;

/** This filter prototype converts a given hash input value in hexadecimal notation into a raw binary string. */
class Raw extends Filter implements ISubtype
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getName(): string
	{
		return 'raw';
	}
	
	/** {@inheritdoc} */
	public function processValue(&$value): bool
	{
		if (is_string($value)) {
			$value = hex2bin($value);
			return $value !== false;
		}
		return false;
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Components\Input\Prototypes\Modifier\Interfaces\Subtype)
	/** {@inheritdoc} */
	public function getSubtype(): string
	{
		return 'hash';
	}
}
