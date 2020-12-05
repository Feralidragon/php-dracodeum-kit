<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Prototypes\Inputs\Dictionary\Constraints;

use Dracodeum\Kit\Components\Input\Prototypes\Modifiers\Constraint;
use Dracodeum\Kit\Components\Input\Prototypes\Modifier\Interfaces\{
	Subtype as ISubtype,
	Information as IInformation
};
use Dracodeum\Kit\Primitives\Dictionary as Primitive;
use Dracodeum\Kit\Options\Text as TextOptions;
use Dracodeum\Kit\Utilities\{
	Data as UData,
	Text as UText
};

/** This constraint prototype restricts a given dictionary input value to unique values. */
class Unique extends Constraint implements ISubtype, IInformation
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getName(): string
	{
		return 'unique';
	}
	
	/** {@inheritdoc} */
	public function checkValue($value): bool
	{
		if ($value instanceof Primitive) {
			$map = [];
			foreach ($value as $v) {
				$key = UData::keyfy($v);
				if (isset($map[$key])) {
					return false;
				}
				$map[$key] = true;
			}
			return true;
		}
		return false;
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Components\Input\Prototypes\Modifier\Interfaces\Subtype)
	/** {@inheritdoc} */
	public function getSubtype(): string
	{
		return 'dictionary';
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Components\Input\Prototypes\Modifier\Interfaces\Information)
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options): string
	{
		return UText::localize("Unique", self::class, $text_options);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options): string
	{
		return UText::localize("Only unique values are allowed.", self::class, $text_options);
	}
}
