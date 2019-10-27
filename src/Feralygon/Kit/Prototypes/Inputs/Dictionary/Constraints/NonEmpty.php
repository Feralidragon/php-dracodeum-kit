<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Prototypes\Inputs\Dictionary\Constraints;

use Feralygon\Kit\Components\Input\Prototypes\Modifiers\Constraint;
use Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\{
	Subtype as ISubtype,
	Information as IInformation
};
use Feralygon\Kit\Primitives\Dictionary as Primitive;
use Feralygon\Kit\Options\Text as TextOptions;
use Feralygon\Kit\Utilities\Text as UText;

/** This constraint prototype prevents a given dictionary input value from being empty. */
class NonEmpty extends Constraint implements ISubtype, IInformation
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getName(): string
	{
		return 'non_empty';
	}
	
	/** {@inheritdoc} */
	public function checkValue($value): bool
	{
		return is_object($value) && $value instanceof Primitive ? !$value->isEmpty() : false;
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\Subtype)
	/** {@inheritdoc} */
	public function getSubtype(): string
	{
		return 'dictionary';
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\Information)
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options): string
	{
		return UText::localize("Non-empty", self::class, $text_options);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options): string
	{
		return UText::localize("An empty dictionary is not allowed.", self::class, $text_options);
	}
}
