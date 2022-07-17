<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Utilities\Type\Info\Enumerations;

use Dracodeum\Kit\Enumeration;

class Kind extends Enumeration
{
	//Public constants
	public const SIMPLE = 'SIMPLE';
	
	public const GENERIC = 'GENERIC';
	
	public const ARRAY = 'ARRAY';
	
	public const UNION = 'UNION';
	
	public const GROUP = 'GROUP';
}
