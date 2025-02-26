<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Prototypes\Types;

use Dracodeum\Kit\Prototypes\Type as Prototype;
use Dracodeum\Kit\Prototypes\Type\Interfaces\Textifier as ITextifier;
use Dracodeum\Kit\Primitives\{
	Error,
	Text
};

/**
 * This prototype represents an interface.
 * 
 * Only a string, as a full interface name, is allowed as an interface.
 */
class TInterface extends Prototype implements ITextifier
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function process(mixed &$value, $context, bool $strict): ?Error
	{
		//check
		if (is_string($value) && interface_exists($value)) {
			$value = $value[0] === '\\' ? substr($value, 1) : $value;
			return null;
		}
		
		//return
		return Error::build(
			text: $strict
				? "Only a string, as a full interface name, is strictly allowed."
				: "Only a string, as a full interface name, is allowed as an interface."
		);
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Prototypes\Type\Interfaces\Textifier)
	/** {@inheritdoc} */
	public function textify(mixed $value)
	{
		return Text::build("interface<{{name}}>")->setParameter('name', $value);
	}
}
