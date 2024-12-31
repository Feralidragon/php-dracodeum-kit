<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Utilities\Type\Info\Enums;

enum Kind
{
	/**
	 * A generic type with its name returned in `names[0]`, 
	 * and any additional types returned in `names` after index `0`, as `names[1]`, `names[2]`, and so on.
	 */
	case GENERIC;
	
	/** A non-associative array type with its content returned in `names[0]`. */
	case ARRAY;
	
	/** A group with its content returned in `names[0]`. */
	case GROUP;
	
	/** A union of types returned in `names`. */
	case UNION;
	
	/** An intersection of types returned in `names`. */
	case INTERSECTION;
}
