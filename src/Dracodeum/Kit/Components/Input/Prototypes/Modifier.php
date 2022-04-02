<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components\Input\Prototypes;

use Dracodeum\Kit\Prototype;

/**
 * @see \Dracodeum\Kit\Components\Input\Components\Modifier
 * @see \Dracodeum\Kit\Components\Input\Prototypes\Modifier\Interfaces\Subtype
 * @see \Dracodeum\Kit\Components\Input\Prototypes\Modifier\Interfaces\Priority
 * @see \Dracodeum\Kit\Components\Input\Prototypes\Modifier\Interfaces\Information
 * @see \Dracodeum\Kit\Components\Input\Prototypes\Modifier\Interfaces\Stringification
 * @see \Dracodeum\Kit\Components\Input\Prototypes\Modifier\Interfaces\ErrorUnsetter
 * @see \Dracodeum\Kit\Components\Input\Prototypes\Modifier\Interfaces\ErrorMessage
 * @see \Dracodeum\Kit\Components\Input\Prototypes\Modifier\Interfaces\SchemaData
 */
abstract class Modifier extends Prototype
{
	//Abstract public methods
	/**
	 * Get name.
	 * 
	 * The returning name must be a canonical string which identifies this modifier within an input.
	 * 
	 * @return string
	 * <p>The name.</p>
	 */
	abstract public function getName(): string;
}
